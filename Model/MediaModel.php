<?php
namespace Arnm\MediaBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
/**
 * This class is responsible for media form data
 *
 *
 * @author Alex Agulyansky <alex@iibspro.com>
 */
class MediaModel
{

    /**
     * Media ID
     *
     * @var int
     */
    private $id;

    /**
     * Media name
     *
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\NotNull()
     */
    private $name;

    /**
     * @var string $tag
     *
     * @Assert\Type(type="string", message="The value {{ value }} is not a valid {{ type }}.")
     * @Assert\Length(
     * min=3,
     * minMessage="Tag must be at least {{ limit }} characters."
     * )
     */
    private $tag;

    /**
     * This property represents uploaded file.
     *
     * In general when the object is built based on the existing media record,
     * the assumption that this field is empty and filled in only on form submission.
     *
     * @var string
     *
     * @Assert\Image()
     */
    private $file;

    /**
     * Namespace for inernal use
     *
     * @var string
     */
    private $namespace = null;

	/**
	 * Gets the ID
	 *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

	/**
	 * Sets the ID
	 *
     * @param int
     */
    public function setId($id)
    {
        $this->id = (int) $id;
    }

	/**
	 * Gets name
	 *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

	/**
	 * Sets name
	 *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = (string) $name;
    }

	/**
	 * Gets tag
	 *
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

	/**
	 * Sets tag
	 *
     * @param string $tag
     */
    public function setTag($tag)
    {
        $this->tag = (string) $tag;
    }

	/**
	 * Gets file after validation
	 *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

	/**
	 * Sets validated file
	 *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file)
    {
        $this->file = $file;
    }

	/**
	 * Gets namspace value
	 *
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

	/**
	 * Sets namespace value
	 *
     * @param string $namespace
     */
    public function setNamespace($namespace)
    {
        $this->namespace = (string) $namespace;
    }
}