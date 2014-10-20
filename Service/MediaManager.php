<?php
namespace Arnm\MediaBundle\Service;

use Arnm\MediaBundle\Service\Storage\MediaStorageInterface;
use Doctrine\Common\Cache\CacheProvider;
use Doctrine\Bundle\DoctrineBundle\Registry;

/**
 * This class is the main media manager
 *
 * @author Alex Agulyansky <alex@iibspro.com>
 */
class MediaManager
{

    const SIGNED_URL_EXPIRATION = '600';
    const SIGNED_URL_EXPIRATION_THRESHOLD = '10';

    /**
     * Media storage object instance
     *
     * @var MediaStorageInterface
     */
    private $storage;

    /**
     * Cache provider object instance
     *
     * @var CacheProvider
     */
    private $cache;

    /**
     * @var Registry
     */
    private $doctrine;

    /**
     * Constructor
     *
     * @param MediaStorageInterface $storage
     */
    public function __construct(MediaStorageInterface $storage, CacheProvider $cacheProvider, Registry $doctrine)
    {
        $this->setStorage($storage);
        $this->setCache($cacheProvider);
        $this->setDoctrine($doctrine);
    }

    /**
     * Gets signed url for an object for a given key
     *
     * @param string $key
     *
     * @return string
     */
    public function getObjectPublicUrl($key)
    {
        //getck if we already have a url for this key stored in cache
        if ($this->getCache()->contains($key)) {
           return $this->getCache()->fetch($key);
        }

        //check if the object even exists
        if (!$this->getStorage()->objectExists($key)) {
            throw new \InvalidArgumentException("Object '".$key."' does not exists!");
        }

        //otherwise create new signed url
        $signedUrl = $this->getStorage()->getObjectUrl($key, '+'.self::SIGNED_URL_EXPIRATION.' seconds');

        //save it into a cache
        $this->getCache()->save($key, $signedUrl, (self::SIGNED_URL_EXPIRATION-self::SIGNED_URL_EXPIRATION_THRESHOLD));

        return $signedUrl;
    }

    /**
     * Finds a single media by full file name
     *
     * @param string $file
     */
    public function findMediaByFile($file)
    {
        $em = $this->getEntityManager();
        $media = $em->getRepository('ArnmMediaBundle:Media')->findOneByFile($file);

        return $media;
    }

	/**
	 * Gets media storage object instance
	 *
     * @return MediaStorageInterface
     */
    public function getStorage()
    {
        return $this->storage;
    }

	/**
	 * Sets media storage object instance
	 *
     * @param \Arnm\MediaBundle\Service\Storage\MediaStorageInterface $storage
     */
    public function setStorage(MediaStorageInterface $storage)
    {
        $this->storage = $storage;
    }

	/**
	 * Gets cache provider object
	 *
     * @return \Doctrine\Common\Cache\CacheProvider
     */
    public function getCache()
    {
        return $this->cache;
    }

	/**
	 * Sets cache provider object
	 *
     * @param \Doctrine\Common\Cache\CacheProvider $cache
     */
    public function setCache(CacheProvider $cache)
    {
        $this->cache = $cache;
    }

	/**
	 * Gets doctrine registry
	 *
     * @return Registry
     */
    public function getDoctrine()
    {
        return $this->doctrine;
    }

	/**
	 * Sets Registry instance
	 *
     * @param \Doctrine\Bundle\DoctrineBundle\Registry $doctrine
     */
    public function setDoctrine(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    protected function getEntityManager()
    {
        return $this->getDoctrine()->getManager();
    }
}
