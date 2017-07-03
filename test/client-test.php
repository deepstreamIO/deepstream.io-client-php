<?php
use PHPUnit\Framework\TestCase;
use Deepstreamhub\DeepstreamClient;

define( 'API_URL', 'http://localhost:8000/api/v1' );
define( 'AUTH_DATA', array( 'token' => 'fiwueeb-3942jjh3jh23i4h23i4h2' ) );

final class ClientTest extends TestCase
{
//    public function testPresence()
//    {
//        $client = new DeepstreamClient( API_URL, AUTH_DATA );
//        $result = $client->getPresence();
//        var_dump($result);
//        $this->assertEquals( $result, false );
//    }

    public function testRecordDoesNotExist()
    {
        $client = new DeepstreamClient( API_URL, AUTH_DATA );
        $result = $client->getRecord( 'user/wolfram' );
        $this->assertEquals( $result, false );
    }

    public function testWritesFullRecord()
    {
        $client = new DeepstreamClient( API_URL, AUTH_DATA );
        $result = $client->setRecord( 'user/wolfram', array( 'lastname'=>'Hempel') );
        $this->assertEquals( $result, true );
    }

    public function testReadsRecord()
    {
        $client = new DeepstreamClient( API_URL, AUTH_DATA );
        $result = $client->getRecord( 'user/wolfram' );
        $this->assertEquals( $result->lastname, 'Hempel' );
    }

    public function testGetsRecordVersion()
    {
        $client = new DeepstreamClient( API_URL, AUTH_DATA );
        $result = $client->getRecordVersion( 'user/wolfram' );
        $this->assertEquals( $result, 1 );
    }

    public function testWritesRecordPath()
    {
        $client = new DeepstreamClient( API_URL, AUTH_DATA );
        $setResult = $client->setRecord( 'user/wolfram', 'age', 32 );
        $this->assertEquals( $setResult, true );
        $getResult = $client->getRecord('user/wolfram');
        $this->assertEquals( $getResult->lastname, 'Hempel');
        $this->assertEquals( $getResult->age, 32 );
    }

    public function testGetsRecordVersionAfterPatch()
    {
        $client = new DeepstreamClient( API_URL, AUTH_DATA );
        $result = $client->getRecordVersion( 'user/wolfram' );
        $this->assertEquals( $result, 2 );
    }

    public function testDeletesRecord()
    {
        $client = new DeepstreamClient( API_URL, AUTH_DATA );
        $resultDelete = $client->deleteRecord( 'user/wolfram' );
        $this->assertEquals( $resultDelete, true );
        $resultGet = $client->getRecord( 'user/wolfram' );
        $this->assertEquals( $resultGet, false );
    }

    public function testMakesRpc()
    {
        $client = new DeepstreamClient( API_URL, AUTH_DATA );
        $response = $client->makeRpc( 'times-two', 7 );
        $this->assertEquals( $response, 14 );
    }

    public function testResetsTestProvider()
    {
        $client = new DeepstreamClient( API_URL, AUTH_DATA );
        $response = $client->makeRpc( 'reset-test-provider' );
        $this->assertEquals( $response, 'OK' );
    }

    public function testGetsEmptyEventData()
    {
        $client = new DeepstreamClient( API_URL, AUTH_DATA );
        $response = $client->makeRpc( 'get-event-info' );
        $this->assertEquals( count( $response ), 0 );
    }

    public function testEmitsEvent()
    {
        $client = new DeepstreamClient( API_URL, AUTH_DATA );
        $responseEmit = $client->emitEvent( 'test-event', 'some-data' );
        $this->assertEquals( $responseEmit, true );
        $response = $client->makeRpc( 'get-event-info' );
        $this->assertEquals( count( $response ), 1 );
        $this->assertEquals( $response[ 0 ]->name, 'test-event' );
        $this->assertEquals( $response[ 0 ]->data, 'some-data' );
    }

    public function testRunsBatchRequest()
    {
        $client = new DeepstreamClient( API_URL, AUTH_DATA );
        $client->startBatch();
        $this->assertEquals( $client->makeRpc( 'times-two', 11 ), true );
        $this->assertEquals( $client->makeRpc( 'times-two', 45 ), true );
        $response = $client->executeBatch();
        $this->assertEquals( $response->result, 'SUCCESS' );
        $this->assertEquals( count($response->body), 2 );
        $this->assertEquals( $response->body[0]->data, 22 );
        $this->assertEquals( $response->body[1]->data, 90 );
    }
}