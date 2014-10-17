<?php
namespace Arnm\MediaBundle\Service;

use Arnm\MediaBundle\Service\Storage\MediaStorageInterface;
/**
 * Media manager is the one and only class that is responsible for any media resources management
 *
 * @author Alex Agulyansky <alex@iibspro.com>
 */
class MediaManager
{
    /**
     * @var MediaStorageInterface
     */
    private $storage;

    public function __construct(MediaStorageInterface $storage)
    {
        $this->setStorage($storage);
    }

	/**
     * @return \Arnm\MediaBundle\Service\Storage\MediaStorageInterface
     */
    public function getStorage()
    {
        return $this->storage;
    }

	/**
     * @param \Arnm\MediaBundle\Service\Storage\MediaStorageInterface $storage
     */
    public function setStorage(MediaStorageInterface $storage)
    {
        $this->storage = $storage;
    }
}