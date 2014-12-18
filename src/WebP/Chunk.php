<?php
namespace CSD\Image\WebP;

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
        $length = $this->getLength();
        $data = $this->data;

        // pad data with null byte if length is odd
        if ($length & 1) {
            $data .= "\x00";
        }

        return $this->type . pack('V', $length) . $data;
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
