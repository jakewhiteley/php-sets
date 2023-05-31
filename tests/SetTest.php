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
        $this->assertSame(['a'], $set->values());

        $set->add('a');
        $this->assertSame(['a'], $set->values());
    }

    public function testAddViaArrayAccess()
    {
        $set = new Set();
        $set[] = 'a';
        $this->assertSame(['a'], $set->values());

        $set[] ='a';
        $this->assertSame(['a'], $set->values());
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
        $result = $set->difference($set2)->size;

        $this->assertSame($expected, $result);
    }

    public function testDiffOnDifferentValues()
    {
        $set = new Set(1, 2);
        $set2 = new Set(3, 4);

        $result = $set->difference($set2);

        $this->assertSame(2, $result->size);
        $this->assertSame([1,2], $result->values());
    }

    public function testDiffOnEmptySet()
    {
        $set = new Set();
        $set2 = new Set(3, 4);

        $result = $set->difference($set2);
        $this->assertSame(0, $result->size);
    }

    public function testDiffOnEmptyTargetSet()
    {
        $set = new Set(1,2);
        $set2 = new Set();

        $result = $set->difference($set2);
        $this->assertSame(2, $result->size);
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
        $result = $set->union($set2)->values();

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
        $set = new Set(1, 2);
        $subset = new Set(1);

        $this->assertTrue($set->isSupersetOf($subset));
        $this->assertFalse($subset->isSupersetOf($set));
    }

    public function testSubsetShouldReturnFalse()
    {
        $set = new Set();
        $set->add(1);
        $set->add(2);

        $subset = new Set();
        $subset->add(3);

        $this->assertFalse($set->isSupersetOf($subset));
    }

    public function testSymmetricDifference()
    {
        $set1 = new Set(1, 2, 'three');
        $set2 = new Set(2, 3, 4);

        $this->assertEquals([1, 'three', 3, 4], $set1->symmetricDifference($set2)->values());
        $this->assertEqualsArray([1, 'three', 3, 4], $set2->symmetricDifference($set1)->values());
    }

    public function testFromArray()
    {
        $set1 = new Set(1, 2, 'three');
        $set2 = Set::FromArray([1, 2, 'three']);

        $this->assertEquals($set1->values(),$set2->values());
    }

    public function testUnionOfArray()
    {
      # Test equal sets 
      $set1 = new Set(1,2);
      $set2 = new Set(1,2);
      $sets = [ $set1, $set2 ];
      $set_expected = new Set(1,2);
      $set_union = Set::UnionOfArray($sets);

      $this->assertEquals($set_union->values(),$set_expected->values());

      # Test disjoint sets 
      $set1 = new Set(1,2);
      $set2 = new Set(3,4);
      $sets = [ $set1, $set2 ];
      $set_expected = new Set(1,2,3,4);
      $set_union = Set::UnionOfArray($sets);

      $this->assertEquals($set_union->values(),$set_expected->values());

      # Test sets with nonempty intersection
      $set1 = new Set(1,2,3);
      $set2 = new Set(3,4,5);
      $sets = [ $set1, $set2 ];
      $set_expected = new Set(1,2,3,4,5);
      $set_union = Set::UnionOfArray($sets);

      $this->assertEquals($set_union->values(),$set_expected->values());

      # Test sets where one is subset of another
      $set1 = new Set(1,2,3);
      $set2 = new Set(1,2,3,4,5);
      $sets = [ $set1, $set2 ];
      $set_expected = new Set(1,2,3,4,5);
      $set_union = Set::UnionOfArray($sets);

      $this->assertEquals($set_union->values(),$set_expected->values());
    }

    public function testIntersectionOfArray()
    {
      # Test equal sets 
      $set1 = new Set(1,2);
      $set2 = new Set(1,2);
      $sets = [ $set1, $set2 ];
      $set_expected = new Set(1,2);
      $set_intersection = Set::IntersectionOfArray($sets);

      $this->assertEquals($set_intersection->values(),$set_expected->values());

      # Test disjoint sets 
      $set1 = new Set(1,2);
      $set2 = new Set(3,4);
      $sets = [ $set1, $set2 ];
      $set_expected = new Set();
      $set_intersection = Set::IntersectionOfArray($sets);

      $this->assertEquals($set_intersection->values(),$set_expected->values());

      # Test sets with nonempty intersection
      $set1 = new Set(1,2,3);
      $set2 = new Set(2,3,4,5);
      $sets = [ $set1, $set2 ];
      $set_expected = new Set(2,3);
      $set_intersection = Set::IntersectionOfArray($sets);

      $this->assertEquals($set_intersection->values(),$set_expected->values());

      # Test sets where one is subset of another
      $set1 = new Set(1,2,3);
      $set2 = new Set(1,2,3,4,5);
      $sets = [ $set1, $set2 ];
      $set_expected = new Set(1,2,3);
      $set_intersection = Set::IntersectionOfArray($sets);

      $this->assertEquals($set_intersection->values(),$set_expected->values());
    }

    protected function assertEqualsArray($expected, $actual, $message = '')
    {
        sort($expected);
        sort($actual);
        $this->assertEquals($expected, $actual, $message);
    }


}
