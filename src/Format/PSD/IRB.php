<?php

namespace CSD\Image\Format\PSD;

/**
 * Class for Image Resource Blocks.
 *
 * @author Joel Bernerman <joel.bernerman@aller.se>
 */
class IRB {

    protected $data;
    protected $resourceId;
    protected $pascalString;

    /**
     * @param string $type
     * @param string $data
     */
    public function __construct($resourceId, $pascalString, $data)
    {
        $this->resourceId = $resourceId;
        $this->data = $data;
        $this->pascalString = $pascalString;
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
    public function getResourceId()
    {
        return $this->resourceId;
    }

    /**
     * @return string
     */
    public function getPascalString()
    {
        return $this->pascalString;
    }

    /**
     * @return int
     */
    public function getLength()
    {
        return strlen($this->data);
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