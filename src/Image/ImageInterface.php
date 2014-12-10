<?php
namespace CSD\Photo\Image;

use CSD\Photo\Metadata\Aggregate;
use CSD\Photo\Metadata\UnsupportedException;
use CSD\Photo\Metadata\Exif;
use CSD\Photo\Metadata\Iptc;
use CSD\Photo\Metadata\Xmp;

/**
 *
 */
interface ImageInterface
{

    /**
     * @param $filename
     *
     * @return bool
     */
    public function save($filename = null);

    /**
     * @return string
     *
     * todo: think of better name
     */
    public function getBytes();

    /**
     * @param $filename
     */
    public function setFilename($filename);

    /**
     * @return Xmp
     * @throws UnsupportedException
     */
    public function getXmp();

    /**
     * @return Exif
     * @throws UnsupportedException
     */
    public function getExif();

    /**
     * @return Iptc
     * @throws UnsupportedException
     */
    public function getIptc();

    /**
     * @return Aggregate
     */
    public function getAggregateMeta();

    public static function fromFile($filename);
}
