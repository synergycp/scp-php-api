<?php

use Scp\Support\Collection;

class CollectionTest extends \Scp\TestCase
{
    public function testImplementsStandardInterfaces()
    {
        $collection = new Collection(['a' => 1, 'b' => 2]);

        $this->assertCount(2, $collection);
        $this->assertSame(['a' => 1, 'b' => 2], iterator_to_array($collection));
        $this->assertSame('{"a":1,"b":2}', json_encode($collection));

        $this->assertTrue(isset($collection['a']));
        $this->assertFalse(isset($collection['missing']));
        $this->assertSame(2, $collection['b']);

        $collection['c'] = 3;
        $collection[] = 4;
        $this->assertSame(['a' => 1, 'b' => 2, 'c' => 3, 0 => 4], $collection->all());

        unset($collection['a']);
        $this->assertFalse(isset($collection['a']));
        $this->assertCount(3, $collection);
    }

    public function testJsonSerializeUnwrapsNestedCollections()
    {
        $collection = new Collection([new Collection([1, 2]), 'x']);

        $this->assertSame([[1, 2], 'x'], $collection->jsonSerialize());
    }
}
