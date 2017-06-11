<?php
use PHPUnit\Framework\TestCase;


define( 'API_URL', 'http://localhost:8000/api/v1' );

/**
 * @covers Email
 */
final class ClientTest extends TestCase
{
    public function testRecordDoesNotExist()
    {
        $client = new DeepstreamClient( API_URL );
        $result = $client->getRecord( 'user/wolfram' );
        $this->assertEquals( $result, false );
    }
    
    public function testWritesFullRecord()
    {
        $client = new DeepstreamClient( API_URL );
        $result = $client->setRecord( 'user/wolfram', array( 'lastname'=>'Hempel') );
        $this->assertEquals( $result, true );
    }
    
    public function testReadsRecord()
    {
        $client = new DeepstreamClient( API_URL );
        $result = $client->getRecord( 'user/wolfram' );
        $this->assertEquals( $result->lastname, 'Hempel' );
    }
    
    public function testWritesRecordPath()
    {
        $client = new DeepstreamClient( API_URL );
        $setResult = $client->setRecord( 'user/wolfram', 'age', 32 );
        $this->assertEquals( $setResult, true );
        $getResult = $client->getRecord('user/wolfram');
        $this->assertEquals( $getResult->lastname, 'Hempel');
        $this->assertEquals( $getResult->age, 32 );
    }
    
    public function testDeletesRecord()
    {
        $client = new DeepstreamClient( API_URL );
        $resultDelete = $client->deleteRecord( 'user/wolfram' );
        $this->assertEquals( $resultDelete, true );
        $resultGet = $client->getRecord( 'user/wolfram' );
        $this->assertEquals( $resultGet, false );
    }
}