<?php

namespace CSD\Image\Tests\Type;

use CSD\Image\Type\PNG\Chunk;

/**
 * @author Daniel Chesterton <daniel@chestertondevelopment.com>
 *
 * @coversDefaultClass \CSD\Image\Type\PNG\Chunk
 */
class ChunkTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getType
     * @covers ::getData
     */
    public function testGetters()
    {
        $chunk = new Chunk('iTXt', 'data');

        $this->assertEquals('iTXt', $chunk->getType());
        $this->assertEquals('data', $chunk->getData());
    }

    /**
     * @covers ::getLength
     */
    public function testGetLength()
    {
        $chunk = new Chunk('iTXt', 'data');

        $this->assertEquals(4, $chunk->getLength());
    }

    /**
     * @covers ::getCrc
     */
    public function testGetCRC()
    {
        $chunk = new Chunk('iTXt', 'data');

        $this->assertEquals('1d2449b7', bin2hex($chunk->getCrc()));
    }

    /**
     * @covers ::getChunk
     */
    public function testGetChunk()
    {
        $chunk = new Chunk('iTXt', 'data');

        $this->assertEquals('0000000469545874646174611d2449b7', bin2hex($chunk->getChunk()));
    }

    /**
     * @covers ::setData
     */
    public function testSetData()
    {
        $chunk = new Chunk('iTXt', 'data');
        $chunk->setData('newdata');

        $this->assertEquals('newdata', $chunk->getData());
    }
}
