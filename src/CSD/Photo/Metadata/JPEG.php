<?php
namespace CSD\Photo\Metadata;

class JPEG
{

    public static function fromFile($filename)
    {
        // Attempt to open the jpeg file
        $fileHandle = @fopen($filename, 'rb');

        // Check if the file opened successfully
        if (!$fileHandle) {
            throw new \Exception('Could not open file ' . $filename);
        }

        try {
            // Read the first two characters
            $data = fread($fileHandle, 2);

            // Check that the first two characters are 0xFF 0xDA  (SOI - Start of image)
            if ($data != "\xFF\xD8") {
                throw new \Exception('Could not find SOI, invalid JPEG file.');
            }

            // Read the next two characters
            $data = fread($fileHandle, 2);

            // Check that the third character is 0xFF (Start of first segment header)
            if ($data[0] != "\xFF") {
                throw new \Exception('No start of segment header character, JPEG probably corrupted.');
            }

            // Cycle through the file until, one of: 1) an EOI (End of image) marker is hit,
            //                                       2) we have hit the compressed image data
            //                                       3) or end of file is hit

            $segments = array();

            while (($data[1] != "\xD9") && (!feof($fileHandle))) {
                // Found a segment to look at.
                // Check that the segment marker is not a restart marker, restart markers don't have size or data
                if ((ord($data[1]) < 0xD0) || (ord($data[1]) > 0xD7)) {
                    $decodedSize = unpack("nsize", fread($fileHandle, 2)); // find segment size

                    $segmentStart = ftell($fileHandle); // segment start position
                    $segmentData = fread($fileHandle, $decodedSize['size'] - 2); // read segment data
                    $segmentType = ord($data[1]);

                    $segments[] = new JPEGSegment($segmentType, $segmentStart, $segmentData);
                }

                // If this is a SOS (Start Of Scan) segment, then there is no more header data, the image data follows
                if ($data[1] == "\xDA") {
                    break; // exit loop as no more headers available.
                } else {
                    // Not an SOS - Read the next two bytes - should be the segment marker for the next segment
                    $data = fread($fileHandle, 2);

                    // Check that the first byte of the two is 0xFF as it should be for a marker
                    if ($data[0] != "\xFF") {
                        // NO FF found - close file and return - JPEG is probably corrupted
                        throw new \Exception('No FF found, JPEG probably corrupted.');
                    }
                }
            }

            fclose($fileHandle);
            return new self($filename, $segments);

        } catch (\Exception $e) {
            // close the file, then rethrow the exception
            fclose($fileHandle);
            throw $e;
        }
    }

    /**
     * @var JPEGSegment[]
     */
    private $segments;

    /**
     * @var string
     */
    private $filename;

    /**
     * @param $filename string
     * @param $segments JPEGSegment[]
     */
    public function __construct($filename, $segments)
    {
        $this->filename = $filename;
        $this->segments = $segments;
    }

    /**
     * @return JPEGSegment[]
     */
    public function getSegments()
    {
        return $this->segments;
    }

    /**
     * @param $name
     *
     * @return JPEGSegment[]
     */
    public function getSegmentsByName($name)
    {
        $segments = array();

        foreach ($this->segments as $segment) {
            if ($segment->getName() == $name) {
                $segments[] = $segment;
            }
        }

        return $segments;
    }

    public function setXmp($xmpData)
    {
        $segments = $this->getSegmentsByName('APP1');

        foreach ($segments as $segment) {
            $data = $segment->getData();

            // And if it has the Adobe XMP/RDF label (http://ns.adobe.com/xap/1.0/\x00) ,
            if (strncmp($data, "http://ns.adobe.com/xap/1.0/\x00", 29) == 0) {
                $segment->setData("http://ns.adobe.com/xap/1.0/\x00" . $xmpData);
                return true;
            }
        }

        // No pre-existing XMP/RDF found - insert a new one after any pre-existing APP0 or APP1 blocks
        $i = 0;

        // Loop until a block is found that isn't an APP0 or APP1
        while (($this->segments[$i]->getName() == "APP0") || ($this->segments[$i]->getName() == "APP1")) {
            $i++;
        }

        // Insert a new XMP/RDF APP1 segment at the specified point.
        $segment = new JPEGSegment(0xE1, 0, "http://ns.adobe.com/xap/1.0/\x00" . $xmpData);

        array_splice($this->segments, $i, 0, array($segment));
    }

    public function save($filename = null)
    {
        $filename = $filename?: $this->filename;

        // extract the compressed image data from the old file
        $imageData = get_jpeg_image_data($this->filename);

        if (!$imageData) {
            throw new \Exception('Could not get image data from ' . $this->filename);
        }

        // check the headers are not too large
        foreach ($this->segments as $segment) {
            if (strlen($segment->getData()) > 0xfffd) {
                throw new \Exception('Header ' . $segment->getType() . ' is too large to fit in JPEG segment');
            }
        }

        // Attempt to open the new jpeg file
        $handle = @fopen($filename, 'wb');

        // Check if the file opened successfully
        if (!$handle) {
            throw new \Exception('Could not open file ' . $filename);
        }

        // Write SOI
        fwrite($handle, "\xFF\xD8");

        // Cycle through new headers, writing them to the new file
        foreach ($this->segments as $segment) {
            fwrite($handle, sprintf("\xFF%c", $segment->getType())); // segment marker
            fwrite($handle, pack("n", strlen($segment->getData()) + 2)); // segment size
            fwrite($handle, $segment->getData()); // segment data
        }

        // Write the compressed image data
        fwrite($handle, $imageData);

        // Write EOI
        fwrite($handle, "\xFF\xD9");

        // Close File
        fclose($handle);
    }
}


function get_jpeg_image_data( $filename )
{
    // Attempt to open the jpeg file
    $filehnd = @fopen($filename, 'rb');

    // Check if the file opened successfully
    if ( ! $filehnd  )
    {
        // Could't open the file - exit
        return FALSE;
    }


    // Read the first two characters
    $data = fread( $filehnd, 2 );

    // Check that the first two characters are 0xFF 0xDA  (SOI - Start of image)
    if ( $data != "\xFF\xD8" )
    {
        // No SOI (FF D8) at start of file - close file and return;
        fclose($filehnd);
        return FALSE;
    }



    // Read the third character
    $data = fread( $filehnd, 2 );

    // Check that the third character is 0xFF (Start of first segment header)
    if ( $data{0} != "\xFF" )
    {
        // NO FF found - close file and return
        fclose($filehnd);
        return;
    }

    // Flag that we havent yet hit the compressed image data
    $hit_compressed_image_data = FALSE;


    // Cycle through the file until, one of: 1) an EOI (End of image) marker is hit,
    //                                       2) we have hit the compressed image data (no more headers are allowed after data)
    //                                       3) or end of file is hit

    while ( ( $data{1} != "\xD9" ) && (! $hit_compressed_image_data) && ( ! feof( $filehnd ) ))
    {
        // Found a segment to look at.
        // Check that the segment marker is not a Restart marker - restart markers don't have size or data after them
        if (  ( ord($data{1}) < 0xD0 ) || ( ord($data{1}) > 0xD7 ) )
        {
            // Segment isn't a Restart marker
            // Read the next two bytes (size)
            $sizestr = fread( $filehnd, 2 );

            // convert the size bytes to an integer
            $decodedsize = unpack ("nsize", $sizestr);

            // Read the segment data with length indicated by the previously read size
            $segdata = fread( $filehnd, $decodedsize['size'] - 2 );
        }

        // If this is a SOS (Start Of Scan) segment, then there is no more header data - the compressed image data follows
        if ( $data{1} == "\xDA" )
        {
            // Flag that we have hit the compressed image data - exit loop after reading the data
            $hit_compressed_image_data = TRUE;

            // read the rest of the file in
            // Can't use the filesize function to work out
            // how much to read, as it won't work for files being read by http or ftp
            // So instead read 1Mb at a time till EOF

            $compressed_data = "";
            do
            {
                $compressed_data .= fread( $filehnd, 1048576 );
            } while( ! feof( $filehnd ) );

            // Strip off EOI and anything after
            $EOI_pos = strpos( $compressed_data, "\xFF\xD9" );
            $compressed_data = substr( $compressed_data, 0, $EOI_pos );
        }
        else
        {
            // Not an SOS - Read the next two bytes - should be the segment marker for the next segment
            $data = fread( $filehnd, 2 );

            // Check that the first byte of the two is 0xFF as it should be for a marker
            if ( $data{0} != "\xFF" )
            {
                // Problem - NO FF foundclose file and return";
                fclose($filehnd);
                return;
            }
        }
    }

    // Close File
    fclose($filehnd);

    // Return the compressed data if it was found
    if ( $hit_compressed_image_data )
    {
        return $compressed_data;
    }
    else
    {
        return FALSE;
    }
}

