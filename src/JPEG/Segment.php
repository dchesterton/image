<?php
namespace CSD\Image\JPEG;

/**
 * @author Daniel Chesterton <daniel@chestertondevelopment.com>
 */
class Segment
{
    private static $segmentNames = [
        0xC0 => "SOF0",  0xC1 => "SOF1",  0xC2 => "SOF2",  0xC3 => "SOF4",
        0xC5 => "SOF5",  0xC6 => "SOF6",  0xC7 => "SOF7",  0xC8 => "JPG",
        0xC9 => "SOF9",  0xCA => "SOF10", 0xCB => "SOF11", 0xCD => "SOF13",
        0xCE => "SOF14", 0xCF => "SOF15",
        0xC4 => "DHT",   0xCC => "DAC",

        0xD0 => "RST0",  0xD1 => "RST1",  0xD2 => "RST2",  0xD3 => "RST3",
        0xD4 => "RST4",  0xD5 => "RST5",  0xD6 => "RST6",  0xD7 => "RST7",

        0xD8 => "SOI",   0xD9 => "EOI",   0xDA => "SOS",   0xDB => "DQT",
        0xDC => "DNL",   0xDD => "DRI",   0xDE => "DHP",   0xDF => "EXP",

        0xE0 => "APP0",  0xE1 => "APP1",  0xE2 => "APP2",  0xE3 => "APP3",
        0xE4 => "APP4",  0xE5 => "APP5",  0xE6 => "APP6",  0xE7 => "APP7",
        0xE8 => "APP8",  0xE9 => "APP9",  0xEA => "APP10", 0xEB => "APP11",
        0xEC => "APP12", 0xED => "APP13", 0xEE => "APP14", 0xEF => "APP15",

        0xF0 => "JPG0",  0xF1 => "JPG1",  0xF2 => "JPG2",  0xF3 => "JPG3",
        0xF4 => "JPG4",  0xF5 => "JPG5",  0xF6 => "JPG6",  0xF7 => "JPG7",
        0xF8 => "JPG8",  0xF9 => "JPG9",  0xFA => "JPG10", 0xFB => "JPG11",
        0xFC => "JPG12", 0xFD => "JPG13",

        0xFE => "COM",   0x01 => "TEM",   0x02 => "RES"
    ];

    private static $segmentDescriptions = [
        0xC0 => "Start Of Frame (SOF) Huffman  - Baseline DCT",
        0xC1 => "Start Of Frame (SOF) Huffman  - Extended sequential DCT",
        0xC2 => "Start Of Frame Huffman  - Progressive DCT (SOF2)",
        0xC3 => "Start Of Frame Huffman  - Spatial (sequential) lossless (SOF3)",
        0xC5 => "Start Of Frame Huffman  - Differential sequential DCT (SOF5)",
        0xC6 => "Start Of Frame Huffman  - Differential progressive DCT (SOF6)",
        0xC7 => "Start Of Frame Huffman  - Differential spatial (SOF7)",
        0xC8 => "Start Of Frame Arithmetic - Reserved for JPEG extensions (JPG)",
        0xC9 => "Start Of Frame Arithmetic - Extended sequential DCT (SOF9)",
        0xCA => "Start Of Frame Arithmetic - Progressive DCT (SOF10)",
        0xCB => "Start Of Frame Arithmetic - Spatial (sequential) lossless (SOF11)",
        0xCD => "Start Of Frame Arithmetic - Differential sequential DCT (SOF13)",
        0xCE => "Start Of Frame Arithmetic - Differential progressive DCT (SOF14)",
        0xCF => "Start Of Frame Arithmetic - Differential spatial (SOF15)",
        0xC4 => "Define Huffman Table(s) (DHT)",
        0xCC => "Define Arithmetic coding conditioning(s) (DAC)",

        0xD0 => "Restart with modulo 8 count 0 (RST0)",
        0xD1 => "Restart with modulo 8 count 1 (RST1)",
        0xD2 => "Restart with modulo 8 count 2 (RST2)",
        0xD3 => "Restart with modulo 8 count 3 (RST3)",
        0xD4 => "Restart with modulo 8 count 4 (RST4)",
        0xD5 => "Restart with modulo 8 count 5 (RST5)",
        0xD6 => "Restart with modulo 8 count 6 (RST6)",
        0xD7 => "Restart with modulo 8 count 7 (RST7)",

        0xD8 => "Start of Image (SOI)",
        0xD9 => "End of Image (EOI)",
        0xDA => "Start of Scan (SOS)",
        0xDB => "Define quantization Table(s) (DQT)",
        0xDC => "Define Number of Lines (DNL)",
        0xDD => "Define Restart Interval (DRI)",
        0xDE => "Define Hierarchical progression (DHP)",
        0xDF => "Expand Reference Component(s) (EXP)",

        0xE0 => "Application Field 0 (APP0) - usually JFIF or JFXX",
        0xE1 => "Application Field 1 (APP1) - usually EXIF or XMP/RDF",
        0xE2 => "Application Field 2 (APP2) - usually Flashpix",
        0xE3 => "Application Field 3 (APP3)",
        0xE4 => "Application Field 4 (APP4)",
        0xE5 => "Application Field 5 (APP5)",
        0xE6 => "Application Field 6 (APP6)",
        0xE7 => "Application Field 7 (APP7)",

        0xE8 => "Application Field 8 (APP8)",
        0xE9 => "Application Field 9 (APP9)",
        0xEA => "Application Field 10 (APP10)",
        0xEB => "Application Field 11 (APP11)",
        0xEC => "Application Field 12 (APP12) - usually [picture info]",
        0xED => "Application Field 13 (APP13) - usually photoshop IRB / IPTC",
        0xEE => "Application Field 14 (APP14)",
        0xEF => "Application Field 15 (APP15)",

        0xF0 => "Reserved for JPEG extensions (JPG0)",
        0xF1 => "Reserved for JPEG extensions (JPG1)",
        0xF2 => "Reserved for JPEG extensions (JPG2)",
        0xF3 => "Reserved for JPEG extensions (JPG3)",
        0xF4 => "Reserved for JPEG extensions (JPG4)",
        0xF5 => "Reserved for JPEG extensions (JPG5)",
        0xF6 => "Reserved for JPEG extensions (JPG6)",
        0xF7 => "Reserved for JPEG extensions (JPG7)",
        0xF8 => "Reserved for JPEG extensions (JPG8)",
        0xF9 => "Reserved for JPEG extensions (JPG9)",
        0xFA => "Reserved for JPEG extensions (JPG10)",
        0xFB => "Reserved for JPEG extensions (JPG11)",
        0xFC => "Reserved for JPEG extensions (JPG12)",
        0xFD => "Reserved for JPEG extensions (JPG13)",

        0xFE => "Comment (COM)",
        0x01 => "For temp private use arith code (TEM)",
        0x02 => "Reserved (RES)"
    ];

    private $data;
    private $start;
    private $type;


    public function __construct($type, $start, $data)
    {
        $this->type = $type;
        $this->start = $start;
        $this->data = $data;
    }

    /**
     * Get data.
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set data.
     *
     * @param mixed $data
     *
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Get start.
     *
     * @return mixed
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set start.
     *
     * @param mixed $start
     *
     * @return $this
     */
    public function setStart($start)
    {
        $this->start = $start;
        return $this;
    }

    /**
     * Get type.
     *
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type.
     *
     * @param mixed $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get segment type name.
     *
     * @return string
     */
    public function getName()
    {
        if (isset(self::$segmentNames[$this->type])) {
            return self::$segmentNames[$this->type];
        }
        return '';
    }

    /**
     * Get description of segment type.
     *
     * @return string
     */
    public function getDescription()
    {
        if (isset(self::$segmentDescriptions[$this->type])) {
            return self::$segmentDescriptions[$this->type];
        }
        return '';
    }

    public function isXmpSegment()
    {

    }
}
