<?php

namespace Tests\Unit;

use Exception;
use Tests\TestCase;
use App\Inspections\Spam;

class SpamTest extends TestCase
{
    /**
     * @test
     */
    public function testChecksForInvalidKeywords()
    {
        $spam = new Spam;

        $this->assertFalse($spam->detect('Innocent reply here.'));

        $this->expectException(Exception::class);

        $spam->detect('Yahoo Customer Support');
    }

    /**
     * @test
     */
    public function testChecksForAnyKeyBeingHeldDown()
    {
        $spam = new Spam;

        $this->expectException(Exception::class);

        $spam->detect('Hello world aaaaaaaaaaaa');
    }
}
