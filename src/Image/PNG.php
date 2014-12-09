<?php
namespace CSD\Photo\Image;

use CSD\Photo\Metadata\UnsupportedException;
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
     * @return Iptc|void
     * @throws UnsupportedException
     */
    public function getIptc()
    {
        throw new UnsupportedException('PNG files do not support IPTC metadata');
    }

    public static function fromFile($filename)
    {
        $contents = file_get_contents($filename);


        $signaure = [137, 80, 78, 71, 13, 10, 26, 10];








        // TODO: Implement fromFile() method.
    }
}
