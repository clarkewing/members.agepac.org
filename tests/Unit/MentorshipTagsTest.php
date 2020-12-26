<?php

namespace Tests\Unit;

use App\Models\MentorshipTag;
use Spatie\Tags\Tag;
use Tests\TestCase;

class MentorshipTagsTest extends TestCase
{
    /** @test */
    public function testOnlyRetrievesTagsOfMentorshipType()
    {
        Tag::create(['name' => 'Foo', 'type' => 'mentorship']);
        Tag::create(['name' => 'Bar', 'type' => 'other']);

        $mentorshipTags = MentorshipTag::all();

        $this->assertCount(1, $mentorshipTags);
        $this->assertEquals('Foo', $mentorshipTags->first()->name);
    }

    /** @test */
    public function testAlwaysCreatesTagsWithMentorshipType()
    {
        $tag = MentorshipTag::create(['name' => 'Foo']);
        $this->assertEquals('mentorship', $tag->type);

        $tag = MentorshipTag::create(['name' => 'Foo', 'type' => 'foobar']);
        $this->assertEquals('mentorship', $tag->type);
    }
}
