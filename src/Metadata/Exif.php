<?php
namespace CSD\Image\Metadata;

/**
 * Class to read EXIF metadata from an image.
 *
 * @author Daniel Chesterton <daniel@chestertondevelopment.com>
 */
class Exif
{
    /**
     * Array to hold the EXIF metadata.
     *
     * @var array
     */
    protected $data = [];

    /**
     * @var string
     */
    private $byteAlignment;

    /**
     * Constructor.
     *
     * @param array $data An array of EXIF data from exif_read_data.
     *
     * @throws \Exception
     */
    public function __construct($data = null)
    {
        $this->data = $data;

        $pos = 0;
        $byteAlignment = substr($data, $pos, 2);

        if ('II' !== $byteAlignment && 'MM' !== $byteAlignment) {
            throw new \Exception('Invalid EXIF byte alignment');
        }

        $this->byteAlignment = $byteAlignment;

        $pos += 2; // skip over byte alignment

        $id = substr($data, $pos, 2);

        if (42 !== $this->decodeIFDField($id, 3)) {
            throw new \Exception('Invalid EXIF file');
        }

        $pos += 2; // skip over $id field

        $offset = $this->decodeIFDField(substr($data, $pos, 4), 4);
        $pos = $offset;

        $numberOfTags = $this->decodeIFDField(substr($data, $pos, 2), 4);

        $pos += 2;

        for ($i = 0; $i < $numberOfTags; $i++) {

            $tagNumber = $this->decodeIFDField(substr($data, $pos, 2), 4);

            $pos += 2;

            $tagType = $this->decodeIFDField(substr($data, $pos, 2), 4);

            $pos += 2;

            $dataLength = $this->decodeIFDField(substr($data, $pos, 4), 4);

            $pos += 4;

            $dataLocation = $this->decodeIFDField(substr($data, $pos, 4), 4);

            $pos += 4;

            //var_dump($tagNumber, $dataLength, $dataLocation);
        }
    }

    private function decodeIFDField($field, $type)
    {
        $Byte_Align = $this->byteAlignment;

        if ($type == 1 || $type == 3 || $type == 4) {
            // This is a Unsigned Byte, Unsigned Short or Unsigned Long

            // Check the byte alignment to see if the bytes need tp be reversed
            if ('II' === $Byte_Align) {
                $field = strrev($field); // This is in Intel format, reverse it
            }

            // Convert the binary string to a number and return it
            return hexdec(bin2hex($field));
        } elseif ($type == 2) {
            // Null terminated ASCII string(s)
            // The input data may represent multiple strings, as the
            // 'count' field represents the total bytes, not the number of strings
            // Hence this should not be processed here, as it would have
            // to return multiple values instead of a single value

            echo "<p>Error - ASCII Strings should not be processed in get_IFD_Data_Type</p>\n";
            return "Error Should never get here"; //explode( "\x00", $input_data );
        } elseif ($type == 5) {
            // This is a Unsigned rational type

            if ('MM' === $Byte_Align) {
                // Motorola MSB first byte alignment
                return unpack('NNumerator/NDenominator', $field);
            } else {
                // Intel LSB first byte alignment
                return unpack('VNumerator/VDenominator', $field);
            }
        } elseif ($type == 6 || $type == 8 || $type == 9) {
            // This is a Signed Byte, Signed Short or Signed Long

            // Check the byte alignment to see if the bytes need to be reversed
            if ('II' === $Byte_Align) {
                $field = strrev($field); // Intel format, reverse the bytes
            }

            // Convert the binary string to an Unsigned number
            $value = hexdec(bin2hex($field));

            // Convert to signed number

            if (($type == 6) && ($value > 128)) {
                // number should be negative - make it negative
                return $value - 256;
            }

            if ($type == 8 && $value > 32767) {
                // number should be negative - make it negative
                return $value - 65536;
            }

            if ($type == 9 && $value > 2147483648) {
                // number should be negative - make it negative
                return $value - 4294967296;
            }

            // Return the signed number
            return $value;
        } elseif ($type == 7) {
            // Custom Data - Do nothing
            return $field;
        } elseif ($type == 10) {
            // This is a Signed Rational type

            // Signed Long not available with endian in unpack , use unsigned and convert

            // Check the byte alignment to see if the bytes need to be reversed
            if ('MM' === $Byte_Align) {
                // Motorola MSB first byte aligment
                $value = unpack('NNumerator/NDenominator', $field);
            } else {
                // Intel LSB first byte aligment
                $value = unpack('VNumerator/VDenominator', $field);
            }

            // Convert the numerator to a signed number
            // Check if it is above 2147483648 (i.e. a negative number)
            if ($value['Numerator'] > 2147483648) {
                // number is negative
                $value['Numerator'] -= 4294967296;
            }

            // Convert the denominator to a signed number
            // Check if it is above 2147483648 (i.e. a negative number)
            if ($value['Denominator'] > 2147483648) {
                // number is negative
                $value['Denominator'] -= 4294967296;
            }

            // Return the Signed Rational value
            return $value;
        }
        // Check if this is a Float type
        elseif ( $type == 11 )
        {
            // IEEE 754 Float
            // TODO - EXIF - IFD datatype Float not implemented yet
            return "FLOAT NOT IMPLEMENTED YET";
        }
        // Check if this is a Double type
        elseif ( $type == 12 )
        {
            // IEEE 754 Double
            // TODO - EXIF - IFD datatype Double not implemented yet
            return "DOUBLE NOT IMPLEMENTED YET";
        }
        else
        {
            // Error - Invalid Datatype
            return "Invalid Datatype $type";

        }

    }
}
