<?php

namespace PhpSets\Test;

use PhpSets\Set;
use PHPUnit\Framework\TestCase;

class SetTest extends TestCase
{
    public function testSetOnMultipleValues()
    {
        $set = new Set(1, 2, 3, 4, 5);

        $expected = [1, 2, 3, 4, 5];
        $result = $set->values();

        $this->assertSame($expected, $result);
    }

    public function testAdd()
    {
        $set = new Set();
        $set->add('a');

        $expected = ['a'];
        $result = $set->values();

        $this->assertSame($expected, $result);
    }

    public function testDelete()
    {
        $set = new Set();
        $set->add('a');

        $result = $set->delete('a');

        $this->assertTrue($result);
    }

    public function testDeleteOnNonExistedKey()
    {
        $set = new Set();

        $result = $set->delete('a');

        $this->assertFalse($result);
    }

    public function testClear()
    {
        $set = new Set();
        $set->add(1);
        $set->add(2);
        $set->clear();

        $expected = [];
        $result = $set->values();

        $this->assertSame($expected, $result);
    }

    public function testDiffOnSameValues()
    {
        $set = new Set();
        $set->add(1);
        $set->add(2);

        $set2 = new Set();
        $set2->add(1);
        $set2->add(2);

        $expected = 0;
        $result = $set->diff($set2)->size;

        $this->assertSame($expected, $result);
    }

    public function testDiffOnDifferentValues()
    {
        $set = new Set();
        $set->add(1);
        $set->add(2);

        $set2 = new Set();
        $set2->add(3);
        $set2->add(4);

        $expected = 4;
        $result = $set->diff($set2)->size;

        $this->assertSame($expected, $result);
    }

    public function testHas()
    {
        $set = new Set();
        $result = $set->has(2);
        $this->assertFalse($result);

        $set->add(2);
        $result = $set->has(2);
        $this->assertTrue($result);
    }

    public function testEach()
    {
        $set = new Set();
        $set->add(1);
        $set->add(2);

        $expected = $set->values();

        $resultSet = $set->each(function ($value, $param) {
            return $value * $param;
        }, 10);
        $result = $resultSet->values();

        $this->assertSame($expected, $result);
    }

    public function testOffsetSetOnNull()
    {
        $set = new Set();
        $set->offsetSet(0, null);

        $expected = [null];
        $result = $set->values();

        $this->assertSame($expected, $result);
    }

    public function testMerge()
    {
        $set = new Set();
        $set->add(1);
        $set->add(2);

        $set2 = new Set();
        $set2->add(1);
        $set2->add(null);

        $expected = [1, 2, null];
        $result = $set->merge($set2)->values();

        $this->assertSame($expected, $result);
    }

    public function testIntersectOnContainedValue()
    {
        $set = new Set();
        $set->add(1);
        $set->add(2);
        $set->add(3);

        $set2 = new Set();
        $set2->add(1);

        $expected = 1;
        $result = $set->intersect($set2)->size;

        $this->assertSame($expected, $result);
    }

    public function testIntersectOnNoContainedValue()
    {
        $set = new Set();
        $set->add(1);
        $set->add(2);
        $set->add(3);

        $set2 = new Set();
        $set2->add(4);

        $expected = 0;
        $result = $set->intersect($set2)->size;

        $this->assertSame($expected, $result);
    }

    public function testSubsetShouldReturnTrue()
    {
        $set = new Set();
        $set->add(1);
        $set->add(2);

        $subset = new Set();
        $subset->add(1);

        $this->assertTrue($set->subset($subset));
    }

    public function testSubsetShouldReturnFalse()
    {
        $set = new Set();
        $set->add(1);
        $set->add(2);

        $subset = new Set();
        $subset->add(3);

        $this->assertFalse($set->subset($subset));
    }
}
