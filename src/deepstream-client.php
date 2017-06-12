<?php

include 'api-request.php';

define( 'SUCCESS_RESPONSE', 'SUCCESS' );

class DeepstreamClient {

    private $url;
    private $isBatch = false;

    public function __construct( $url ) {
        $this->url = $url;
    }
    
    public function getRecord( $recordName ) {
        $apiRequest = new ApiRequest( $this->url );
        $apiRequest->add(array(
                'topic' => 'record',
                'action' => 'read',
                'recordName' => $recordName
        ));
        $result = $apiRequest->execute();
        if( $result->result === SUCCESS_RESPONSE ) {
            return $result->body[0]->data;
        } else {
            return false;
        }
    }
    
    public function setRecord() {
        $apiRequest = new ApiRequest( $this->url );
        $requestData = array(
            'topic' => 'record',
            'action' => 'write',
            'recordName' => func_get_arg( 0 ),
        );
        
        if( func_num_args() === 2 ) {
            $requestData['data'] = func_get_arg( 1 );
        }
        else if(func_num_args() === 3 ) {
            $requestData['path'] = func_get_arg( 1 );
            $requestData['data'] = func_get_arg( 2 );
        }
        
        $apiRequest->add($requestData);
        return ($apiRequest->execute()->result ===SUCCESS_RESPONSE );
    }
    
    public function makeRpc() {
        $apiRequest = new ApiRequest( $this->url );
        $requestData = array(
            'topic' => 'rpc',
            'action' => 'make',
            'rpcName' => func_get_arg( 0 )
        );
        
        if(func_num_args() === 2 ) {
            $requestData['data'] = func_get_arg(1);
        } else {
            $requestData['data'] = null;
        }
        
        $apiRequest->add($requestData);
        $response = $apiRequest->execute();

        if( $response->result === SUCCESS_RESPONSE ) {
            return $response->body[0]->data;
        } else {
            return false;
        }
    }
    
    public function emitEvent() {
        $apiRequest = new ApiRequest( $this->url );
        $requestData = array(
            'topic' => 'event',
            'action' => 'emit',
            'eventName' => func_get_arg( 0 )
        );
        
        if(func_num_args() === 2 ) {
            $requestData['data'] = func_get_arg(1);
        } else {
            $requestData['data'] = null;
        }
        
        $apiRequest->add($requestData);
        $response = $apiRequest->execute();

        return ( $response->result === SUCCESS_RESPONSE );
    }

        
    public function getRecordVersion( $recordName ) {
        $apiRequest = new ApiRequest( $this->url );
        $apiRequest->add(array(
                'topic' => 'record',
                'action' => 'read',
                'recordName' => $recordName
        ));
        return $apiRequest->execute()->body[0]->version;
    }
    
    public function deleteRecord( $recordName ) {
        $apiRequest = new ApiRequest( $this->url );
        $apiRequest->add(array(
                'topic' => 'record',
                'action' => 'delete',
                'recordName' => $recordName
        ));
        return ($apiRequest->execute()->result === SUCCESS_RESPONSE );
    }
    // TODO
//    public function getPresence() {
//        $apiRequest = new ApiRequest( $this->url );
//        $apiRequest->add(array(
//                'topic' => 'presence',
//                'action' => 'query'
//        ));
//        return ($apiRequest->execute() );
//    }
}