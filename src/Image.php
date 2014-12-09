<?php

namespace CSD\Photo;

use CSD\Photo\Image\ImageInterface;

/**
 * @author Daniel Chesterton <daniel@chestertondevelopment.com>
 */
class Image
{
    /**
     * @param string $fileName
     *
     * @return ImageInterface
     */
    public static function fromFile($fileName)
    {
        // try to guess type of filename and load either JPEG/TIFF/PNG etc.

        // throw exception if cannot work it out
    }
}
