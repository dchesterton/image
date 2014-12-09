<?php

namespace CSD\Photo\Image;

use CSD\Photo\Metadata\Reader\AggregateReader;

/**
 * @author Daniel Chesterton <daniel@chestertondevelopment.com>
 */
abstract class AbstractImage implements ImageInterface
{
    protected $filename;

    /**
     * @param string $filename
     *
     * @return $this
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * @return AggregateReader
     */
    public function getAggregateMeta()
    {
        $xmp = $this->getXmp();
        $exif = $this->getExif();
        $iptc = $this->getIptc();

        $reader = new AggregateReader($xmp, $exif, $iptc);

        return $reader;
    }
}
