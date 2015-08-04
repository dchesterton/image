<?php
namespace CSD\Image\Format;

use CSD\Image\Metadata\Exif;
use CSD\Image\Metadata\UnsupportedException;
use CSD\Image\Metadata\Xmp;
use CSD\Image\Image;

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
//            throw new \Exception('Only extended WebP format is supported');
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

    private function getBitstreamChunk()
    {
        foreach ($this->chunks as $chunk) {
            if ($chunk->getType() == 'VP8 ' || $chunk->getType() == 'VP8L') {
                return $chunk;
            }
        }

        throw new \Exception('Invalid format: No VP8 or VP8L chunk');
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
        // var_dump($filename);
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

            // var_dump($chunkType);

            if ('VP8X' === $chunkType) {
                $chunks[] = new WebP\VP8XChunk($payload);
            } else {
                $chunks[] = new WebP\Chunk($chunkType, $payload);
            }

            $chunkType = substr($contents, $pos, 4);
        }

        return $chunks;
    }

    /**
     * @return string
     */
    public function getBytes()
    {
        $xmp = $this->getXmp();

        if ($xmp && ($xmp->hasChanges() || $this->hasNewXmp)) {
            $data = $xmp->getString();

            $xmpChunk = $this->getXmpChunk();

            if ($xmpChunk) {
                // update the existing chunk
                $xmpChunk->setData($data);
            } else {
                // add new chunk to contain XMP data
                $this->chunks[] = new WebP\Chunk('XMP ', $data);
            }

            // todo: set XMP byte in VP8X header
        }

        $hasExtendedFeatures = false;

        foreach ($this->chunks as $chunk) {
            if (in_array($chunk->getType(), ['ICCP', 'ANIM', 'ALPH', 'EXIF', 'XMP'])) {
                $hasExtendedFeatures = true;
                break;
            }
        }

        if ($hasExtendedFeatures) {
            if (!$this->isExtendedFormat()) {
                // generate VP8X header

            }

            return $this->getFile($this->chunks);

        } else {
            $chunk = $this->getBitstreamChunk();
            return $this->getFile([$chunk]);
        }
    }

    /**
     * @param WebP\Chunk[] $chunks
     *
     * @return string
     */
    private function getFile($chunks)
    {
        $data = '';

        foreach ($chunks as $chunk) {
            $data .= $chunk->getChunk();
        }

        $header = 'RIFF' . pack('V', strlen($chunks) + 4) . 'WEBP';
        return $header . $data;
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
