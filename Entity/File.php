<?php

namespace wbx\FileBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * wbx\FileBundle\Entity\File
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="wbx\FileBundle\Entity\FileRepository")
 * @ORM\HasLifecycleCallbacks
 */
class File {
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
     * @var string $path
     *
     * @ORM\Column(name="path", type="string", length=255, nullable=true)
     */
    private $path;

    /**
     * @var string $is_file_changed
     *
     * @ORM\Column(name="is_file_changed", type="boolean", nullable=true)
     */
    private $is_file_changed;

    /**
     * @var string $old_path
     */
    private $old_path;

    /**
     * @var string $file
     * @Assert\File(maxSize="5000000")
     */
    public $file;


    /**
     *  Constructor
     */
    public function __construct() {
        $this->uniq = uniqid();
    }


    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set path
     *
     * @param string $path
     */
    public function setPath($path) {
        $this->path = $path;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * Set $is_file_changed
     * needed to trigger the Lifecycle Callbacks when only $file is modified
     *
     * @param boolean $is_file_changed
     */
    public function setIsFileChanged($is_file_changed) {
        $this->is_file_changed = $is_file_changed;
    }

    /**
     * Get $is_file_changed
     *
     * @return boolean
     */
    public function getIsFileChanged() {
        return $this->is_file_changed;
    }


    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload() {
        if ($this->file !== null) {
            $this->old_path = $this->path;
            $this->path = uniqid() . '.' . $this->file->guessExtension();
            $this->is_file_changed = false;
            $this->name = $this->name != "" ? $this->name : $this->file->getClientOriginalName();
        }
        else {
            $this->name = $this->name != "" ? $this->name : "untitled";
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload() {
        if ($this->file !== null) {

            // if old == new -> delete old because rename() doesn't seem to replace existing file
            // if old != new -> delete old because not needed anymore
            if ($this->getOldAbsolutePath()) {
                unlink($this->getOldAbsolutePath());
            }

            $this->file->move($this->getUploadRootDir(), $this->path);

            unset($this->file);
        }
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload() {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }
    }


    public function getAbsolutePath() {
        return $this->path === null ? null : $this->getUploadRootDir() . '/' . $this->path;
    }

    public function getOldAbsolutePath() {
        return $this->old_path === null ? null : $this->getUploadRootDir() . '/' . $this->old_path;
    }

    public function getWebPath() {
        return $this->path === null ? null : '/' . $this->getUploadDir() . '/' . $this->path;
    }

    protected function getUploadRootDir() {
        return __DIR__ . '/../../../../../web/' . $this->getUploadDir();
    }

    protected function getUploadDir() {
        return 'uploads';
    }
}