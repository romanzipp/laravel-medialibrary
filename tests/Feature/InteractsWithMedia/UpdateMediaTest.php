<?php

namespace Spatie\MediaLibrary\Tests\Feature\InteractsWithMedia;

use Spatie\MediaLibrary\Tests\TestCase;

class UpdateMediaTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->testModel->addMedia($this->getTestJpg())->usingName('test1')->preservingOriginal()->toMediaCollection();
        $this->testModel->addMedia($this->getTestJpg())->usingName('test2')->preservingOriginal()->toMediaCollection();
    }

    /** @test */
    public function it_removes_a_media_item_if_its_not_in_the_update_array()
    {
        $mediaArray = $this->testModel->images->toArray();
        unset($mediaArray[0]);

        $this->testModel->updateMedia($mediaArray);
        $this->testModel->load('images');

        $this->assertCount(1, $this->testModel->images);
        $this->assertEquals('test2', $this->testModel->getFirstMedia()->name);
    }

    /** @test */
    public function it_removes_a_media_item_with_eager_loaded_relation()
    {
        $mediaArray = $this->testModel->images->toArray();
        unset($mediaArray[0]);

        $this->testModel->load('images');
        $this->testModel->updateMedia($mediaArray);

        $this->assertCount(1, $this->testModel->images);
        $this->assertEquals('test2', $this->testModel->getFirstMedia()->name);
    }

    /** @test */
    public function it_renames_media_items()
    {
        $mediaArray = $this->testModel->images->toArray();

        $mediaArray[0]['name'] = 'testFoo';
        $mediaArray[1]['name'] = 'testBar';

        $this->testModel->updateMedia($mediaArray);
        $this->testModel->load('images');

        $this->assertEquals('testFoo', $this->testModel->images[0]->name);
        $this->assertEquals('testBar', $this->testModel->images[1]->name);
    }

    /** @test */
    public function it_updates_media_item_custom_properties()
    {
        $mediaArray = $this->testModel->images->toArray();

        $mediaArray[0]['custom_properties']['foo'] = 'bar';

        $this->testModel->updateMedia($mediaArray);
        $this->testModel->load('images');

        $this->assertEquals('bar', $this->testModel->images[0]->getCustomProperty('foo'));
    }

    /**
     * @test
     */
    public function it_reorders_media_items()
    {
        $mediaArray = $this->testModel->images->toArray();

        $differentOrder = array_reverse($mediaArray);

        $this->testModel->updateMedia($differentOrder);
        $this->testModel->load('images');

        $orderedMedia = $this->testModel->images->sortBy('order_column');

        $this->assertEquals($mediaArray[0]['order_column'], $orderedMedia[1]->order_column);
        $this->assertEquals($mediaArray[1]['order_column'], $orderedMedia[0]->order_column);
    }
}
