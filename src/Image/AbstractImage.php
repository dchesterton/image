<?php

namespace CSD\Photo\Image;

use CSD\Photo\Metadata\Reader\Aggregate;

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
     * @return Aggregate
     */
    public function getAggregateMeta()
    {
        $xmp = $this->getXmp();
        $exif = $this->getExif();
        $iptc = $this->getIptc();

        $reader = new Aggregate($xmp, $exif, $iptc);

        return $reader;
    }

    public function save($filename = null)
    {
        $filename = $filename ?: $this->filename;

        if (!$filename) {
            throw new \Exception('Must provide a filename');
        }

        file_put_contents($filename, $this->getBytes());
    }
}
