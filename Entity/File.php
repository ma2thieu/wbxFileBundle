<?php

namespace wbx\FileBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\HttpFoundation\File\File AS SymfonyFile;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
    protected $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var string $extension
     *
     * @ORM\Column(name="extension", type="string", length=255, nullable=true)
     */
    protected $extension;

    /**
     * @var string $path
     *
     * @ORM\Column(name="path", type="string", length=255, nullable=true)
     */
    protected $path;

    /**
     * @var string $preview_path
     *
     * @ORM\Column(name="preview_path", type="string", length=255, nullable=true)
     */
    protected $preview_path;

    /**
     * @var string $is_web_image
     *
     * @ORM\Column(name="is_web_image", type="boolean", nullable=true)
     */
    private $is_web_image;

    /**
     * @var string $is_file_changed
     *
     * @ORM\Column(name="is_file_changed", type="boolean", nullable=true)
     */
    protected $is_file_changed;

    /**
     * @var string $mask_path
     *
     * @ORM\Column(name="mask_path", type="string", length=255, nullable=true)
     */
    protected $mask_path;

    /**
     * @var string $old_path
     */
    protected $old_path;

    /**
     * @var string $old_preview_path
     */
    protected $old_preview_path;

    /**
     * @var string $to_unlink
     */
    protected $to_unlink;

    /**
     * @var string $to_unlink_preview
     */
    protected $to_unlink_preview;

    /**
     * @var \Symfony\Component\HttpFoundation\File\File $file
     */
    protected $file;

    /**
     * @var string $to_empty
     */
    protected $to_empty;

    /**
     * @var string $preview_format
     */
    protected $preview_format = "jpg";

    /**
     * @var string $default_path
     */
    protected $default_path = "/bundles/wbxfile/images/default.png";


    /**
     *  Constructor
     */
    public function __construct() {
        $this->uniq = uniqid();
        $this->mask_path = "";
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
     * Set preview_path
     *
     * @param string $preview_path
     */
    public function setPreviewPath($preview_path) {
        $this->preview_path = $preview_path;
    }

    /**
     * Get preview_path
     *
     * @return string
     */
    public function getPreviewPath() {
        return $this->preview_path;
    }

    /**
     * Set $is_web_image
     *
     * @param boolean $is_web_image
     */
    public function setIsWebImage($is_web_image) {
        $this->is_web_image = $is_web_image;
    }

    /**
     * Get $is_web_image
     *
     * @return boolean
     */
    public function getIsWebImage() {
        return $this->is_web_image;
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
     * Get mask_path
     *
     * @return string
     */
    public function getMaskPath() {
        return $this->mask_path;
    }


    /**
     * Set preview_path
     *
     * @param string $mask_path
     */
    public function setMaskPath($mask_path) {
        $this->mask_path = $mask_path;
    }


    /**
     * @param \Symfony\Component\HttpFoundation\File\File $file
     */
    public function setFile(SymfonyFile $file) {
        $this->file = $file;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\File\File
     */
    public function getFile() {
        return $this->file;
    }



    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
	public function preUpload() {
        $this->is_file_changed = !$this->is_file_changed;

        if ($this->to_empty) {
            if (is_file($this->getAbsolutePath())) {
        		unlink($this->getAbsolutePath());
        	}

            if (is_file($this->getAbsolutePreviewPath())) {
        		unlink($this->getAbsolutePreviewPath());
        	}

            $this->path = null;
            $this->preview_path = null;
            $this->name = "untitled";
            $this->is_web_image = null;
        }
        else {
            if ($this->file !== null) {
                $this->old_path = $this->path;

                if ($this->file instanceof UploadedFile) {
                    $filename = $this->file->getClientOriginalName();
                    $this->is_web_image = in_array($this->file->getMimeType(), array('image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png', 'image/gif'));
                }
				else {
                    $filename = $this->file->getFileName();
                    $this->is_web_image = in_array($this->file->getExtension(), array('jpeg', 'jpg', 'png', 'gif'));
                }

                $this->extension = $this->extractFilenameExtension($filename);
                if ($this->extension == "") {
                    $this->extension = "dat";
                }

                $this->name = $this->name != "" ? $this->name : $filename;

                $this->path = uniqid() . '.' . $this->extension;

                $this->preview_path = null;

                if ($this->extension == "pdf") {
                    if (extension_loaded('Imagick')) {
                        $this->preview_path = $this->path . '.' . $this->preview_format;
                    }
                }

                if ($this->mask_path != "") {
                    if (extension_loaded('Imagick')) {
                        $this->preview_format = "png";
                        $this->preview_path = $this->path . '.' . $this->preview_format;
                    }
                }

            }
            else {
                $this->name = $this->name != "" ? $this->name : "untitled";
            }
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload() {
        if (!$this->to_empty && $this->file !== null) {

            // if old == new -> delete old because rename() doesn't seem to replace existing file
            // if old != new -> delete old because not needed anymore
            if ($this->getOldAbsolutePath() && is_file($this->getOldAbsolutePath())) {
                unlink($this->getOldAbsolutePath());
            }

            if ($this->getOldAbsolutePreviewPath() && is_file($this->getOldAbsolutePreviewPath())) {
                unlink($this->getOldAbsolutePreviewPath());
            }

            $this->file->move($this->getUploadRootDir(), $this->path);


            if ($this->extension == "pdf") {
                if (extension_loaded('Imagick')) {
                    $img = new \Imagick();
                    $img->setResolution(3*72, 3*72);

                    $img->readImage($this->getAbsolutePath() . '[0]');
                    $img->setbackgroundcolor("#ff0000");

                    $img->resampleImage(72, 72, \imagick::FILTER_GAUSSIAN, 1);
                    $img->setImageResolution(72, 72);

                    $img_flat = new \IMagick();
                    $img_flat->newImage($img->getImageWidth(), $img->getImageHeight(), new \ImagickPixel("white"));

                    $img_flat->compositeImage($img, \imagick::COMPOSITE_OVER, 0, 0);

                    $img_flat->setImageFormat($this->preview_format);
                    $img_flat->writeImage('image.jpg');
                    $img_flat->setCompressionQuality(90);
                    $img_flat->writeImages($this->getAbsolutePreviewPath(), true);

                    $img->clear();
                    $img->destroy();

                    $img_flat->clear();
                    $img_flat->destroy();
                }
            }

            if ($this->mask_path != "") {
                if (extension_loaded('Imagick')) {
                    $img = new \Imagick($this->getAbsolutePath());
                    $new = new \Imagick($this->getUploadRootDir() . '/../' . $this->getMaskPath());
                    $mask = new \Imagick($this->getUploadRootDir() . '/../' . $this->getMaskPath());

                    $width = $new->getImageWidth();
                    $height = $new->getImageHeight();

                    $img_width = $img->getImageWidth();
                    $img_height = $img->getImageHeight();

                    list($thumb_width, $thumb_height) = $this->fit(
                        $img_width,
                        $img_height,
                        $width,
                        $height,
                        "out",
                        true
                    );

                    $img->resizeImage($thumb_width, $thumb_height, \Imagick::FILTER_LANCZOS, 1);
                    $img->cropImage($width, $height, ($thumb_width - $width) / 2, ($thumb_height - $height) / 2);

                    $new->resizeImage($width, $height, \Imagick::FILTER_LANCZOS, 1);
                    $mask->resizeImage($width, $height, \Imagick::FILTER_LANCZOS, 1);

                    $new->compositeImage($img, \Imagick::COMPOSITE_DEFAULT, 0, 0);
                    $new->compositeImage($mask, \Imagick::COMPOSITE_DSTIN, 0, 0, \Imagick::CHANNEL_ALPHA);

                    $new->setImageFormat($this->extension);
                    $new->writeImage($this->getAbsolutePreviewPath());

                    $new->clear();
                    $new->destroy();
                }
            }


            unset($this->file);
        }
    }


    /**
     * @ORM\PreRemove()
     */
    public function preRemoveUpload() {
        // http://www.doctrine-project.org/jira/browse/DDC-1401
        $this->to_unlink = $this->getAbsolutePath();
        $this->to_unlink_preview = $this->getAbsolutePreviewPath();
    }

    /**
     * @ORM\PostRemove()
     */
	public function postRemoveUpload() {
	    if ($this->to_unlink && is_file($this->to_unlink)) {
			unlink($this->to_unlink);
	    }

        if ($this->to_unlink_preview && is_file($this->to_unlink_preview)) {
   			unlink($this->to_unlink_preview);
   	    }
	}


    public function getDownloadFilename() {
        $n = $this->getName();

        if ($this->extractFilenameExtension($n) == "") {
            $n .= '.' . $this->extension;
        }

        return $n;
    }

    public function getAbsolutePath() {
        return $this->path === null ? null : $this->getUploadRootDir() . '/' . $this->path;
    }

    public function getAbsolutePreviewPath() {
        return $this->preview_path === null ? null : $this->getUploadRootDir() . '/' . $this->preview_path;
    }

    public function getOldAbsolutePath() {
        return $this->old_path === null ? null : $this->getUploadRootDir() . '/' . $this->old_path;
    }

    public function getOldAbsolutePreviewPath() {
        return $this->old_preview_path === null ? null : $this->getUploadRootDir() . '/' . $this->old_preview_path;
    }

    public function getWebPath() {
        if ($this->path === null) {
            return $this->default_path;
        }
        else if ($this->preview_path !== null) {
            return '/' . $this->getUploadDir() . '/' . $this->preview_path;
        }
        else if ($this->is_web_image) {
            return '/' . $this->getUploadDir() . '/' . $this->path;
        }
        else {
            return $this->default_path;
        }
    }

    public function getDownloadPath() {
        if ($this->path === null) {
            return $this->default_path;
        }
        else {
            return '/' . $this->getUploadDir() . '/' . $this->path;
        }
    }


    protected function getUploadRootDir() {
        return __DIR__ . '/../../../../../../' . $this->getWebDir() . '/' . $this->getUploadDir();
    }

    public function getWebDir() {
        return 'web';
    }

    public function getUploadDir() {
        return 'uploads';
    }


    protected function extractFilenameExtension($filename) {
        $extension = "";

        $filename_a = explode(".", $filename);
        if (count($filename_a) > 1) {
            $extension = array_pop($filename_a);
        }

        return $extension;
    }

    protected function fit($w_in, $h_in, $w_box, $h_box, $mode = "in", $upscale = true) {
    	$rw= $w_in / $w_box;
    	$rh= $h_in / $h_box;
    	$ri= $w_in / $h_in;

        if (!$upscale && $rw < 1 && $rh < 1) {
            $a = array($w_in, $h_in);
        }
        else {
            if ($rw > $rh) {
            	$a = $mode == "in" ?
                    array($w_box, round($w_box / $ri)) :
                    array(round($h_box * $ri), $h_box);
            }
            else if ($rw < $rh) {
                $a = $mode == "in" ?
                    array(round($h_box * $ri), $h_box) :
                    array($w_box, round($w_box / $ri));
            }
            else {
                $a = array($w_box, $h_box);
            }
        }

        return $a;
    }



}