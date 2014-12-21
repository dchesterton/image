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

        switch ($ext) {
            case 'jpg':
            case 'jpeg':
                return Format\JPEG::fromFile($fileName);
                break;
            case 'png':
                return Format\PNG::fromFile($fileName);
                break;
            case 'webp':
                return Format\WebP::fromFile($fileName);
                break;
        }

        throw new \Exception('Unrecognised file name');
    }
}
