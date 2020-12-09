<?php

namespace Deepstreamhub;
use Deepstreamhub\Exceptions\NoDeepStreamClientException;

/**
 * A class representing a single API request
 *
 * @author deepstreamHub GmbH <info@deepstreamhub.com>
 * @copyright (c) 2017, deepstreamHub GmbH
 */
class ApiRequest
{
    private $requestData;
    private $url;

    const ONE_MB_IN_BYTES = 1048576;
    const RESULT_SUCCESS = 'SUCCESS';
    /**
     * Creates the request
     *
     * @param string $url
     * @param mixed $authData
     */
    public function __construct($url, $authData)
    {
        $this->url = $url;
        $this->requestData = $authData;
        $this->requestData['body'] = [];
    }

    /**
     * Adds an aditional step to the request
     *
     * @param array $request
     *
     * @private
     * @returns void
     */
    public function add($request)
    {
        array_push($this->requestData['body'], $request);
    }

    /**
     * Executes the HTTP request and parses the result
     *
     * @private
     * @return mixed result data
     */
    public function execute()
    {   
        $results = $this->sendRequestInChunks();

        $this->requestData['body'] = [];

        if($results === false) {
            throw new NoDeepStreamClientException;
        }

        return $results;
    }

    private function sendRequestInChunks() {
        $requestBody = $this->requestData;
        $requestBody['body'] = [];
        $results = (object) ['body' => [], 'result' => self::RESULT_SUCCESS];

        foreach($this->requestData['body'] as $requestPart) {
            if($this->isNextRequestSmallerThanOneMB($requestBody, $requestPart)) {
                array_push($requestBody['body'], $requestPart);
            } else {
                $this->appendToResults($results, $this->sendRequest($requestBody));
                $requestBody['body'] = [];
            } 
        }
        if(sizeof($requestBody['body']) > 0) $this->appendToResults($results, $this->sendRequest($requestBody));
        return $results;
    }

    private function isNextRequestSmallerThanOneMB($requestBody, $requestPart) {
        return mb_strlen(serialize((array)$requestBody), '8bit') + mb_strlen(serialize((object)$requestPart), '8bit') < self::ONE_MB_IN_BYTES;
    }

    private function sendRequest($requestBody) {
        $options = [
            'http' => [
                'header'  => "Content-type: application/json\r\n",
                'method'  => 'POST',
                'content' => json_encode($requestBody, JSON_UNESCAPED_SLASHES),
                'keep-alive' => 'timeout=5, max=1000'
            ]
        ];

        $context  = stream_context_create($options);
        return file_get_contents($this->url, false, $context);
    }

    private function appendToResults($results, $result) {
        $resultToAppend = json_decode($result);
        if($resultToAppend->result !== self::RESULT_SUCCESS)
            $results->result = $resultToAppend->result;

        $results->body = array_merge($results->body, $resultToAppend->body);
    }
    /**
     * 
     * 
     * @return boolean
     */
    public function hasAnyData()
    {
        return !empty($this->requestData['body']);
    }
}
