<?php

namespace Nubersoft;

class ImageFactory extends \Nubersoft\nApp
{
    public $destination, $error;

    protected  $original, $search_for_filename, $search_for_location, $filesize;

    const SMALL_INPUT = 1000000;
    const MEDIUM_INPUT = 10000000;
    const LARGE_INPUT = 100000000;
    const MAX_INPUT = 10000000000000000;

    public function setFileSize()
    {
        $args_count = func_num_args();

        if ($args_count > 0)
            $args = func_get_args();

        $this->max_filesize = (isset($args)) ? $args[0] : self::SMALL_INPUT;

        return $this;
    }

    public function fetchOriginal($file)
    {
        $size = @getimagesize($file);
        $this->original['width'] = $size[0];
        $this->original['height'] = $size[1];
        $this->original['type'] = $size['mime'];
        return $this;
    }

    public function getLargerDimension()
    {
        return ($this->original['width'] > $this->original['height']) ? 'width' : 'height';
    }

    public function autoScale($file, $targeDim)
    {
        $size = $this->fetchOriginal($file)->get();
        $scale = $targeDim / $size[$this->getLargerDimension()];

        return [
            'width' => ($scale * $size['width']),
            'height' => ($scale * $size['height'])
        ];
    }

    public function get()
    {
        return $this->original;
    }

    public function scrapThumbnails()
    {
        if (!empty($this->search_for_filename)) {
            if (!empty($this->search_for_location)) {
                foreach ($this->search_for_filename as $filename) {
                    if (in_array($filename, $this->search_for_location)) {
                        unlink($filename);
                        if (is_file($filename))
                            $this->error[] = 'Deleted: ' . $filename;
                    }
                }
            }
        }

        return $this;
    }

    protected function fileSize($val, $nApp)
    {
        return $this->getHelper('Conversion\Data')->getByteSize($this->max_filesize, array('from' => 'B', 'to' => 'MB', 'round' => 2, 'extension' => true));
    }

    public function makeThumbnail($thumb_target = '', $width = 60, $height = 60, $SetFileName = false, $quality = 80, $cropImg = true)
    {

        $this->scrapThumbnails();

        $this->max_filesize = (isset($this->max_filesize)) ? $this->max_filesize : self::SMALL_INPUT;

        $targetSize  = filesize($thumb_target);

        if ($this->max_filesize < $targetSize) {
            return false;
        }
        # Set original file settings
        $this->fetchOriginal($thumb_target);
        # Determine kind to extract from
        if ($this->original['type'] == 'image/gif') {
            $thumb_img  =   imagecreatefromgif($thumb_target);
        } elseif ($this->original['type'] == 'image/png') {
            $thumb_img  =   imagecreatefrompng($thumb_target);
            $quality =   7;
        } elseif ($this->original['type'] == 'image/jpeg') {
            ini_set('display_errors', 1);
            error_reporting(E_ALL);

            $thumb_img  =   imagecreatefromjpeg($thumb_target);
        } else {
            return false;
        }

        if (empty($thumb_img))
            return false;

        # Assign variables for calculations
        $w  =   $this->original['width'];
        $h  =   $this->original['height'];
        # Calculate proportional height/width
        if ($w > $h) {
            $new_height =   $height;
            $new_width  =   ($cropImg) ? (floor($w * ($new_height / $h))) : $width;
            $crop_x  =   ($cropImg) ? (ceil(($w - $h) / 2)) : 0;
            $crop_y  =   0;
        } else {
            $new_width  =   $width;
            $new_height =   ($cropImg) ? (floor($h * ($new_width / $w))) : $height;
            $crop_x  =   0;
            $crop_y  =   ($cropImg) ? (ceil(($h - $w) / 2)) : 0;
        }
        # New image
        $tmp_img = imagecreatetruecolor($width, $height);
        # Keeps transparency in background
        if ($this->original['type'] == 'image/png') {
            imagealphablending($tmp_img, false);
            imagesavealpha($tmp_img, true);
        }
        # Copy/crop action
        imagecopyresampled($tmp_img, $thumb_img, 0, 0, $crop_x, $crop_y, $new_width, $new_height, $w, $h);

        # If false, send browser header for output to browser window
        if (!$SetFileName)
            header('Content-Type: ' . $this->original['type']);
        # Output proper image type
        if ($this->original['type'] == 'image/gif') ($SetFileName) ? imagegif($tmp_img, $SetFileName) : imagegif($tmp_img);
        elseif ($this->original['type'] == 'image/png') {
            ($SetFileName) ? imagepng($tmp_img, $SetFileName, $quality) : imagepng($tmp_img);
        } elseif ($this->original['type'] == 'image/jpeg') ($SetFileName) ? imagejpeg($tmp_img, $SetFileName, $quality) : imagejpeg($tmp_img);
        # Destroy set images
        if (isset($thumb_img))
            imagedestroy($thumb_img);
        # Destroy image
        if (isset($tmp_img))
            imagedestroy($tmp_img);
    }

    public function searchFor($filename = false)
    {
        if (empty($filename))
            return $this;

        $this->search_for_filename[] = $filename;

        return $this;
    }
}
