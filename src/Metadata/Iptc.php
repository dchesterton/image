<?php
namespace CSD\Photo\Metadata;

/**
 * Class to read IPTC metadata from an image.
 *
 * @author Daniel Chesterton <daniel@chestertondevelopment.com>
 */
class Iptc
{
    /**
     * @var array
     */
    private $fields = [
        'headline'               => '2#105',
        'caption'                => '2#120',
        'location'               => '2#092',
        'city'                   => '2#090',
        'state'                  => '2#095',
        'country'                => '2#101',
        'countryCode'            => '2#100',
        'photographerName'       => '2#080',
        'credit'                 => '2#110',
        'photographerTitle'      => '2#085',
        'source'                 => '2#115',
        'copyright'              => '2#116',
        'objectName'             => '2#005',
        'captionWriters'         => '2#122',
        'instructions'           => '2#040',
        'category'               => '2#015',
        'supplementalCategories' => '2#020',
        'transmissionReference'  => '2#103',
        'urgency'                => '2#010',
        'keywords'               => '2#025',
        'date'                   => '2#055',
        'time'                   => '2#060',
    ];

    /**
     * Array to hold the IPTC metadata.
     *
     * @var array
     */
    private $data;

    /**
     * @var bool
     */
    private $hasChanges = false;

    /**
     * Constructor.
     *
     * @param array $data
     */
    public function __construct($data = [])
    {
        $this->data = $data;
    }

    /**
     * Checks whether the given string looks like UTF-8 content.
     *
     * @param string $str String to check for UTF-8.
     *
     * @see https://core.trac.wordpress.org/browser/tags/3.8.1/src/wp-includes/formatting.php
     * @return boolean
     */
    private static function seemsUtf8($str)
    {
        $length = strlen($str);

        for ($i = 0; $i < $length; $i++) {
            $c = ord($str[$i]);
            if ($c < 0x80) {
                $n = 0; // 0bbbbbbb
            } elseif (($c & 0xE0) == 0xC0) {
                $n = 1; // 110bbbbb
            } elseif (($c & 0xF0) == 0xE0) {
                $n = 2; // 1110bbbb
            } elseif (($c & 0xF8) == 0xF0) {
                $n = 3; // 11110bbb
            } elseif (($c & 0xFC) == 0xF8) {
                $n = 4; // 111110bb
            } elseif (($c & 0xFE) == 0xFC) {
                $n = 5; // 1111110b
            } else {
                return false; // Does not match any model
            }
            for ($j = 0; $j < $n; $j++) { # // bytes matching 10bbbbbb follow ?
                if ((++$i == $length) || ((ord($str[$i]) & 0xC0) != 0x80)) {
                    return false;
                }
            }
        }
        return true;
    }
    function get_Photoshop_IRB( $jpeg_header_data )
    {
        // Photoshop Image Resource blocks can span several JPEG APP13 segments, so we need to join them up if there are more than one
        $joined_IRB = "";


        //Cycle through the header segments
        for( $i = 0; $i < count( $jpeg_header_data ); $i++ )
        {
            // If we find an APP13 header,
            if ( strcmp ( $jpeg_header_data[$i]['SegName'], "APP13" ) == 0 )
            {
                // And if it has the photoshop label,
                if( strncmp ( $jpeg_header_data[$i]['SegData'], "Photoshop 3.0\x00", 14) == 0 )
                {
                    // join it to the other previous IRB data
                    $joined_IRB .= substr ( $jpeg_header_data[$i]['SegData'], 14 );
                }
            }
        }

        // If there was some Photoshop IRB information found,
        if ( $joined_IRB != "" )
        {
            // Found a Photoshop Image Resource Block - extract it.
            // Change: Moved code into unpack_Photoshop_IRB_Data to allow TIFF reading as of 1.11
            return unpack_Photoshop_IRB_Data( $joined_IRB );

        }
        else
        {
            // No Photoshop IRB found
            return FALSE;
        }

    }

    public static function  fromJPEG(JPEG $jpeg)
    {
        return new self(array());
        $segments = $jpeg->getSegmentsByName('APP13');

        $irb = '';

        // loop through APP13 segments
        foreach ($segments as $segment) {
            $data = $segment->getData();

            // And if it has the photoshop label,
            if (strncmp($data, "Photoshop 3.0\x00", 14) == 0) {
                // join it to the other previous IRB data
                $irb .= substr($data, 14);
            }
        }

        $dataArray = array();

        if ($irb) {
            $pos = 0;

            // Cycle through the IRB and extract its records
            // Records are started with 8BIM, so cycle until no more instances of 8BIM can be found
            while (($pos < strlen($irb)) && (($pos = strpos($irb, "8BIM", $pos)) !== false)) {
                $pos += 4; // skip over the 8BIM characters
                $id = ord($irb[$pos]) * 256 + ord($irb[$pos + 1]); // next two characters are record ID

                $pos += 2; // skip the position over the two record ID characters

                // Next comes a Record Name - usually not used, but it should be a null terminated string,
                // padded with 0x00 to be an even length
                $namestartpos = $pos;

                // Change: Fixed processing of embedded resource names, as of revision 1.10

                // NOTE: Photoshop does not process resource names according to the standard :
                // "Adobe Photoshop 6.0 File Formats Specification, Version 6.0, Release 2, November 2000"
                //
                // The resource name is actually formatted as follows:
                // One byte name length, followed by the null terminated ascii name string.
                // The field is then padded with a Null character if required, to ensure that the
                // total length of the name length and name is even.

                // Name - process it
                // Get the length
                $nameLength = ord($irb[$namestartpos]);

                // Total length of name and length info must be even, hence name length must be odd
                // Check if the name length is even,
                if ($nameLength % 2 == 0) {
                    $nameLength++; // add one to length to make it odd
                }
                // Extract the name
                $resembeddedname = trim(substr($irb, $namestartpos + 1, $nameLength));
                $pos += $nameLength + 1;

                // Next is a four byte size field indicating the size in bytes of the record's data  - MSB first
                $dataSize = ord($irb[$pos]) * 16777216 + ord($irb[$pos + 1]) * 65536 +
                    ord($irb[$pos + 2]) * 256 + ord($irb[$pos + 3]);

                $pos += 4;

                // The record is stored padded with 0x00 characters to make the size even, so we need to calculate
                // the stored size
                $storedSize = $dataSize + ($dataSize % 2);

                $resdata = substr($irb, $pos, $dataSize);

                // Get the description for this resource
                // Check if this is a Path information Resource, since they have a range of ID's
                if (($id >= 0x07D0 ) && ($id <= 0x0BB6)) {
                    $ResDesc = "ID Info : Path Information (saved paths).";
                } else {
                    /*
                    if (array_key_exists($id, $GLOBALS[ "Photoshop_ID_Descriptions" ])) {
                        $ResDesc = $GLOBALS[ "Photoshop_ID_Descriptions" ][ $id ];
                    }
                    else
                    {*/
                    $ResDesc = "";
                    //}
                }
                /*
                                // Get the Name of the Resource
                                if ( array_key_exists( $id, $GLOBALS[ "Photoshop_ID_Names" ] ) )
                                {
                                    $ResName = $GLOBALS['Photoshop_ID_Names'][ $id ];
                                }
                                else
                                {*/
                $ResName = "";
                //}

                // Store the Resource in the array to be returned
                $dataArray[] = ["ResID" => $id,
                    "ResName" => $ResName,
                    "ResDesc" => $ResDesc,
                    "ResEmbeddedName" => $resembeddedname,
                    "ResData" => $resdata ];

                // Jump over the data to the next record
                $pos += $storedSize;
            }
        }

        //var_dump($dataArray);

        return new self($dataArray);
    }

    /**
     * Load IPTC data from an image.
     *
     * @param string $path The path of the image.
     *
     * @return self
     */
    public static function fromFile($path)
    {
        @getimagesize($path, $info);

        $iptc = (isset($info['APP13']))? iptcparse($info['APP13']): [];
        $data = [];

        foreach ($iptc as $field => $values) {
            // convert values to UTF-8 if needed
            for ($i = 0; $i < count($values); $i++) {
                if (!self::seemsUtf8($values[$i])) {
                    $values[$i] = utf8_decode($values[$i]);
                }
            }
            $data[$field] = $values;
        }

        return new self($data);
    }

    /**
     * Returns data for the given IPTC field. Returns null if the field does not exist.
     *
     * @param string  $field  The field to return.
     * @param boolean $single Return the first value or all values in field. Defaults to single (true).
     *
     * @return string|null
     */
    private function get($field, $single = true)
    {
        $code = $this->fields[$field];

        if (isset($this->data[$code])) {
            return ($single)? $this->data[$code][0]: $this->data[$code];
        }

        return null;
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return $this
     */
    private function set($field, $value)
    {
        $code = $this->fields[$field];

        $this->data[$code] = $value;
        $this->hasChanges = true;

        return $this;
    }

    /**
     * @return string
     */
    public function getHeadline()
    {
        return $this->get('headline');
    }

    /**
     * @param string $headline
     *
     * @return $this
     */
    public function setHeadline($headline)
    {
        return $this->set('headline', [$headline]);
    }

    /**
     * @return string
     */
    public function getCaption()
    {
        return $this->get('caption');
    }

    /**
     * @param $caption
     *
     * @return $this
     */
    public function setCaption($caption)
    {
        return $this->set('caption', [$caption]);
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->get('location');
    }

    /**
     * @param $location
     *
     * @return $this
     */
    public function setLocation($location)
    {
        return $this->set('location', [$location]);
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->get('city');
    }

    /**
     * @param $city
     *
     * @return $this
     */
    public function setCity($city)
    {
        return $this->set('city', [$city]);
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->get('state');
    }

    /**
     * @param $state
     *
     * @return $this
     */
    public function setState($state)
    {
        return $this->set('state', [$state]);
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->get('country');
    }

    /**
     * @param $country
     *
     * @return $this
     */
    public function setCountry($country)
    {
        return $this->set('country', [$country]);
    }

    /**
     * @return string
     */
    public function getCountryCode()
    {
        return $this->get('countryCode');
    }

    /**
     * @param $countryCode
     *
     * @return $this
     */
    public function setCountryCode($countryCode)
    {
        return $this->set('countryCode', [$countryCode]);
    }

    /**
     * @return string
     */
    public function getPhotographerName()
    {
        return $this->get('photographerName');
    }

    /**
     * @param $photographerName
     *
     * @return $this
     */
    public function setPhotographerName($photographerName)
    {
        return $this->set('photographerName', [$photographerName]);
    }

    /**
     * @return string
     */
    public function getCredit()
    {
        return $this->get('credit');
    }

    /**
     * @param $credit
     *
     * @return $this
     */
    public function setCredit($credit)
    {
        return $this->set('credit', [$credit]);
    }

    /**
     * @return string
     */
    public function getPhotographerTitle()
    {
        return $this->get('photographerTitle');
    }

    /**
     * @param $photographerTitle
     *
     * @return $this
     */
    public function setPhotographerTitle($photographerTitle)
    {
        return $this->set('photographerTitle', [$photographerTitle]);
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->get('source');
    }

    /**
     * @param $source
     *
     * @return $this
     */
    public function setSource($source)
    {
        return $this->set('source', [$source]);
    }

    /**
     * @return string
     */
    public function getCopyright()
    {
        return $this->get('copyright');
    }

    /**
     * @param $copyright
     *
     * @return $this
     */
    public function setCopyright($copyright)
    {
        return $this->set('copyright', [$copyright]);
    }

    /**
     * @return string
     */
    public function getObjectName()
    {
        return $this->get('objectName');
    }

    /**
     * @param $objectName
     *
     * @return $this
     */
    public function setObjectName($objectName)
    {
        return $this->set('objectName', [$objectName]);
    }

    /**
     * @return string
     */
    public function getCaptionWriters()
    {
        return $this->get('captionWriters');
    }

    /**
     * @param $captionWriters
     *
     * @return $this
     */
    public function setCaptionWriters($captionWriters)
    {
        return $this->set('captionWriters', [$captionWriters]);
    }

    /**
     * @return string
     */
    public function getInstructions()
    {
        return $this->get('instructions');
    }

    /**
     * @param $instructions
     *
     * @return $this
     */
    public function setInstructions($instructions)
    {
        return $this->set('instructions', [$instructions]);
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->get('category');
    }

    /**
     * @param $category
     *
     * @return $this
     */
    public function setCategory($category)
    {
        return $this->set('category', [$category]);
    }

    /**
     * @return string
     */
    public function getSupplementalCategories()
    {
        return $this->get('supplementalCategories');
    }

    /**
     * @param $supplementalCategories
     *
     * @return $this
     */
    public function setSupplementalCategories($supplementalCategories)
    {
        return $this->set('supplementalCategories', [$supplementalCategories]);
    }

    /**
     * @return string
     */
    public function getTransmissionReference()
    {
        return $this->get('transmissionReference');
    }

    /**
     * @param $transmissionReference
     *
     * @return $this
     */
    public function setTransmissionReference($transmissionReference)
    {
        return $this->set('transmissionReference', [$transmissionReference]);
    }

    /**
     * @return string
     */
    public function getUrgency()
    {
        return $this->get('urgency');
    }

    /**
     * @param $urgency
     *
     * @return $this
     */
    public function setUrgency($urgency)
    {
        return $this->set('urgency', [$urgency]);
    }

    /**
     * @return string
     */
    public function getKeywords()
    {
        return $this->get('keywords', false);
    }

    /**
     * @param $keywords
     *
     * @return $this
     */
    public function setKeywords($keywords)
    {
        return $this->set('keywords', $keywords);
    }

    /**
     * @return \DateTime|null
     */
    public function getDateCreated()
    {
        $date = $this->get('date');
        $time = $this->get('time');

        if ($date && $time) {
            return new \DateTime($date . ' ' . $time);
        }
        return null;
    }

    /**
     * @param \DateTime $date
     *
     * @return $this
     */
    public function setDateCreated(\DateTime $date)
    {
        //$this->set('date', );
        //$this->set('time', );

        return $this;
    }

    /**
     * Get the IPTC data.
     *
     * @return array
     */
    public function all()
    {
        return $this->data;
    }

    /**
     * @return boolean
     */
    public function hasChanges()
    {
        return $this->hasChanges;
    }
}
