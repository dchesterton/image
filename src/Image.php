<?php

namespace CSD\Image;

use CSD\Image\Metadata\Aggregate;
use CSD\Image\Metadata\UnsupportedException;

/**
 * @author Daniel Chesterton <daniel@chestertondevelopment.com>
 */
abstract class Image implements ImageInterface
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
    public function getAggregate()
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
