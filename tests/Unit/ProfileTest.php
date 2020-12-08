<?php

namespace Tests\Unit;

use App\Course;
use App\Occupation;
use App\Profile;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    /** @test */
    public function testCanHaveBio()
    {
        $profile = Profile::factory()->create(['bio' => 'This is a pretty awesome bio.']);

        $this->assertEquals('This is a pretty awesome bio.', $profile->bio);
    }

    /** @test */
    public function testCanHaveFlightHours()
    {
        $profile = Profile::factory()->create(['flight_hours' => 150]);

        $this->assertSame(150, $profile->flight_hours);
    }

    /** @test */
    public function testCanHaveExperience()
    {
        $profile = Profile::factory()->create();

        $this->assertEmpty($profile->experience);

        Occupation::factory()->create(['user_id' => $profile->id]);

        $this->assertCount(1, $profile->fresh()->experience);
    }

    /** @test */
    public function testExperienceOrderedLatestFirst()
    {
        $profile = Profile::factory()->create();

        $occupationOne = Occupation::factory()->create([
            'user_id' => $profile->id,
            'start_date' => '2010-01-01',
            'end_date' => '2012-01-01',
        ]);

        $occupationTwo = Occupation::factory()->create([
            'user_id' => $profile->id,
            'start_date' => '2012-01-01',
            'end_date' => '2012-12-31',
        ]);

        $occupationThree = Occupation::factory()->create([
            'user_id' => $profile->id,
            'start_date' => '2015-01-01',
            'end_date' => null,
        ]);

        $this->assertEquals($occupationThree->id, $profile->experience[0]->id);
        $this->assertEquals($occupationTwo->id, $profile->experience[1]->id);
        $this->assertEquals($occupationOne->id, $profile->experience[2]->id);
    }

    /** @test */
    public function testPrimaryOccupationIsFirstExperience()
    {
        $profile = Profile::factory()->create();

        $primaryOccupation = Occupation::factory()->create([
            'user_id' => $profile->id,
            'start_date' => '2010-01-01',
            'end_date' => null,
            'is_primary' => true,
        ]);

        $otherOccupation = Occupation::factory()->create([
            'user_id' => $profile->id,
            'start_date' => '2012-01-01',
            'end_date' => null,
        ]);

        $this->assertEquals($primaryOccupation->id, $profile->experience[0]->id);
        $this->assertEquals($otherOccupation->id, $profile->experience[1]->id);
    }

    /** @test */
    public function testCanGetCurrentOccupation()
    {
        $profile = Profile::factory()->create();

        // Has no experience
        $this->assertNull($profile->currentOccupation());

        // Has experience but none ongoing
        Occupation::factory()->create([
            'user_id' => $profile->id,
            'start_date' => '2012-01-01',
            'end_date' => '2012-12-31',
        ]);
        $this->assertNull($profile->fresh()->currentOccupation());

        // Has ongoing experience but no primary set
        $occupationOne = Occupation::factory()->create([
            'user_id' => $profile->id,
            'start_date' => '2015-01-01',
            'end_date' => null,
        ]);
        $this->assertEquals($occupationOne->id, $profile->fresh()->currentOccupation()->id);

        // Has two ongoing experiences with no primary set
        $occupationTwo = Occupation::factory()->create([
            'user_id' => $profile->id,
            'start_date' => '2017-01-01',
            'end_date' => null,
        ]);
        $this->assertEquals($occupationTwo->id, $profile->fresh()->currentOccupation()->id);

        // Has two ongoing experiences with primary set
        $occupationOne->update(['is_primary' => true]);
        $this->assertEquals($occupationOne->id, $profile->fresh()->currentOccupation()->id);
    }

    /** @test */
    public function testKnowsIfItHasAnOccupation()
    {
        $profile = Profile::factory()->create();

        $this->assertFalse($profile->hasOccupation());

        Occupation::factory()->create([
            'user_id' => $profile->id,
            'end_date' => null,
        ]);
        $this->assertTrue($profile->fresh()->hasOccupation());
    }

    /** @test */
    public function testCanHaveEducation()
    {
        $profile = Profile::factory()->create();

        $this->assertEmpty($profile->education);

        Course::factory()->create(['user_id' => $profile->id]);

        $this->assertCount(1, $profile->fresh()->education);
    }

    /** @test */
    public function testEducationOrderedLatestFirst()
    {
        $profile = Profile::factory()->create();

        $courseOne = Course::factory()->create([
            'user_id' => $profile->id,
            'start_date' => '2010-01-01',
            'end_date' => '2012-01-01',
        ]);

        $courseTwo = Course::factory()->create([
            'user_id' => $profile->id,
            'start_date' => '2012-01-01',
            'end_date' => '2012-12-31',
        ]);

        $courseThree = Course::factory()->create([
            'user_id' => $profile->id,
            'start_date' => '2015-01-01',
            'end_date' => null,
        ]);

        $this->assertEquals($courseThree->id, $profile->education[0]->id);
        $this->assertEquals($courseTwo->id, $profile->education[1]->id);
        $this->assertEquals($courseOne->id, $profile->education[2]->id);
    }
}
