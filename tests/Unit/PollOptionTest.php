<?php

namespace Tests\Unit;

use App\Poll;
use Tests\TestCase;

class PollOptionTest extends TestCase
{
    /** @test */
    public function testIfColorMissingOnCreationThenItIsRandomlyGenerated()
    {
        $poll = Poll::factory()->create();

        $pollOption = $poll->addOption(['label' => 'Option label']);

        $this->assertNotNull($pollOption->color);
    }
}
