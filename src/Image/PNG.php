<?php
namespace CSD\Photo\Image;

use CSD\Photo\Metadata\Xmp;

/**
 * @author Daniel Chesterton <daniel@chestertondevelopment.com>
 */
class PNG extends AbstractImage
{

    /**
     * @param $filename
     *
     * @return bool
     */
    public function save($filename = null)
    {
        // TODO: Implement save() method.
    }

    /**
     * @return string
     *
     * todo: think of better name
     */
    public function getBytes()
    {
        // TODO: Implement getBytes() method.
    }

    /**
     * @return Xmp
     */
    public function getXmp()
    {
        // TODO: Implement getXmp() method.
    }

    /**
     * @return Exif
     */
    public function getExif()
    {
        // TODO: Implement getExif() method.
    }

    /**
     * @return Iptc
     */
    public function getIptc()
    {
        // TODO: Implement getIptc() method.
    }

    public static function fromFile($filename)
    {
        $contents = file_get_contents($filename);









        // TODO: Implement fromFile() method.
    }
}
