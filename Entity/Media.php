<?php
namespace Arnm\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Arnm\MediaBundle\Entity\Attribute;
/**
 * Arnm\MediaBundle\Entity\Media
 *
 * @ORM\Table(name="media", indexes={@ORM\Index(name="file_idx", columns={"file"})})
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
     * @var string $tag
     *
     * @ORM\Column(name="tag", type="string", length=255, nullable=true)
     */
    private $tag;

    /**
     * @var datetime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @var datetime $updated
     *
     * @Gedmo\Timestampable
     * @ORM\Column(type="datetime")
     */
    private $updated;

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
     * Get datatime of when the record was created
     *
     * @return string
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Explicitely sets the created time
     *
     * @param string $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * Gets the datetime of when the recored was updated
     *
     * @return string
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Explicitely sets the updated time
     *
     * @param string $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
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
        if ($attr instanceof Attribute) {
            return $attr->getValue();
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
        if ($urlAttr instanceof Attribute) {
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
            if ($attr->getName() == $name) {
                return $attr;
            }
        }

        return null;
    }
}