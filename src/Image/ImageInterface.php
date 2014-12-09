<?php
namespace CSD\Photo\Image;

use CSD\Photo\Metadata\Reader\AggregateReader;
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
     */
    public function getXmp();

    /**
     * @return Exif
     */
    public function getExif();

    /**
     * @return Iptc
     */
    public function getIptc();

    /**
     * @return AggregateReader
     */
    public function getAggregateMeta();

    public static function fromFile($filename);
}
