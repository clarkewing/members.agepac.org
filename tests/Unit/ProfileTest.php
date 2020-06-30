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
        $profile = create(Profile::class, ['bio' => 'This is a pretty awesome bio.']);

        $this->assertEquals('This is a pretty awesome bio.', $profile->bio);
    }

    /** @test */
    public function testCanHaveFlightHours()
    {
        $profile = create(Profile::class, ['flight_hours' => 150]);

        $this->assertSame(150, $profile->flight_hours);
    }

    /** @test */
    public function testCanHaveExperience()
    {
        $profile = create(Profile::class);

        $this->assertEmpty($profile->experience);

        create(Occupation::class, ['user_id' => $profile->id]);

        $this->assertCount(1, $profile->fresh()->experience);
    }

    /** @test */
    public function testExperienceOrderedLatestFirst()
    {
        $profile = create(Profile::class);

        $occupationOne = create(Occupation::class, [
            'user_id' => $profile->id,
            'start_date' => '2010-01-01',
            'end_date' => '2012-01-01',
        ]);

        $occupationTwo = create(Occupation::class, [
            'user_id' => $profile->id,
            'start_date' => '2012-01-01',
            'end_date' => '2012-12-31',
        ]);

        $occupationThree = create(Occupation::class, [
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
        $profile = create(Profile::class);

        $primaryOccupation = create(Occupation::class, [
            'user_id' => $profile->id,
            'start_date' => '2010-01-01',
            'end_date' => null,
            'is_primary' => true,
        ]);

        $otherOccupation = create(Occupation::class, [
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
        $profile = create(Profile::class);

        // Has no experience
        $this->assertNull($profile->currentOccupation());

        // Has experience but none ongoing
        create(Occupation::class, [
            'user_id' => $profile->id,
            'start_date' => '2012-01-01',
            'end_date' => '2012-12-31',
        ]);
        $this->assertNull($profile->fresh()->currentOccupation());

        // Has ongoing experience but no primary set
        $occupationOne = create(Occupation::class, [
            'user_id' => $profile->id,
            'start_date' => '2015-01-01',
            'end_date' => null,
        ]);
        $this->assertEquals($occupationOne->id, $profile->fresh()->currentOccupation()->id);

        // Has two ongoing experiences with no primary set
        $occupationTwo = create(Occupation::class, [
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
        $profile = create(Profile::class);

        $this->assertFalse($profile->hasOccupation());

        create(Occupation::class, [
            'user_id' => $profile->id,
            'end_date' => null,
        ]);
        $this->assertTrue($profile->fresh()->hasOccupation());
    }

    /** @test */
    public function testCanHaveEducation()
    {
        $profile = create(Profile::class);

        $this->assertEmpty($profile->education);

        create(Course::class, ['user_id' => $profile->id]);

        $this->assertCount(1, $profile->fresh()->education);
    }

    /** @test */
    public function testEducationOrderedLatestFirst()
    {
        $profile = create(Profile::class);

        $courseOne = create(Course::class, [
            'user_id' => $profile->id,
            'start_date' => '2010-01-01',
            'end_date' => '2012-01-01',
        ]);

        $courseTwo = create(Course::class, [
            'user_id' => $profile->id,
            'start_date' => '2012-01-01',
            'end_date' => '2012-12-31',
        ]);

        $courseThree = create(Course::class, [
            'user_id' => $profile->id,
            'start_date' => '2015-01-01',
            'end_date' => null,
        ]);

        $this->assertEquals($courseThree->id, $profile->education[0]->id);
        $this->assertEquals($courseTwo->id, $profile->education[1]->id);
        $this->assertEquals($courseOne->id, $profile->education[2]->id);
    }
}
