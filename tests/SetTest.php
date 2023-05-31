<?php

namespace PhpSets\Test;

use PhpSets\Set;
use PHPUnit\Framework\TestCase;

class SetTest extends TestCase
{
    public function testSetOnMultipleValues(): void
    {
        $set = new Set(1, 2, 3, 4, 5);

        $expected = [1, 2, 3, 4, 5];
        $result = $set->values();

        $this->assertSame($expected, $result);
    }

    public function testAdd(): void
    {
        $set = new Set();
        $set->add('a');
        $this->assertSame(['a'], $set->values());

        $set->add('a');
        $this->assertSame(['a'], $set->values());
    }

    public function testAddViaArrayAccess(): void
    {
        $set = new Set();
        $set[] = 'a';
        $this->assertSame(['a'], $set->values());

        $set[] = 'a';
        $this->assertSame(['a'], $set->values());
    }

    public function testDelete(): void
    {
        $set = new Set();
        $set->add('a');

        $result = $set->delete('a');

        $this->assertTrue($result);
    }

    public function testDeleteOnNonExistedKey(): void
    {
        $set = new Set();

        $result = $set->delete('a');

        $this->assertFalse($result);
    }

    public function testClear(): void
    {
        $set = new Set();
        $set->add(1);
        $set->add(2);
        $set->clear();

        $expected = [];
        $result = $set->values();

        $this->assertSame($expected, $result);
    }

    public function testDiffOnSameValues(): void
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

    public function testDiffOnDifferentValues(): void
    {
        $set = new Set(1, 2);
        $set2 = new Set(3, 4);

        $result = $set->difference($set2);

        $this->assertSame(2, $result->size);
        $this->assertSame([1, 2], $result->values());
    }

    public function testDiffOnEmptySet(): void
    {
        $set = new Set();
        $set2 = new Set(3, 4);

        $result = $set->difference($set2);
        $this->assertSame(0, $result->size);
    }

    public function testDiffOnEmptyTargetSet(): void
    {
        $set = new Set(1, 2);
        $set2 = new Set();

        $result = $set->difference($set2);
        $this->assertSame(2, $result->size);
    }

    public function testHas(): void
    {
        $set = new Set();
        $result = $set->has(2);
        $this->assertFalse($result);

        $set->add(2);
        $result = $set->has(2);
        $this->assertTrue($result);
    }

    public function testEach(): void
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

    public function testOffsetSetOnNull(): void
    {
        $set = new Set();
        $set->offsetSet(0, null);

        $expected = [null];
        $result = $set->values();

        $this->assertSame($expected, $result);
    }

    public function testMerge(): void
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

    public function testIntersectOnContainedValue(): void
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

    public function testIntersectOnNoContainedValue(): void
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

    public function testSubsetShouldReturnTrue(): void
    {
        $set = new Set(1, 2);
        $subset = new Set(1);

        $this->assertTrue($set->isSupersetOf($subset));
        $this->assertFalse($subset->isSupersetOf($set));
    }

    public function testSubsetShouldReturnFalse(): void
    {
        $set = new Set();
        $set->add(1);
        $set->add(2);

        $subset = new Set();
        $subset->add(3);

        $this->assertFalse($set->isSupersetOf($subset));
    }

    public function testSymmetricDifference(): void
    {
        $set1 = new Set(1, 2, 'three');
        $set2 = new Set(2, 3, 4);

        $this->assertEquals([1, 'three', 3, 4], $set1->symmetricDifference($set2)->values());
        $this->assertEqualsArray([1, 'three', 3, 4], $set2->symmetricDifference($set1)->values());
    }

    public function testFromArray(): void
    {
        $set1 = new Set(1, 2, 'three');
        $set2 = new Set([1, 2, 'three']);
        $set3 = new Set(...[1, 2, 'three']);

        $this->assertEquals($set1->values(), $set2->values());
        $this->assertEquals($set1->values(), $set3->values());
    }

    public function testFamilyUnion(): void
    {
        # test empty family
        $sets = [];
        $set_expected = new Set();
        $set_union = Set::familyUnion($sets);

        $this->assertEquals($set_union->values(), $set_expected->values());

        # Test only one set
        $set1 = new Set(1, 2);
        $sets = [$set1];
        $set_expected = new Set(1, 2);
        $set_union = Set::familyUnion($sets);

        $this->assertEquals($set_union->values(), $set_expected->values());

        # Test equal sets
        $set1 = new Set(1, 2);
        $set2 = new Set(1, 2);
        $set3 = new Set(1, 2);
        $sets = [$set1, $set2, $set3];
        $set_expected = new Set(1, 2);
        $set_union = Set::familyUnion($sets);

        $this->assertEquals($set_union->values(), $set_expected->values());

        # Test disjoint sets
        $set1 = new Set(1, 2);
        $set2 = new Set(3, 4);
        $set3 = new Set(5, 6, 1);
        $sets = [$set1, $set2, $set3];
        $set_expected = new Set(1, 2, 3, 4, 5, 6);
        $set_union = Set::familyUnion($sets);

        $this->assertEquals($set_union->values(), $set_expected->values());

        # Test sets with nonempty intersection
        $set1 = new Set(1, 2, 3);
        $set2 = new Set(3, 4, 5);
        $sets = [$set1, $set2];
        $set_expected = new Set(1, 2, 3, 4, 5);
        $set_union = Set::familyUnion($sets);

        $this->assertEquals($set_union->values(), $set_expected->values());

        # Test sets where one is subset of another
        $set1 = new Set(1, 3, 2);
        $set2 = new Set(1, 2, 3, 4, 5);
        $sets = [$set1, $set2];
        $set_expected = new Set(1, 3, 2, 4, 5);
        $set_union = Set::familyUnion($sets);

        $this->assertEquals($set_union->values(), $set_expected->values());
    }

    public function testIntersectionOfArray(): void
    {
        # test empty family
        $sets = [];
        $set_expected = new Set();
        $set_union = Set::familyIntersection($sets);

        $this->assertEquals($set_union->values(), $set_expected->values());

        # Test only one set
        $set1 = new Set(1, 2);
        $sets = [$set1];
        $set_expected = new Set(1, 2);
        $set_union = Set::familyIntersection($sets);

        $this->assertEquals($set_union->values(), $set_expected->values());

        # Test equal sets
        $set1 = new Set(1, 2);
        $set2 = new Set(1, 2);
        $sets = [$set1, $set2];
        $set_expected = new Set(1, 2);
        $set_intersection = Set::familyIntersection($sets);

        $this->assertEquals($set_intersection->values(), $set_expected->values());

        # Test disjoint sets
        $set1 = new Set(1, 2);
        $set2 = new Set(3, 4);
        $sets = [$set1, $set2];
        $set_expected = new Set();
        $set_intersection = Set::familyIntersection($sets);

        $this->assertEquals($set_intersection->values(), $set_expected->values());

        # Test sets with nonempty intersection
        $set1 = new Set(1, 2, 3);
        $set2 = new Set(2, 3, 4, 5);
        $sets = [$set1, $set2];
        $set_expected = new Set(2, 3);
        $set_intersection = Set::familyIntersection($sets);

        $this->assertEquals($set_intersection->values(), $set_expected->values());

        # Test sets where one is subset of another
        $set1 = new Set(1, 2, 3);
        $set2 = new Set(1, 2, 3, 4, 5);
        $sets = [$set1, $set2];
        $set_expected = new Set(1, 2, 3);
        $set_intersection = Set::familyIntersection($sets);

        $this->assertEquals($set_intersection->values(), $set_expected->values());
    }

    protected function assertEqualsArray($expected, $actual, $message = ''): void
    {
        sort($expected);
        sort($actual);
        $this->assertEquals($expected, $actual, $message);
    }


}
