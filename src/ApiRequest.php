<?php

namespace Deepstreamhub;

/**
 * A class representing a single API request
 *
 * @author deepstreamHub GmbH <info@deepstreamhub.com>
 * @copyright (c) 2017, deepstreamHub GmbH
 */
class ApiRequest {
    private $requestData;
    private $url;

    /**
     * Creates the request
     *
     * @param string $url
     * @param mixed $authData
     */
    public function __construct( $url, $authData ) {
        $this->url = $url;
        $this->requestData = $authData;
        $this->requestData['body'] = array();
    }

    /**
     * Adds an aditional step to the request
     *
     * @param array $request
     *
     * @private
     * @returns void
     */
    public function add( $request ) {
        array_push( $this->requestData['body'], $request );
    }

    /**
     * Executes the HTTP request and parses the result
     *
     * @private
     * @return mixed result data
     */
    public function execute() {
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/json\r\n",
                'method'  => 'POST',
                'content' => json_encode( $this->requestData, JSON_UNESCAPED_SLASHES )
            )
        );

        $context  = stream_context_create($options);
        $result = file_get_contents($this->url, false, $context);

        if( $result === false ) {
            return false;
        } else {
            return json_decode( $result );
        }
    }
}
