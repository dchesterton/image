<?php
/**
 * This file is part of the Photo Store package.
 *
 * (c) Daniel Chesterton <daniel@chestertondevelopment.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CSD\Photo\Metadata;

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
     * Constructor.
     *
     * @param array $data An array of EXIF data from exif_read_data.
     */
    public function __construct($data = null)
    {
        $this->data = $data;
    }

    /**
     * Load EXIF data from an image.
     *
     * @param string $path The path of the image.
     *
     * @throws \Exception
     *
     * @return ExifReader
     */
    public static function fromFile($path)
    {
        if (!function_exists('exif_read_data')) {
            throw new \Exception('exif_read_data function is required.');
        }

        $data = exif_read_data($path, null, true) ?: array();
        return new self($data);
    }

    /**
     * Returns data for the given EXIF field. Returns null if the field does not exist.
     *
     * @param string $field The field to return.
     *
     * @return string|null
     */
    public function get($field)
    {
        if (is_array($this->data) && isset($this->data[$field])) {
            if ($value = $this->data[$field]) {
                return $value;
            }
        }

        return null;
    }

    /**
     * Get the EXIF data.
     *
     * @return array
     */
    public function all()
    {
        return $this->data;
    }

    /**
     * Get ISO.
     *
     * @return integer|null
     */
    public function getISO()
    {
        if (isset($this->data['EXIF']['ISOSpeedRatings'])) {
            return $this->data['EXIF']['ISOSpeedRatings'];
        }
        return null;
    }
    
    /**
     * Get aperture.
     *
     * @return float|null
     */
    public function getAperture()
    {
        if (isset($this->data['Composite']['Aperture'])) {
            return (float) $this->data['Composite']['Aperture'];
        } elseif (isset($this->data['EXIF']['FNumber'])) {
            return $this->fractionToDecimal($this->data['EXIF']['FNumber']);
        } elseif (isset($this->data['EXIF']['ApertureValue'])) {
            return $this->fractionToDecimal($this->data['EXIF']['ApertureValue']);
        } elseif (isset($this->data['XMP']['FNumber'])) {
            return (float) $this->data['XMP']['FNumber'];
        }
        return null;
    }

    /**
     * Get exposure program, e.g. 'Aperture Priority'.
     *
     * @return string|null
     */
    public function getExposureProgram()
    {
        if (isset($this->data['EXIF']['ExposureProgram'])) {
            $mode = $this->data['EXIF']['ExposureProgram'];
            $modes = array(
                1 => 'Manual',
                2 => 'Program',
                3 => 'Aperture Priority',
                4 => 'Shutter Priority',
                5 => 'Creative',
                6 => 'Action',
                7 => 'Portrait',
                8 => 'Landscape'
            );

            if (isset($modes[$mode])) {
                return $modes[$mode];
            }
        }
        return null;
    }

    /**
     * Get white balance.
     *
     * @return string|null
     */
    public function getWhiteBalance()
    {
        if (isset($this->data['EXIF']['WhiteBalance'])) {
            $mode = $this->data['EXIF']['WhiteBalance'];
            $modes = array(
                0 => 'Auto',
                1 => 'Daylight',
                2 => 'Cloudy',
                3 => 'Tungsten',
                4 => 'Fluorescent',
                5 => 'Flash',
                6 => 'Custom',
                7 => 'Black & White',
                8 => 'Shade',
                9 => 'Manual Temperature (Kelvin)',
                10 => 'PC Set1',
                11 => 'PC Set2',
                12 => 'PC Set3',
                14 => 'Daylight Fluorescent',
                15 => 'Custom 1',
                16 => 'Custom 2',
                17 => 'Underwater',
                18 => 'Custom 3',
                19 => 'Custom 4',
                20 => 'PC Set4',
                21 => 'PC Set5'
            );

            if (isset($modes[$mode])) {
                return $modes[$mode];
            }
        }
        return null;
    }

    /**
     * Get exposure bias.
     *
     * @return integer|null
     */
    public function getExposureBias()
    {
        if (isset($this->data['EXIF']['ExposureBiasValue'])) {
            $value = $this->data['EXIF']['ExposureBiasValue'];

            if ($value == '0/1') {
                return 0;
            } else {
                return $value;
            }
        }
        return null;
    }

    /**
     * Get camera make.
     *
     * @return string
     */
    public function getMake()
    {
        if (isset($this->data['IFD0']['Make'])) {
            return trim($this->data['IFD0']['Make']);
        }
        return null;
    }

    /**
     * Get camera model.
     *
     * @return string|null
     */
    public function getModel()
    {
        if (isset($this->data['IFD0']['Model'])) {
            return trim($this->data['IFD0']['Model']);
        }
        return null;
    }

    /**
     * Get flash mode.
     *
     * @return string|null
     */
    public function getFlashMode()
    {
        if (isset($this->data['EXIF']['Flash'])) {
            $mode = $this->data['EXIF']['Flash'];

            $modes = array(
                0  => 'Flash did not fire',
                1  => 'Flash fired',
                5  => 'Strobe return light not detected',
                7  => 'Strobe return light detected',
                9  => 'Flash fired, compulsory flash mode',
                13 => 'Flash fired, compulsory flash mode, return light not detected',
                15 => 'Flash fired, compulsory flash mode, return light detected',
                16 => 'Flash did not fire, compulsory flash suppression mode',
                24 => 'Flash did not fire, auto mode',
                25 => 'Flash fired, auto mode',
                29 => 'Flash fired, auto mode, return light not detected',
                31 => 'Flash fired, auto mode, return light detected',
                32 => 'No flash function',
                65 => 'Flash fired, red-eye reduction mode',
                69 => 'Flash fired, red-eye reduction mode, return light not detected',
                71 => 'Flash fired, red-eye reduction mode, return light detected',
                73 => 'Flash fired, compulsory flash mode, red-eye reduction mode',
                77 => 'Flash fired, compulsory flash mode, red-eye reduction mode, return light not detected',
                79 => 'Flash fired, compulsory flash mode, red-eye reduction mode, return light detected',
                89 => 'Flash fired, auto mode, red-eye reduction mode',
                93 => 'Flash fired, auto mode, return light not detected, red-eye reduction mode',
                95 => 'Flash fired, auto mode, return light detected, red-eye reduction mode'
            );

            if (isset($modes[$mode])) {
                return $modes[$mode];
            }
        }
        return null;
    }

    /**
     * Get camera make and model.
     *
     * @return string
     */
    public function getCamera()
    {
        $make = $this->getMake();
        $model = $this->getModel();

        if ($make == null) {
            return $model;
        }
        if ($model == null) {
            return $make;
        }

        // if make appears at the beginning of model string, return model rather than duplicating
        if (strpos($model, $make) === 0) {
            return $model;
        }

        // special case for Nikon cameras. Change Nikon Corporation Nikon D3 to Nikon D3
        if (strtoupper($make) == 'NIKON CORPORATION' && stripos($model, 'NIKON') === 0) {
            return $model;
        }

        return trim($make . ' ' . $model);
    }

    /**
     * Get shutter speed of photo.
     *
     * @return string|null
     */
    public function getShutterSpeed()
    {
        if (isset($this->data['EXIF']['ExposureTime'])) {
            return $this->fractionToDecimal($this->data['EXIF']['ExposureTime']);
        }
        return null;
    }

    /**
     * Get date and time photo was taken.
     *
     * @return string|null
     */
    public function getDateTime()
    {
        if (isset($this->data['DateTimeDigitized'])) {
            return $this->fractionToDecimal($this->data['DateTimeDigitized']);
        }
        return null;
    }

    /**
     * Get focal length in milimetres.
     *
     * @return float|null
     */
    public function getFocalLength()
    {
        if (isset($this->data['EXIF']['FocalLength'])) {
            return $this->fractionToDecimal($this->data['EXIF']['FocalLength']);
        }
        return null;
    }

    /**
     * Get lens model.
     *
     * @return string|null
     */
    public function getLensModel()
    {
        $lens = '';

        if (isset($this->data['EXIF']['UndefinedTag:0xA434'])) {
            $lens = trim($this->data['EXIF']['UndefinedTag:0xA434']);
        } elseif (isset($this->data['Composite']['LensID'])) {
            $lens = trim($this->data['Composite']['LensID']);
        }

        if ($lens && stripos($lens, 'Unknown') !== 0) {
            return $lens;
        }
        return null;
    }

    /**
     * Get software used.
     *
     * @return string|null
     */
    public function getSoftware()
    {
        $software = null;

        if (isset($this->data['EXIF']['Software'])) {
            $software = $this->data['EXIF']['Software'];
        } elseif (isset($this->data['XMP']['CreatorTool'])) {
            $software = $this->data['XMP']['CreatorTool'];
        }

        if ($software != null) {
            $software = trim($software);
            
            if (preg_match('#^v?[0-9\.]*$#', $software)) {
                $software = null;
            }
        }
        return $software;
    }

    /**
     * Get color mode.
     *
     * @return string|null
     */
    public function getColorMode()
    {
        if (isset($this->data['XMP']['ColorMode'])) {
            return trim($this->data['XMP']['ColorMode']);
        } elseif (isset($this->data['ICC_Profile']['ColorSpaceData'])) {
            return trim($this->data['ICC_Profile']['ColorSpaceData']);
        } elseif (isset($this->data['EXIF']['ColorSpace']) && $this->data['EXIF']['ColorSpace'] == 'sRGB') {
            return 'RGB';
        }
        return null;
    }

    /**
     * Get GPS coordinates.
     *
     * @return array|null 
     */
    public function getGPS()
    {
        if (isset($this->data['GPS']['GPSLatitudeRef'])
         && isset($this->data['GPS']['GPSLatitude'])
         && isset($this->data['GPS']['GPSLongitudeRef'])
         && isset($this->data['GPS']['GPSLongitude'])) {
            $latitude = $this->getGPSPart($this->data['GPS']['GPSLatitude'], $this->data['GPS']['GPSLatitudeRef']);
            $longitude = $this->getGPSPart($this->data['GPS']['GPSLongitude'], $this->data['GPS']['GPSLongitudeRef']);

            return array(
                'latitude' => $latitude,
                'longitude' => $longitude
            );
        }
        return null;
    }

    /**
     * Utility function to convert a fraction as a string into a float.
     *
     * @param string $string String to convert.
     *
     * @return float
     */
    protected function fractionToDecimal($string)
    {
        $result = explode('/', $string);

        if (count($result) == 2) {
            return (float) $result[0] / (float) $result[1];
        }
        return $string;
    }

    /**
     * Get GPS data from a coordinate.
     *
     * @param array  $exifCoord An array of values with degrees, minutes and seconds as keys.
     * @param string $ref       Reference.
     *
     * @return array An array of data about this GPS coordinate.
     */
    protected function getGPSPart($exifCoord, $ref)
    {
        $degrees = count($exifCoord) > 0? $this->fractionToDecimal($exifCoord[0]) : 0;
        $minutes = count($exifCoord) > 1? $this->fractionToDecimal($exifCoord[1]) : 0;
        $seconds = count($exifCoord) > 2? $this->fractionToDecimal($exifCoord[2]) : 0;

        // store human readable string
        $string = $degrees . '°' . $minutes . '′' . $seconds . '″' . $ref;

        // calculate the coordinates for use in maps etc.
        $flip = ($ref == 'W' || $ref == 'S')? -1 : 1;
        $coordinates = $flip * ($degrees + $minutes / 60 + $seconds / 3600);

        return array(
            'degrees' => $degrees,
            'minutes' => $minutes,
            'seconds' => $seconds,
            'reference' => $ref,
            'string' => $string,
            'coordinates' => $coordinates
        );
    }
}
