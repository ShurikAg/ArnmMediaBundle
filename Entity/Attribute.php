<?php
namespace Arnm\MediaBundle\Entity;

use Arnm\MediaBundle\Entity\Media;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\Blameable\Traits\BlameableEntity;
/**
 * Arnm\MediaBundle\Entity\Attribute
 *
 * @ORM\Table(name="attribute")
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @Gedmo\Loggable
 */
class Attribute
{

    use SoftDeleteableEntity;
    use TimestampableEntity;
    use BlameableEntity;

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
    * @Gedmo\Versioned
    */
    private $name;

   /**
    * @var string $value
    *
    * @ORM\Column(name="value", type="string", length=1000)
    * @Gedmo\Versioned
    */
    private $value;

   /**
    * @var Media $media
    *
    * @ORM\ManyToOne(targetEntity="Media", inversedBy="attributes")
    * @ORM\JoinColumn(name="media_id", referencedColumnName="id")
    */
    private $media;

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
    *
    * @return Attribute
    */
    public function setName($name)
    {
        $this->name = (string) $name;

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
    * Set value
    *
    * @param text $value
    *
    * @return Attribute
    */
    public function setValue($value)
    {
        $this->value = (string) $value;

        return $this;
    }

   /**
    * Get value
    *
    * @return text
    */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set media
     *
     * @param Arnm\MediaBundle\Entity\Media $media
     *
     * @return Attribute
     */
    public function setMedia(Media $media = null)
    {
        $this->media = $media;

        return $this;
    }

    /**
     * Get media
     *
     * @return Arnm\MediaBundle\Entity\Media
     */
    public function getMedia()
    {
        return $this->media;
    }
}