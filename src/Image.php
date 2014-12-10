<?php
namespace CSD\Photo;

use CSD\Photo\Image\ImageInterface;
use CSD\Photo\Image\JPEG;
use CSD\Photo\Image\PNG;

/**
 * @author Daniel Chesterton <daniel@chestertondevelopment.com>
 */
class Image
{
    /**
     * @param string $fileName
     *
     * @throws \Exception
     * @return ImageInterface
     *
     * @todo add more sophisticated checks by inspecting file
     */
    public static function fromFile($fileName)
    {
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if ('png' === $ext) {
            return PNG::fromFile($fileName);
        } elseif ('jpg' === $ext || 'jpeg' === $ext) {
            return JPEG::fromFile($fileName);
        }

        throw new \Exception('Unrecognised file name');
    }
}
