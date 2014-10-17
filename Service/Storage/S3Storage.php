<?php
namespace Arnm\MediaBundle\Service\Storage;

use Arnm\MediaBundle\Service\Storage\MediaStorageInterface;
use Aws\S3\S3Client;
use Aws\Common\Aws;
use Guzzle\Service\Resource\Model;

/**
 * This is S# specific storage service
 *
 * @author Alex Agulyansky <alex@iibspro.com>
 */
class S3Storage implements MediaStorageInterface
{

    /**
     *
     * @var S3Client
     */
    private $s3Client;

    /**
     * Name of a bucket to work with
     *
     * @var string
     */
    private $bucket;

    /**
     * Constructor
     *
     * @param Aws $aws
     */
    public function __construct(Aws $aws)
    {
        $this->setS3Client($aws->get('S3'));
    }

    /**
     *
     * @return S3Client
     */
    public function getS3Client()
    {
        return $this->s3Client;
    }

    /**
     *
     * @param \Aws\S3\S3Client $s3Client
     */
    public function setS3Client(S3Client $s3Client)
    {
        $this->s3Client = $s3Client;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Arnm\MediaBundle\Service\Storage\MediaStorageInterface::objectExists()
     */
    public function objectExists($key)
    {
        return $this->getS3Client()->doesObjectExist($this->getBucket(), $key);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Arnm\MediaBundle\Service\Storage\MediaStorageInterface::saveObject()
     */
    public function saveObject($key, $file)
    {
        $return = $this->getS3Client()->putObject(array(
            'Bucket' => $this->getBucket(),
            'Key' => $key,
            'SourceFile' => $file
        ));

        // $this->getS3Client()->waitUntil('ObjectExists', array(
        // 'Bucket' => $this->getBucket(),
        // 'Key' => $key
        // ));

        return $return;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Arnm\MediaBundle\Service\Storage\MediaStorageInterface::getObject()
     */
    public function getObject($key, $targetFile)
    {
        return $this->getS3Client()->getObject(array(
            'Bucket' => $this->getBucket(),
            'Key' => ((string) $key),
            'SaveAs' => ((string) $targetFile)
        ));
    }

    /**
     * Gets object's presigned URL
     *
     * @param string $key
     * @param string $expiration
     *
     * @return string
     */
    public function getObjectUrl($key, $expiration = '+10 minutes')
    {
        $signedUrl = $this->getS3Client()->getObjectUrl($this->getBucket(), $key, $expiration);

        return $signedUrl;
    }

    /**
     * Deletes object from the storage
     *
     * @param string $key
     */
    public function deleteObject($key)
    {
        $response = $this->getS3Client()->deleteObject(array(
            'Bucket' => $this->getBucket(),
            'Key' => ((string) $key)
        ));

        return $response;
    }

    /**
     * Gets bucket name
     *
     * @return string
     */
    public function getBucket()
    {
        return $this->bucket;
    }

    /**
     * Sets bucket name
     *
     * @param string $bucket
     */
    public function setBucket($bucket)
    {
        $this->bucket = (string) $bucket;
    }
}