<?php

namespace Memo\Youtube\Tests;

use Memo\Youtube\Youtube;
use PHPUnit\Framework\TestCase;

class YoutubeTest extends TestCase
{
    public $youtube;

    public function setUp()
    {
        $this->youtube = new Youtube(getenv("YOUTUBE_API_KEY"));
    }
    
    public function testSetKey()
    {
        $this->youtube->setKey('YOUTUBE_API_KEY');

        $this->assertEquals($this->youtube->getKey(), 'YOUTUBE_API_KEY');
    }

    public function testGetChannelByName()
    {
        $response = $this->youtube->getChannelByName('Google');

        $this->assertEquals('youtube#channel', $response->items[0]->kind);
        $this->assertEquals('Google', $response->items[0]->snippet->title);

        $this->assertObjectHasAttribute('id', $response->items[0]);
        $this->assertObjectHasAttribute('snippet', $response->items[0]);
        $this->assertObjectHasAttribute('statistics', $response->items[0]);
    }

    public function testGetChannelById()
    {
        $response = $this->youtube->getChannelById('UCK8sQmJBp8GCxrOtXWBpyEA');

        $this->assertEquals('youtube#channel', $response->items[0]->kind);
        $this->assertEquals('Google', $response->items[0]->snippet->title);
        
        $this->assertObjectHasAttribute('id', $response->items[0]);
        $this->assertObjectHasAttribute('snippet', $response->items[0]);
        $this->assertObjectHasAttribute('statistics', $response->items[0]);
    }

    public function tearDown()
    {
        $this->youtube = null;
    }
}
