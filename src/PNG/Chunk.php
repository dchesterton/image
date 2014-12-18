<?php
namespace CSD\Image\PNG;

/**
 * @author Daniel Chesterton <daniel@chestertondevelopment.com>
 */
class Chunk
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $data;

    /**
     * @param string $type
     * @param string $data
     */
    public function __construct($type, $data)
    {
        $this->type = $type;
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getLength()
    {
        return strlen($this->data);
    }

    /**
     * @return string
     */
    public function getChunk()
    {
        return pack('Na4', $this->getLength(), $this->type) . $this->data . $this->getCrc();
    }

    /**
     * @return string
     */
    public function getCrc()
    {
        $crc = crc32($this->type . $this->data);
        $hex = str_pad(dechex($crc), 8, '0', STR_PAD_LEFT); // pad to 4 bytes

        return hex2bin($hex);
    }

    /**
     * @param string $data
     *
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }
}
