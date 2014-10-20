<?php
namespace Arnm\MediaBundle\Service\Storage;

/**
 * This interface defines the behaviour of any media storage
 *
 * @author Alex Agulyansky <alex@iibspro.com>
 */
interface MediaStorageInterface
{

    /**
     * Determines if requested object exists
     *
     * @param string $key
     *
     * @return boolean
     */
    public function objectExists($key);

    /**
     * Saves the object/file
     *
     * @param string $key
     * @param string $file
     * @param string $contentType
     * @param array  $metadata
     *
     * @return Model
     */
    public function saveObject($key, $file, $contentType = null, array $metadata = array());

    /**
     * Saves requested object into a target file
     *
     * @param string $key
     * @param string $targetFile
     *
     * @return Model
     */
    public function getObject($key, $targetFile);

    /**
     * Gets object's presigned URL
     *
     * @param string $key
     * @param string $expiration
     *
     * @return string
     */
    public function getObjectUrl($key, $expiration = '+10 minutes');

    /**
     * Deletes object from the storage
     *
     * @param string $key
     */
    public function deleteObject($key);
}