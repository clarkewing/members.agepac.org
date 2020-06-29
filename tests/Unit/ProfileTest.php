<?php

namespace Tests\Unit;

use App\Course;
use App\Occupation;
use App\User;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    /** @test */
    public function testCanHaveBio()
    {
        $user = create(User::class, ['bio' => 'This is a pretty awesome bio.']);

        $this->assertEquals('This is a pretty awesome bio.', $user->bio);
    }

    /** @test */
    public function testCanHaveFlightHours()
    {
        $user = create(User::class, ['flight_hours' => 150]);

        $this->assertSame(150, $user->flight_hours);
    }

    /** @test */
    public function testCanHaveExperience()
    {
        $user = create(User::class);

        $this->assertEmpty($user->experience);

        create(Occupation::class, ['user_id' => $user->id]);

        $this->assertCount(1, $user->fresh()->experience);
    }

    /** @test */
    public function testExperienceOrderedLatestFirst()
    {
        $user = create(User::class);

        $occupationOne = create(Occupation::class, [
            'user_id' => $user->id,
            'start_date' => '2010-01-01',
            'end_date' => '2012-01-01',
        ]);

        $occupationTwo = create(Occupation::class, [
            'user_id' => $user->id,
            'start_date' => '2012-01-01',
            'end_date' => '2012-12-31',
        ]);

        $occupationThree = create(Occupation::class, [
            'user_id' => $user->id,
            'start_date' => '2015-01-01',
            'end_date' => null,
        ]);

        $this->assertEquals($occupationThree->id, $user->experience[0]->id);
        $this->assertEquals($occupationTwo->id, $user->experience[1]->id);
        $this->assertEquals($occupationOne->id, $user->experience[2]->id);
    }

    /** @test */
    public function testPrimaryOccupationIsFirstExperience()
    {
        $user = create(User::class);

        $primaryOccupation = create(Occupation::class, [
            'user_id' => $user->id,
            'start_date' => '2010-01-01',
            'end_date' => null,
            'is_primary' => true,
        ]);

        $otherOccupation = create(Occupation::class, [
            'user_id' => $user->id,
            'start_date' => '2012-01-01',
            'end_date' => null,
        ]);

        $this->assertEquals($primaryOccupation->id, $user->experience[0]->id);
        $this->assertEquals($otherOccupation->id, $user->experience[1]->id);
    }

    /** @test */
    public function testCanGetCurrentOccupation()
    {
        $user = create(User::class);

        // Has no experience
        $this->assertNull($user->currentOccupation());

        // Has experience but none ongoing
        create(Occupation::class, [
            'user_id' => $user->id,
            'start_date' => '2012-01-01',
            'end_date' => '2012-12-31',
        ]);
        $this->assertNull($user->fresh()->currentOccupation());

        // Has ongoing experience but no primary set
        $occupationOne = create(Occupation::class, [
            'user_id' => $user->id,
            'start_date' => '2015-01-01',
            'end_date' => null,
        ]);
        $this->assertEquals($occupationOne->id, $user->fresh()->currentOccupation()->id);

        // Has two ongoing experiences with no primary set
        $occupationTwo = create(Occupation::class, [
            'user_id' => $user->id,
            'start_date' => '2017-01-01',
            'end_date' => null,
        ]);
        $this->assertEquals($occupationTwo->id, $user->fresh()->currentOccupation()->id);

        // Has two ongoing experiences with primary set
        $occupationOne->update(['is_primary' => true]);
        $this->assertEquals($occupationOne->id, $user->fresh()->currentOccupation()->id);
    }

    /** @test */
    public function testKnowsIfItHasAnOccupation()
    {
        $user = create(User::class);

        $this->assertFalse($user->hasOccupation());

        create(Occupation::class, [
            'user_id' => $user->id,
            'end_date' => null,
        ]);
        $this->assertTrue($user->fresh()->hasOccupation());
    }

    /** @test */
    public function testCanHaveEducation()
    {
        $user = create(User::class);

        $this->assertEmpty($user->education);

        create(Course::class, ['user_id' => $user->id]);

        $this->assertCount(1, $user->fresh()->education);
    }

    /** @test */
    public function testEducationOrderedLatestFirst()
    {
        $user = create(User::class);

        $courseOne = create(Course::class, [
            'user_id' => $user->id,
            'start_date' => '2010-01-01',
            'end_date' => '2012-01-01',
        ]);

        $courseTwo = create(Course::class, [
            'user_id' => $user->id,
            'start_date' => '2012-01-01',
            'end_date' => '2012-12-31',
        ]);

        $courseThree = create(Course::class, [
            'user_id' => $user->id,
            'start_date' => '2015-01-01',
            'end_date' => null,
        ]);

        $this->assertEquals($courseThree->id, $user->education[0]->id);
        $this->assertEquals($courseTwo->id, $user->education[1]->id);
        $this->assertEquals($courseOne->id, $user->education[2]->id);
    }
}
