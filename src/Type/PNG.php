<?php
namespace CSD\Image\Type;

use CSD\Image\Metadata\UnsupportedException;
use CSD\Image\Metadata\Xmp;
use CSD\Image\Image;

/**
 * @author Daniel Chesterton <daniel@chestertondevelopment.com>
 */
class PNG extends Image
{
    /**
     * First 8 bytes of all PNG files.
     */
    const SIGNATURE = "\x89PNG\x0d\x0a\x1a\x0a";

    /**
     * @var Xmp
     */
    private $xmp;

    /**
     * @var bool
     */
    private $hasNewXmp = false;

    /**
     * @var PNG\Chunk[]
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
        $signature = substr($contents, 0, 8);

        // check PNG signature is present
        if (self::SIGNATURE !== $signature) {
            throw new \Exception('Invalid PNG file signature');
        }

        $this->chunks = $this->getChunksFromContents($contents);
        $this->filename = $filename;
    }

    /**
     * {@inheritdoc}
     */
    public function getBytes()
    {
        if ($this->xmp && ($this->xmp->hasChanges() || $this->hasNewXmp)) {
            $data = "XML:com.adobe.xmp\x00\x00\x00\x00\x00" . $this->xmp->getXml();

            $xmpChunk = $this->getXmpChunk();

            if ($xmpChunk) {
                // update the existing chunk
                $xmpChunk->setData($data);
            } else {
                // add new chunk to contain XMP data
                $xmpChunk = new PNG\Chunk('iTXt', $data);

                // insert before the last chunk (iEND)
                array_splice($this->chunks, count($this->chunks) - 1, 0, [$xmpChunk]);
            }
        }

        $file = self::SIGNATURE;

        foreach ($this->chunks as $chunk) {
            $file .= $chunk->getChunk();
        }

        return $file;
    }

    /**
     * @return Xmp
     */
    public function getXmp()
    {
        if (!$this->xmp) {
            $xmpChunk = $this->getXmpChunk();

            if ($xmpChunk) {
                $data = $xmpChunk->getData();
                $data = substr($data, 17); // remove XML:com.adobe.xmp marker
                $data = ltrim($data, "\x00"); // remove null bytes

                $this->xmp = new Xmp($data);
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
     * @return bool|PNG\Chunk
     */
    private function getXmpChunk()
    {
        foreach ($this->chunks as $chunk) {
            if ('iTXt' === $chunk->getType() && strncmp($chunk->getData(), 'XML:com.adobe.xmp', 17) === 0) {
                return $chunk;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getExif()
    {
        throw new UnsupportedException('PNG files do not support EXIF metadata');
    }

    /**
     * {@inheritdoc}
     */
    public function getIptc()
    {
        throw new UnsupportedException('PNG files do not support IPTC metadata');
    }

    /**
     * @param $filename
     *
     * @return PNG
     */
    public static function fromFile($filename)
    {
        return new self(file_get_contents($filename), $filename);
    }

    /**
     * @param string $contents
     *
     * @throws \Exception
     * @return PNG\Chunk[]
     */
    private function getChunksFromContents($contents)
    {
        $chunkHeader = substr($contents, 8, 8);
        $pos = 16;

        $chunks = [];

        while ($chunkHeader) {
            $chunk = unpack('Nsize/a4type', $chunkHeader);
            $data = substr($contents, $pos, $chunk['size']);

            // move pointer over the chunk
            $pos += $chunk['size'];

            $crc = substr($contents, $pos, 4);

            $chunkObj = new PNG\Chunk($chunk['type'], $data);

            if ($crc !== $chunkObj->getCrc()) {
                throw new \Exception(sprintf('Invalid CRC for chunk with type: %s', $chunk['type']));
            }

            $chunks[] = $chunkObj;

            // move pointer over CRC
            $pos += 4;

            $chunkHeader = substr($contents, $pos, 8);
            $pos += 8;
        }

        return $chunks;
    }
}
