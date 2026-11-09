<?php

namespace Scp;

#[\AllowDynamicProperties]
class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function tearDown(): void
    {
        if (class_exists(\Mockery::class)) {
            \Mockery::close();
        }

        parent::tearDown();
    }
}
