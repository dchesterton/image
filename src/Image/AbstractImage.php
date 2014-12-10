<?php

namespace CSD\Photo\Image;

use CSD\Photo\Metadata\Aggregate;
use CSD\Photo\Metadata\UnsupportedException;

/**
 * @author Daniel Chesterton <daniel@chestertondevelopment.com>
 */
abstract class AbstractImage implements ImageInterface
{
    /**
     * @var string
     */
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
        try {
            $xmp = $this->getXmp();
        } catch (UnsupportedException $e) {
            $xmp = null;
        }

        try {
            $exif = $this->getExif();
        } catch (UnsupportedException $e) {
            $exif = null;
        }

        try {
            $iptc = $this->getIptc();
        } catch (UnsupportedException $e) {
            $iptc = null;
        }

        return new Aggregate($xmp, $iptc, $exif);
    }

    /**
     * {@inheritdoc}
     */
    public function save($filename = null)
    {
        $filename = $filename ?: $this->filename;

        if (!$filename) {
            throw new \Exception('Must provide a filename');
        }

        file_put_contents($filename, $this->getBytes());
    }
}
