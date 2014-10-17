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
     *
     * @return Model
     */
    public function saveObject($key, $file);

    /**
     * Saves requested object into a target file
     *
     * @param string $key
     * @param string $targetFile
     *
     * @return Model
     */
    public function getObject($key, $targetFile);
}