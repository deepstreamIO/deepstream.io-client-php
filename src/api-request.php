<?php


class ApiRequest {
    private $requestData;
    private $url;
    
    public function __construct($url) {
        $this->url = $url;
        $this->requestData = array(
            'token' => 'fiwueeb-3942jjh3jh23i4h23i4h2',
            'body' => array()
        );
    }
    
    public function add( $request ) {
        array_push( $this->requestData['body'], $request );
    }
    
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
        
        if($result === false ) {
            return false;
        } else {
            return json_decode( $result );
        }
    }
}
