<?php

namespace wbx\FileBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
* wbx\FileBundle\Entity\File
*
* @Orm\MappedSuperclass
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
     * @var string $extension
     *
     * @ORM\Column(name="extension", type="string", length=255, nullable=true)
     */
    private $extension;

    /**
     * @var string $mime_type
     *
     * @ORM\Column(name="mime_type", type="string", length=255, nullable=true)
     */
    private $mime_type;

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
     * @var string $to_unlink
     */
    private $to_unlink;

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

	public function __toString() {
	    return $this->name;
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
     * Set extension
     *
     * @param string $extension
     */
    public function setExtension($extension) {
        $this->extension = $extension;
    }

    /**
     * Get extension
     *
     * @return string
     */
    public function getExtension() {
        return $this->extension;
    }

    /**
     * Set mime_type
     *
     * @param string $mime_type
     */
    public function setMimeType($mime_type) {
        $this->mime_type = $mime_type;
    }

    /**
     * Get mime_type
     *
     * @return string
     */
    public function getMimeType() {
        return $this->mime_type;
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
     * Get $is_web_image
     *
     * @return boolean
     */
    public function getIsWebImage() {
        return in_array($this->getMimeType(), array('image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png', 'image/gif'));
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
            $this->name = $this->name != "" ? $this->name : $this->file->getClientOriginalName();

            $this->extension = $this->file->getExtension();
            if (strlen($this->extension) == 0) {
                $this->extension = substr($this->file->getClientOriginalName(), strrpos($this->file->getClientOriginalName(), '.') + 1);
            }

            $this->mime_type = $this->file->getMimeType();
            $this->path = uniqid() . (strlen($this->extension) > 0 ? '.' . $this->extension : "");
            $this->is_file_changed = false;
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
     * @ORM\PreRemove()
     */
    public function preRemoveUpload() {
        // http://www.doctrine-project.org/jira/browse/DDC-1401
        $this->to_unlink = $this->getAbsolutePath();
    }

    /**
     * @ORM\PostRemove()
     */
    public function postRemoveUpload() {
        if ($this->to_unlink) {
            unlink($this->to_unlink);
        }
    }


    public function getDownloadFilename() {
        $ret = $this->getName();

        $ext = "." . $this->getExtension();
        $len = strlen($ext);

        if ($len > 1) {
            if (substr($this->getName(), -$len) != $ext) {
                $ret .= $ext;
            }
        }

        return $ret;
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