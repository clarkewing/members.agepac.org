<?php

namespace Tests\Unit;

use App\Inspections\Spam;
use Exception;
use Tests\TestCase;

class SpamTest extends TestCase
{
    /** @test */
    public function testChecksForInvalidKeywords()
    {
        $spam = new Spam;

        $this->assertFalse($spam->detect('Innocent reply here.'));

        $this->expectException(Exception::class);

        $spam->detect('Yahoo Customer Support');
    }

    /** @test */
    public function testChecksForAnyKeyBeingHeldDown()
    {
        $spam = new Spam;

        $this->expectException(Exception::class);

        $spam->detect('Hello world aaaaaaaaaaaa');
    }
}
