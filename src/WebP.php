<?php
namespace CSD\Image;

use CSD\Image\Metadata\Exif;
use CSD\Image\Metadata\UnsupportedException;
use CSD\Image\Metadata\Xmp;

/**
 * @author Daniel Chesterton <daniel@chestertondevelopment.com>
 */
class WebP extends Image
{
    /**
     * @var Xmp
     */
    private $xmp;

    /**
     * @var Exif
     */
    private $exif;

    /**
     * @var bool
     */
    private $hasNewXmp = false;

    /**
     * @var WebP\Chunk[]
     */
    private $chunks;

    /**
     * @param string $contents
     * @param string $filename
     *
     * @throws \Exception
     */
    public function __construct($contents, $filename = null)
    {
        // check header
        if ('RIFF' !== substr($contents, 0, 4)) {
            throw new \Exception('Invalid WebP file');
        }

        if ('WEBP' !== substr($contents, 8, 4)) {
            throw new \Exception('Invalid WebP file');
        }

        $this->chunks = $this->getChunksFromContents($contents);

        if (!$this->isExtendedFormat()) {
            throw new \Exception('Only extended WebP format is supported');
        }

        $this->filename = $filename;
    }

    /**
     * @return Xmp
     */
    public function getXmp()
    {
        if (!$this->xmp) {
            $chunk = $this->getXmpChunk();

            if ($chunk) {
                $this->xmp = new Xmp($chunk->getData());
            } else {
                $this->xmp = new Xmp;
            }
        }

        return $this->xmp;
    }

    /**
     * @param Xmp $xmp
     *
     * @return $this
     */
    public function setXmp(Xmp $xmp)
    {
        $this->xmp = $xmp;
        $this->hasNewXmp = true;

        return $this;
    }

    /**
     * @param string $type
     *
     * @return false|WebP\Chunk
     */
    private function getChunkByType($type)
    {
        foreach ($this->chunks as $chunk) {
            if ($chunk->getType() === $type) {
                return $chunk;
            }
        }

        return false;
    }

    /**
     * @return false|WebP\Chunk
     */
    private function getXmpChunk()
    {
        return $this->getChunkByType('XMP ');
    }

    /**
     * @return false|WebP\Chunk
     */
    private function getExifChunk()
    {
        return $this->getChunkByType('EXIF');
    }

    /**
     * {@inheritdoc}
     */
    public function getExif()
    {
        if (!$this->exif) {
            $chunk = $this->getExifChunk();

            if ($chunk) {
                $this->exif = new Exif($chunk->getData());
            } else {
                $this->exif = new Exif;
            }
        }

        return $this->exif;
    }

    /**
     * {@inheritdoc}
     */
    public function getIptc()
    {
        throw new UnsupportedException('WebP files do not support IPTC metadata');
    }

    /**
     * @param $filename
     *
     * @return WebP
     * @throws \Exception
     */
    public static function fromFile($filename)
    {
        return new self(file_get_contents($filename), $filename);
    }

    /**
     * @param string $contents
     *
     * @throws \Exception
     * @return WebP\Chunk[]
     */
    private function getChunksFromContents($contents)
    {
        $pos = 12; // skip over file header
        $chunkType = substr($contents, $pos, 4);

        $chunks = [];

        while ($chunkType) {
            $pos += 4;

            $size = unpack('Vsize', substr($contents, $pos, 4));
            $size = $size['size'];

            // skip over size bytes
            $pos += 4;

            $payload = substr($contents, $pos, $size);

            // skip to end of payload
            $pos += $size;

            // skip padding byte if odd
            if ($size & 1) {
                $pos += 1;
            }

            $chunk = new WebP\Chunk($chunkType, $payload);
            $chunks[] = $chunk;

            $chunkType = substr($contents, $pos, 4);
        }

        return $chunks;
    }

    /**
     * @return string
     */
    public function getBytes()
    {
        if ($this->xmp && ($this->xmp->hasChanges() || $this->hasNewXmp)) {
            $data = $this->xmp->getXml();

            $xmpChunk = $this->getXmpChunk();

            if ($xmpChunk) {
                // update the existing chunk
                $xmpChunk->setData($data);
            } else {
                // add new chunk to contain XMP data
                $xmpChunk = new WebP\Chunk('XMP ', $data);

                // insert at end of chunks
                $this->chunks[] = $xmpChunk;
            }
        }

        // todo: set XMP byte in VP8X header

        $chunks = '';

        /** @var $chunk WebP\Chunk */
        foreach ($this->chunks as $chunk) {
            $chunks .= $chunk->getChunk();
        }

        $length = strlen($chunks) + 4;
        $header = 'RIFF' . pack('V', $length) . 'WEBP';

        return $header . $chunks;
    }

    /**
     * @return bool
     */
    private function isExtendedFormat()
    {
        $first = $this->chunks[0];
        return 'VP8X' === $first->getType();
    }
}
