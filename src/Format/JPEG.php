<?php
namespace CSD\Image\Format;

use CSD\Image\Metadata\Exif;
use CSD\Image\Metadata\Iptc;
use CSD\Image\Metadata\Xmp;
use CSD\Image\Image;

/**
 * @author Daniel Chesterton <daniel@chestertondevelopment.com>
 */
class JPEG extends Image
{
    const SOI = "\xFF\xD8";

    /**
     * @var JPEG\Segment[]
     */
    private $segments;

    /**
     * @var Xmp
     */
    private $xmp;

    /**
     * @var string
     */
    private $imageData;

    /**
     * @param $imageData string
     * @param $segments JPEG\Segment[]
     * @param $filename string
     */
    private function __construct($imageData, $segments, $filename = null)
    {
        $this->imageData = $imageData;
        $this->segments = $segments;
        $this->filename = $filename;
    }

    /**
     * @param $name
     *
     * @return JPEG\Segment[]
     */
    private function getSegmentsByName($name)
    {
        $segments = [];

        foreach ($this->segments as $segment) {
            if ($segment->getName() == $name) {
                $segments[] = $segment;
            }
        }

        return $segments;
    }

    /**
     * @param Xmp $xmp
     *
     * @return $this
     */
    public function setXmp(Xmp $xmp)
    {
        $this->xmp = $xmp;
        return $this;
    }

    /**
     * @return string
     */
    public function getBytes()
    {
        $stream = fopen('php://temp', 'r+');
        $this->write($stream);

        rewind($stream);

        $contents = stream_get_contents($stream);

        fclose($stream);

        return $contents;
    }

    /**
     * Save to file.
     *
     * @param string $filename
     * @throws \Exception
     * @return void
     */
    public function save($filename = null)
    {
        $filename = $filename ?: $this->filename;

        // Attempt to open the new jpeg file
        $handle = @fopen($filename, 'wb');

        // Check if the file opened successfully
        if (!$handle) {
            throw new \Exception(sprintf('Could not open file %s', $filename));
        }

        $this->write($handle);

        fclose($handle);
    }

    /**
     * Write JPG data to a stream/file.
     *
     * @param $handle
     */
    private function write($handle)
    {
        $this->insertXmpSegment();

        // write SOI
        fwrite($handle, "\xFF\xD8");

        // write each segment
        foreach ($this->segments as $segment) {
            $segmentContent  = sprintf("\xFF%c", $segment->getType()); // marker
            $segmentContent .= pack("n", strlen($segment->getData()) + 2); // size
            $segmentContent .= $segment->getData();

            fwrite($handle, $segmentContent);
        }

        // write the image data
        fwrite($handle, $this->imageData);

        // write EOI
        fwrite($handle, "\xFF\xD9");
    }

    /**
     * Load a JPEG from a GD image resource.
     *
     * @param $gd
     * @return self
     */
    public static function fromResource($gd)
    {
        ob_start();
        imagejpeg($gd);

        $contents = ob_get_contents();
        ob_end_clean();

        return self::fromString($contents);
    }

    /**
     * Load a JPEG from a string.
     *
     * @param $string
     * @return self
     */
    public static function fromString($string)
    {
        $stream = fopen('php://temp', 'r+');
        fwrite($stream, $string);
        rewind($stream);

        return self::fromStream($stream);
    }

    /**
     * Load a JPEG from an Imagick instance.
     *
     * @param \Imagick $imagick
     *
     * @return JPEG
     */
    public static function fromImagick(\Imagick $imagick)
    {
        $imagick->setImageFormat('jpg');
        return self::fromString($imagick->getImageBlob());
    }

    /**
     * Load a JPEG from a stream.
     *
     * @param resource $fileHandle
     * @param string   $filename
     *
     * @return self
     * @throws \Exception
     */
    public static function fromStream($fileHandle, $filename = null)
    {
        try {
            // Read the first two characters
            $data = fread($fileHandle, 2);

            // Check that the first two characters are 0xFF 0xDA (SOI - Start of image)
            if ($data !== self::SOI) {
                throw new \Exception('Could not find SOI, invalid JPEG file.');
            }

            // Read the next two characters
            $data = fread($fileHandle, 2);

            // Check that the third character is 0xFF (Start of first segment header)
            if ($data[0] != "\xFF") {
                throw new \Exception('No start of segment header character, JPEG probably corrupted.');
            }

            $segments = [];
            $imageData = null;

            // Cycle through the file until, either an EOI (End of image) marker is hit or end of file is hit
            while (($data[1] != "\xD9") && (!feof($fileHandle))) {
                // Found a segment to look at.
                // Check that the segment marker is not a restart marker, restart markers don't have size or data
                if ((ord($data[1]) < 0xD0) || (ord($data[1]) > 0xD7)) {
                    $decodedSize = unpack('nsize', fread($fileHandle, 2)); // find segment size

                    $segmentStart = ftell($fileHandle); // segment start position
                    $segmentData = fread($fileHandle, $decodedSize['size'] - 2); // read segment data
                    $segmentType = ord($data[1]);

                    $segments[] = new JPEG\Segment($segmentType, $segmentStart, $segmentData);
                }

                // If this is a SOS (Start Of Scan) segment, then there is no more header data, the image data follows
                if ($data[1] == "\xDA") {
                    // read the rest of the file, reading 1mb at a time until EOF
                    $compressedData = '';
                    do {
                        $compressedData .= fread($fileHandle, 1048576);
                    } while (!feof($fileHandle));

                    // Strip off EOI and anything after
                    $eoiPos = strpos($compressedData, "\xFF\xD9");
                    $imageData = substr($compressedData, 0, $eoiPos);

                    break; // exit loop as no more headers available.
                } else {
                    // Not an SOS - Read the next two bytes - should be the segment marker for the next segment
                    $data = fread($fileHandle, 2);

                    // Check that the first byte of the two is 0xFF as it should be for a marker
                    if ($data[0] != "\xFF") {
                        throw new \Exception('No FF found, JPEG probably corrupted.');
                    }
                }
            }

            return new self($imageData, $segments, $filename);

        } finally {
            fclose($fileHandle);
        }

        return false;
    }

    /**
     * Load a JPEG from a file.
     *
     * @param $filename
     *
     * @return self
     * @throws \Exception
     */
    public static function fromFile($filename)
    {
        $fileHandle = @fopen($filename, 'rb');

        if (!$fileHandle) {
            throw new \Exception(sprintf('Could not open file %s', $filename));
        }

        return self::fromStream($fileHandle, $filename);
    }

    /**
     * @return void
     */
    private function insertXmpSegment()
    {
        $xmp = $this->getXmp();

        if (!$xmp) {
            return;
        }

        $renderSegment = function (Xmp $xmp) {
            return "http://ns.adobe.com/xap/1.0/\x00" . $xmp->getString();
        };

        foreach ($this->getSegmentsByName('APP1') as $segment) {
            // And if it has the Adobe XMP/RDF label (http://ns.adobe.com/xap/1.0/\x00) ,
            if (strncmp($segment->getData(), "http://ns.adobe.com/xap/1.0/\x00", 29) == 0) {
                $segment->setData($renderSegment($xmp));
                return;
            }
        }

        // No pre-existing XMP/RDF found - insert a new one after any pre-existing APP0 or APP1 blocks
        $i = 0;

        // Loop until a block is found that isn't an APP0 or APP1
        while (($this->segments[$i]->getName() == 'APP0') || ($this->segments[$i]->getName() == 'APP1')) {
            $i++;
        }

        // Insert a new XMP/RDF APP1 segment at the specified point.
        $segment = new JPEG\Segment(0xE1, 0, $renderSegment($xmp));

        array_splice($this->segments, $i, 0, [$segment]);
    }

    /**
     * @return Xmp
     */
    public function getXmp()
    {
        if (!$this->xmp) {
            $possible = $this->getSegmentsByName('APP1');
            $xmpData = null;

            foreach ($possible as $segment) {
                $data = $segment->getData();

                if (0 === strncmp($data, "http://ns.adobe.com/xap/1.0/\x00", 29)) {
                    $xmpData = substr($data, 29);
                    break;
                }
            }
            $this->xmp = new Xmp($xmpData);
        }

        return $this->xmp;
    }

    /**
     * @return Exif
     */
    public function getExif()
    {
        // TODO: Implement getExif() method.
    }

    /**
     * @return Iptc
     */
    public function getIptc()
    {
        // TODO: Implement getIptc() method.
    }
}
