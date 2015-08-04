<?php
namespace CSD\Image\Format\WebP;

/**
 * @author Daniel Chesterton <daniel@chestertondevelopment.com>
 */
class VP8XChunk extends Chunk
{
    private $hasXmp;

    /**
     * @param string $data
     */
    public function __construct($data)
    {
        parent::__construct('VP8X', $data);

        var_dump($this->hasXmp());
        var_dump($this->hasExif());
    }

    private function hasFeature($n)
    {
        $features = unpack('c', $this->data[0]);

        return (bool) (($features[1] >> $n-1) & 1);
    }

    public function hasXmp()
    {
        return $this->hasFeature(3);
    }

    public function hasExif()
    {

        $features = unpack('c', $this->data[0]);

        $byte = $features[1] & 4;

        var_dump($byte, $features[1]);


        $this->data[0] = pack('c', $byte);


        return $this->hasFeature(4);
    }
}
