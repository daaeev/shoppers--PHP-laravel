<?php

namespace Tests\Unit;

use App\Services\FiltersProcessing;
use App\Services\Interfaces\FilterProcessingInterface;
use PHPUnit\Framework\TestCase;

class FiltersProcessingTest extends TestCase
{
    protected FilterProcessingInterface $processing;

    public function setUp(): void
    {
        parent::setUp();
        $this->processing = app(FiltersProcessing::class);
    }

    public function testArrayHasFiltersIfNot()
    {
        $data = [2, 'something', [1, 2]];

        $this->assertFalse($this->processing->arrayHasFilters($data));
    }

    /**
     * @dataProvider dataHasFilters
     */
    public function testArrayHasFiltersIfHas($data)
    {
        $this->assertTrue($this->processing->arrayHasFilters($data));
    }

    public function testGetFiltersFromArrayIfNotHas()
    {
        $data = [2, 'something', [1, 2]];
        $result = $this->processing->getFiltersFromArray($data);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testGetFiltersFromArrayIfHas()
    {
        $data = [
            'filt_color' => 1,
            2,
            'filt_size' => 1,
            'something',
            [1, 2],
            'filt_category' => 1,
            'string',
            'order' => 'name_asc',
            123
        ];

        $result = $this->processing->getFiltersFromArray($data);

        $this->assertIsArray($result);
        $this->assertCount(4, $result);
        $this->assertArrayHasKey('filt_color', $result);
        $this->assertArrayHasKey('filt_size', $result);
        $this->assertArrayHasKey('filt_category', $result);
        $this->assertArrayHasKey('order', $result);
    }

    public function testProcessFiltersArrayIfEmpty()
    {
        $result = $this->processing->processFiltersArray([]);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testProcessFiltersArrayAllFilters()
    {
        $filters = [
            'filt_color' => 1,
            'filt_category' => 2,
            'filt_size' => 3,
            'order' => 'name_asc',
        ];

        $result = $this->processing->processFiltersArray($filters);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);

        $this->assertArrayHasKey('where', $result);
        $this->assertArrayHasKey('category_id', $result['where']);
        $this->assertArrayHasKey('size_id', $result['where']);
        $this->assertArrayHasKey('color_id', $result['where']);
        $this->assertCount(3, $result['where']);

        $this->assertArrayHasKey('order', $result);
        $this->assertArrayHasKey('column', $result['order']);
        $this->assertArrayHasKey('sort', $result['order']);
        $this->assertCount(2, $result['order']);

        $this->assertEquals(1, $result['where']['color_id']);
        $this->assertEquals(2, $result['where']['category_id']);
        $this->assertEquals(3, $result['where']['size_id']);

        $this->assertEquals('name', $result['order']['column']);
        $this->assertEquals('asc', $result['order']['sort']);
    }

    public function testProcessFiltersArrayWithoutOrder()
    {
        $filters = [
            'filt_color' => 1,
            'filt_category' => 2,
            'filt_size' => 3,
        ];

        $result = $this->processing->processFiltersArray($filters);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);

        $this->assertArrayHasKey('where', $result);
        $this->assertCount(3, $result['where']);

        $this->assertArrayNotHasKey('order', $result);
    }

    public function testProcessFiltersArrayWithoutWhereFilters()
    {
        $filters = [
            'order' => 'name_asc',
        ];

        $result = $this->processing->processFiltersArray($filters);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);

        $this->assertArrayHasKey('order', $result);
        $this->assertCount(2, $result['order']);

        $this->assertArrayNotHasKey('where', $result);
    }

    public function dataHasFilters()
    {
        return [
            [['filt_category' => 1]],
            [['filt_color' => 1]],
            [['filt_size' => 1]],
            [['filt_size' => 1, 'filt_color' => 1]],
            [['filt_size' => 1, 'filt_category' => 1, 'filt_color' => 1]],
            [['filt_size' => 1, 'filt_category' => 1, 'filt_color' => 1, 'order' => 'name_asc']],
            [['order' => 'name_asc']],

        ];
    }
}
