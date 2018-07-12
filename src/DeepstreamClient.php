<?php
namespace Deepstreamhub;

use \Deepstreamhub\Exceptions\NoBatchInstanceException;

/**
 * The deepstream PHP client running against the dsh/dsx
 * HTTP API
 *
 * @author deepstreamHub GmbH <info@deepstreamhub.com>
 * @copyright (c) 2017, deepstreamHub GmbH
 */
class DeepstreamClient
{
    const SUCCESS_RESPONSE = 'SUCCESS';

    const ACTION_DELETE = 'delete';
    const ACTION_WRITE = 'write';
    const ACTION_MAKE = 'make';
    const ACTION_EMIT = 'emit';
    const ACTION_READ = 'read';
    
    const TOPIC_TYPE_RECORD = 'record';
    const TOPIC_TYPE_RPC = 'rpc';
    const TOPIC_TYPE_EVENT = 'event';
    
    private $url;
    private $batchApiRequest = null;

    /**
     * Constructs the client
     *
     * @param string $url HTTP(S) URL for a deepstream endpoint
     * @param mixed $authData any authentication information
     *
     * @public
     * @return void
     */
    public function __construct($url, $authData)
    {
        $this->url = $url;
        $this->authData = $authData;
    }

    /**
     * Initiates a set of batch operations. No actual request
     * will be sent until executeBatch is called
     *
     * @public
     * @return void
     */
    public function startBatch()
    {
        $this->batchApiRequest = new ApiRequest($this->url, $this->authData);
    }

    /**
     * Executes a set of batch operations
     *
     * @public
     * @return Object result
     */
    public function executeBatch()
    {
        if($this->hasBatch() === false) {
            throw new NoBatchInstanceException;
        }

        return $this->batchApiRequest->execute();
    }

    /**
     * Retrieves data for a single record
     *
     * @param string $recordName
     *
     * @public
     * @return mixed response data
     */
    public function getRecord($recordName)
    {
        $apiRequest = $this->getApiRequest();
        $apiRequest->add([
            'topic' => self::TOPIC_TYPE_RECORD,
            'action' => self::ACTION_READ,
            'recordName' => $recordName
        ]);

        if($this->hasBatch()) {
            return true;
        }

        $result = $apiRequest->execute();
        
        if($this->isSuccessful($result->result)) {
            return $result->body[0]->data;
        }

        return false;
    }

    /**
     * Updates a records data. Can be called with a path
     * for partial updates
     *
     * @param string recordName
     * @param string path optional path
     * @param mixed data
     *
     * @return boolean
     */
    public function setRecord()
    {
        $apiRequest = $this->getApiRequest();
        $requestData = [
            'topic' => self::TOPIC_TYPE_RECORD,
            'action' => self::ACTION_WRITE,
            'recordName' => func_get_arg(0),
        ];

        if( func_num_args() === 2) {
            $requestData['data'] = func_get_arg(1);
        }
        else if(func_num_args() === 3) {
            $requestData['path'] = func_get_arg(1);
            $requestData['data'] = func_get_arg(2);
        }

        $apiRequest->add($requestData);
        if( $this->hasBatch() ) {
            return true;
        }
        return $this->isSuccessful($apiRequest->execute()->result);
    }

    /**
     * Executes a Remote Procedure Call
     *
     * @param string rpcName
     * @param mixed data optional
     *
     * @public
     * @return mixed response data
     */
    public function makeRpc()
    {
        $apiRequest = $this->getApiRequest();
        $requestData = [
            'topic' => self::TOPIC_TYPE_RPC,
            'action' => self::ACTION_MAKE,
            'rpcName' => func_get_arg(0)
        ];

        $requestData['data'] = null;

        if(func_num_args() === 2) {
            $requestData['data'] = func_get_arg(1);
        }

        $apiRequest->add($requestData);

        if($this->hasBatch()) {
            return true;
        }

        $response = $apiRequest->execute();

        if($this->isSuccessful($response->result)) {
            return $response->body[0]->data;
        }
        
        return false;
    }

    /**
     * Emits a deepstream event
     *
     * @param string eventName
     * @param mixed data optional
     *
     * @public
     * @return boolean success
     */
    public function emitEvent()
    {
        $apiRequest = $this->getApiRequest();
        $requestData = [
            'topic' => self::TOPIC_TYPE_EVENT,
            'action' => self::ACTION_EMIT,
            'eventName' => func_get_arg(0)
        ];
        
        $requestData['data'] = null;
        if(func_num_args() === 2) {
            $requestData['data'] = func_get_arg(1);
        }

        $apiRequest->add($requestData);
      
        if($this->hasBatch()) {
            return true;
        }
      
        $response = $apiRequest->execute();

        return $this->isSuccessful($response->result);
    }

    /**
     * Returns the current version for a record
     *
     * @param type $recordName
     *
     * @public
     * @return mixed the version of the record
     */
    public function getRecordVersion($recordName)
    {
        $apiRequest = $this->getApiRequest();
        $apiRequest->add([
            'topic' => self::TOPIC_TYPE_RECORD,
            'action' => self::ACTION_READ,
            'recordName' => $recordName
        ]);

        if($this->hasBatch()) {
            return true;
        }

        return $apiRequest->execute()->body[0]->version;
    }

    /**
     * Deletes a record
     *
     * @param type $recordName
     *
     * @public
     * @return boolean
     */
    public function deleteRecord($recordName)
    {
        $apiRequest = $this->getApiRequest();
        $apiRequest->add([
            'topic' => self::TOPIC_TYPE_RECORD,
            'action' => self::ACTION_DELETE,
            'recordName' => $recordName
        ]);

        if($this->hasBatch()) {
            return true;
        }

        return $this->isSuccessful($apiRequest->execute()->result);
    }

    /**
     * Check if a batch operation is in progress
     *
     * @return boolean
     */
    private function hasBatch()
    {
        return $this->batchApiRequest !== null;
    }

    /**
     * Returns a new API request or an existing one if a batch is in progress
     *
     * @return \ApiRequest
     */
    private function getApiRequest()
    {
        if($this->hasBatch()) {
            return $this->batchApiRequest;
        }

        return new ApiRequest($this->url, $this->authData);
    }

    /**
     * Check is response successfuly
     * 
     * @return boolean
     */
    private function isSuccessful($result)
    {
        return $result === self::SUCCESS_RESPONSE;
    }

    /**
     * Returns Batch Api Request object
     * 
     * @return \ApiRequest
     */
    public function getBatchApiRequest()
    {
        return $this->batchApiRequest;
    }
}