<?php
namespace Arnm\MediaBundle\Tests\Services\Storage;

use Aws\Common\Aws;
use Arnm\MediaBundle\Service\Storage\S3Storage;
use Aws\S3\S3Client;
/**
 * S3Storage test case.
 */
class S3StorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Aws
     */
    private $aws;

    /**
     * Bucket name for testing
     *
     * @var unknown
     */
    private $bucket = 'indago-test';
    /**
     *
     * @var S3Storage
     */
    private $s3Storage;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        $config = array(
                'region' => 'us-west-2',
                'key' => 'AKIAJPDXQ7LOXT476FYQ',
                'secret' => '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'
        );

        $this->aws = Aws::factory($config);
        $this->s3Storage = new S3Storage($this->aws, $this->bucket);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->s3Storage = null;

        parent::tearDown();
    }


    /**
     * Tests S3Storage->__construct()
     */
    public function test__construct()
    {
        $this->assertTrue($this->s3Storage instanceof S3Storage);

        $client = $this->s3Storage->getS3Client();
        $this->assertTrue($client instanceof S3Client);

        //some initial check to allow the rest of the test to run
        $this->assertTrue($client->doesBucketExist($this->bucket));
        //let's clear everything before the rest of the tests.
        $this->s3Storage->getS3Client()->clearBucket($this->bucket);
    }

    /**
     * tests objectExists function
     *
     * @depends test__construct
     */
    public function testObjectExists(){
        $this->s3Storage->setBucket($this->bucket);

        $this->assertFalse($this->s3Storage->objectExists('file.txt'));
    }

    /**
     * tests saveObject method
     *
     * @depends testObjectExists
     */
    public function testSaveObject() {
        $this->s3Storage->setBucket($this->bucket);
        $key = 'folder/test.txt';
        $result = $this->s3Storage->saveObject($key, realpath(dirname(__FILE__).'/test.txt'));

        $this->assertTrue($result instanceof \Guzzle\Service\Resource\Model);
        $this->assertTrue($this->s3Storage->objectExists($key));
    }

    /**
     * tests getObject method
     *
     * @depends testSaveObject
     */
    public function testGetObject() {
        $this->s3Storage->setBucket($this->bucket);
        $key = 'folder/test.txt';
        $original = realpath(dirname(__FILE__).'/test.txt');
        $target = realpath(dirname(__FILE__)).'/test_tmp.txt';
        $result = $this->s3Storage->getObject($key, $target);

        $this->assertTrue($result instanceof \Guzzle\Service\Resource\Model);
        $this->assertTrue(file_exists($target));
        $this->assertEquals($target, $result['Body']->getUri());
        $this->assertEquals(md5_file($original), md5_file($target));

        unset($target);
    }

    /**
     * tests getObjectUrl Method
     *
     * @depends testObjectExists
     */
    public function testGetObjectUrl()
    {
        $key = 'folder/test.txt';
        $http = new \Guzzle\Http\Client;

        // Try to get the plain URL. This should result in a 403 since the object is private
        try {
            $response = $http->get($this->s3Storage->getObjectUrl($key, null))->send();
        } catch (\Guzzle\Http\Exception\ClientErrorResponseException $e) {
            $response = $e->getResponse();
        }

        $this->assertEquals('403', $response->getStatusCode());

        $response = $http->get($this->s3Storage->getObjectUrl($key, '+1 minute'))->send();
        $this->assertEquals('200', $response->getStatusCode());
    }

    /**
     * tests deleteObject method
     *
     * @depends testObjectExists
     */
    public function testDeleteObject() {
        $this->s3Storage->setBucket($this->bucket);
        $key = 'folder/test.txt';
        $result = $this->s3Storage->deleteObject($key);

        $this->assertTrue($result instanceof \Guzzle\Service\Resource\Model);
        $this->assertFalse($this->s3Storage->objectExists($key));
    }

}

