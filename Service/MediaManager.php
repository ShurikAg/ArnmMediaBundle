<?php
namespace Arnm\MediaBundle\Service;

use Arnm\MediaBundle\Service\Storage\MediaStorageInterface;
use Doctrine\Common\Cache\CacheProvider;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Arnm\MediaBundle\Model\MediaModel;
use Arnm\MediaBundle\Entity\Media;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
     * Creates new media including saving the file into stoage as well as creating new media record
     *
     * @param MediaModel $mediaData
     *
     * @return Media
     */
    public function createMedia(MediaModel $mediaData)
    {
        $em = $this->getEntityManager();

        $targetFile = (string) $mediaData->getFile()->getClientOriginalName();

        //find content type
        $contentType = $mediaData->getFile()->getClientMimeType();
        //save file into media storage
        $this->getStorage()->saveObject($targetFile, $mediaData->getFile()->getPathname(), $contentType);

        //create new media object and populate it
        $media = new Media();
        $media->setName($mediaData->getName());
        $media->setTag($mediaData->getTag());
        $media->setFile($targetFile);
        $media->setSize($mediaData->getFile()->getClientSize());

        $em->persist($media);
        $em->flush();

        return $media;
    }

    /**
     * This method creates media model for a media form for a given media ID
     *
     * @param int $id
     *
     * @return MediaModel
     */
    public function createMediaModelForMediaId($id)
    {
        $em = $this->getEntityManager();

        //let's find a media by ID
        $media = $em->getRepository('ArnmMediaBundle:Media')->findOneById($id);

        if (!($media instanceof Media)) {
            return null;
        }

        //let's create new the model
        $model = new MediaModel();
        $model->setId($media->getId());
        $model->setName($media->getName());
        $model->setTag($media->getTag());

        return $model;
    }

    /**
     * Updates media record and files wih new data
     *
     * @param Media      $media
     * @param MediaModel $mediaData
     *
     * @return Media
     */
    public function updateMedia(Media $media, MediaModel $mediaData)
    {
        $em = $this->getDoctrine()->getManager();

        $targetFile = null;
        //do we need to update the actual file in storage
        if ($mediaData->getFile() instanceof UploadedFile) {
            //delete the old source
            $this->getStorage()->deleteObject($media->getFile());

            //save the new one
            $targetFile = (string) $mediaData->getFile()->getClientOriginalName();

            //find content type
            $contentType = $mediaData->getFile()->getClientMimeType();
            //save file into media storage
            $this->getStorage()->saveObject($targetFile, $mediaData->getFile()->getPathname(), $contentType);
        }

        //now update the record
        $media->setName($mediaData->getName());
        $media->setTag($mediaData->getTag());
        if (!is_null($targetFile)) {
            //if the file been uploaded
            //update it's details too
            $media->setFile($targetFile);
            $media->setSize($mediaData->getFile()->getClientSize());
        }

        $em->persist($media);
        $em->flush();

        return $media;
    }

    /**
     * Fully deletes an object from the system
     *
     * @param Media $media
     */
    public function deleteMedia(Media $media)
    {
        $key = $media->getFile();

        //delete the object first
        $em = $this->getEntityManager();

        foreach ($media->getAttributes() as $attribtue) {
            $em->remove($attribtue);
        }
        $em->remove($media);
        $em->flush();

        //now delete the resources from storage
        $this->getStorage()->deleteObject($key);
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
