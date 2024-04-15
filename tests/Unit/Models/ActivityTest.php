<?php

namespace Tests\Unit\Models;

use App\Models\Activity;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class ActivityTest extends TestCase
{

    public function test_next_week_boundaries()
    {
        $object = new Activity();

        $reflection = new ReflectionClass(get_class($object));
        $method = $reflection->getMethod('getNextWeekBoundaries');
        $method->setAccessible(true);
        $ret = $method->invokeArgs($object, ['2021-01-01']);
        $this->assertEquals(['from' => '2021-01-04 00:00:00', 'to' => '2021-01-10 23:59:59'], $ret);
    }


}
