<?php
namespace Arnm\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Arnm\MediaBundle\Entity\Attribute;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
/**
 * Arnm\MediaBundle\Entity\Media
 *
 * @ORM\Table(name="media")
 * @ORM\Entity(repositoryClass="Arnm\MediaBundle\Entity\MediaRepository")
 *
 * @ORM\HasLifecycleCallbacks
 */
class Media
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     *
     * @Assert\NotBlank()
     * @Assert\NotNull()
     */
    private $name;

    /**
     * @var string $file
     *
     * @ORM\Column(name="file", type="string", length=255)
     */
    private $file;

    /**
     * @var integer $size
     *
     * @ORM\Column(name="size", type="integer")
     */
    private $size;

    /**
     * @Assert\Image()
     */
    public $media;

    /**
     * @var string $tag
     *
     * @ORM\Column(name="tag", type="string", length=255, nullable=true)
     *
     * @Assert\Type(type="string", message="The value {{ value }} is not a valid {{ type }}.")
     * @Assert\MinLength(
     * limit=1,
     * message="Slug must be at least {{ limit }} characters."
     * )
     */
    private $tag;

    /**
     * @ORM\OneToMany(targetEntity="Attribute", mappedBy="media")
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $attributes;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->attributes = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Media
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set file
     *
     * @param string $file
     *
     * @return Media
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set size
     *
     * @param integer $size
     * @return Media
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return integer
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @return UploadedFile
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * Sets the tag for the media entity
     *
     * @param string $tag
     *
     * @return Media
     */
    public function setTag($tag)
    {
        $this->tag = (string) $tag;

        return $this;
    }

    /**
     * Get the tag of this media entity
     *
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Add attribute
     *
     * @param Arnm\MediaBundle\Entity\Attribute $attribute
     *
     * @return Media
     */
    public function addAttribute(\Arnm\MediaBundle\Entity\Attribute $attribute)
    {
        $this->attributes[] = $attribute;

        return $this;
    }

    /**
     * Get attributes
     *
     * @return Doctrine\Common\Collections\ArrayCollection
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Gets the value of an attribute by the attribute name
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getAttributeValueByName($name)
    {
        $attr = $this->getAttributeByName($name);
        if($attr instanceof Attribute)
        {
            return $attr->getValue();
        }

        return null;
    }

    //////////////////////////////////
    // Methods that dealing with file upload funcitonality
    //////////////////////////////////
    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if(null !== $this->media) {
            // do whatever you want to generate a unique name
            $this->file = uniqid() . '.' . $this->media->guessExtension();

            //set the size
            $this->setSize($this->media->getSize());
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if(null === $this->media) {
            return;
        }

        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        $this->media->move($this->getUploadRootDir(), $this->getFile());

        unset($this->media);
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if($file = $this->getAbsolutePath()) {
            unlink($file);
        }
    }

    public function getAbsolutePath()
    {
        return null === $this->file ? null : $this->getUploadRootDir() . '/' . $this->file;
    }

    public function getWebPath()
    {
        return null === $this->file ? null : $this->getUploadDir() . '/' . $this->file;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded documents should be saved
        return __DIR__ . '/../../../../web/' . $this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw when displaying uploaded doc/image in the view.
        return 'uploads/images';
    }

    /**
     * Gets target url attribute value is one exists
     *
     * @return string
     */
    public function getTargetUrl()
    {
        $urlAttr = $this->getAttributeByName('url');
        if($urlAttr instanceof Attribute){
            return $urlAttr->getValue();
        }

        return null;
    }

    /**
     * Gets the value of a caption tag
     *
     * @return string
     */
    public function getCaption()
    {
        $urlAttr = $this->getAttributeByName('caption');
        if($urlAttr instanceof Attribute){
            return $urlAttr->getValue();
        }

        return null;
    }

    /**
     * Finds an attribute by it's name
     *
     * @param string $name
     *
     * @return Attribute
     */
    public function getAttributeByName($name)
    {
        $name = (string) $name;
        $attrs = $this->getAttributes();
        foreach ($attrs as $attr) {
            if($attr->getName() == $name) {
                return $attr;
            }
        }

        return null;
    }
}