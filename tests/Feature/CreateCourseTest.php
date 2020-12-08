<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class CreateCourseTest extends TestCase
{
    /**
     * @var array The data to create a new course
     */
    protected $data = [
        'title' => "Diplôme d'Élève Pilote de Ligne",
        'school' => 'ENAC',
        'description' => 'Best flight training in the world. Hands down.',
        'start_date' => '2015-09-28',
        'end_date' => '2018-06-13',
        'location' => [
            'type' => 'address',
            'name' => '7 Avenue Edouard Belin, Toulouse, Occitanie, France',
            'street_line_1' => '7 Avenue Edouard Belin',
            'street_line_2' => null,
            'municipality' => 'Toulouse',
            'administrative_area' => 'Occitanie',
            'sub_administrative_area' => 'Haute-Garonne',
            'postal_code' => '31400',
            'country' => 'France',
            'country_code' => 'FR',
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling()->signIn();
    }

    /** @test */
    public function testGuestCannotStoreCourse()
    {
        Auth::logout();

        $this->storeCourse()
            ->assertUnauthorized();
    }

    /** @test */
    public function testTitleIsRequired()
    {
        $this->storeCourse(['title' => null])
            ->assertJsonValidationErrors('title');
    }

    /** @test */
    public function testTitleMustBeString()
    {
        $this->storeCourse(['title' => 12345])
            ->assertJsonValidationErrors('title');
    }

    /** @test */
    public function testTitleCannotBeLongerThan255Characters()
    {
        $this->storeCourse(['title' => str_repeat('*', 256)])
            ->assertJsonValidationErrors('title');
    }

    /** @test */
    public function testSchoolIsRequired()
    {
        $this->storeCourse(['school' => null])
            ->assertJsonValidationErrors('school');
    }

    /** @test */
    public function testSchoolMustBeString()
    {
        $this->storeCourse(['school' => 12345])
            ->assertJsonValidationErrors('school');
    }

    /** @test */
    public function testSchoolCannotBeLongerThan255Characters()
    {
        $this->storeCourse(['school' => str_repeat('*', 256)])
            ->assertJsonValidationErrors('school');
    }

    /** @test */
    public function testLocationIsRequired()
    {
        $this->storeCourse(['location' => null])
            ->assertJsonValidationErrors('location');
    }

    /** @test */
    public function testLocationMustBeValid()
    {
        $this->storeCourse(['location' => 'foobar'])
            ->assertJsonValidationErrors('location');

        $this->storeCourse(['location' => []])
            ->assertJsonValidationErrors('location');
    }

    /** @test */
    public function testStartDateIsRequired()
    {
        $this->storeCourse(['start_date' => null])
            ->assertJsonValidationErrors('start_date');
    }

    /** @test */
    public function testStartDateMustBeDateInIsoFormat()
    {
        $this->storeCourse(['start_date' => 'foobar'])
            ->assertJsonValidationErrors('start_date');

        $this->storeCourse(['start_date' => 12345678])
            ->assertJsonValidationErrors('start_date');

        $this->storeCourse(['start_date' => '01/01/2020'])
            ->assertJsonValidationErrors('start_date');
    }

    /** @test */
    public function testEndDateCanBeNull()
    {
        $this->storeCourse(['end_date' => null])
            ->assertJsonMissingValidationErrors('end_date');
    }

    /** @test */
    public function testEndDateMustBeDateInIsoFormat()
    {
        $this->storeCourse(['end_date' => 'foobar'])
            ->assertJsonValidationErrors('end_date');

        $this->storeCourse(['end_date' => 12345678])
            ->assertJsonValidationErrors('end_date');

        $this->storeCourse(['end_date' => '01/01/2020'])
            ->assertJsonValidationErrors('end_date');
    }

    /** @test */
    public function testEndDateMustBeInPast()
    {
        $this->storeCourse(['end_date' => '2099-12-31'])
            ->assertJsonValidationErrors('end_date');
    }

    /** @test */
    public function testStartAndEndDatesMustBeChronological()
    {
        $this->storeCourse([
            'start_date' => '2000-01-01',
            'end_date' => '1999-12-31',
        ])->assertJsonValidationErrors(['start_date', 'end_date']);
    }

    /** @test */
    public function testNullEndDateDoesNotLimitStartDate()
    {
        $this->storeCourse([
            'start_date' => '2000-01-01',
            'end_date' => null,
        ])->assertJsonMissingValidationErrors('start_date');
    }

    /** @test */
    public function testDescriptionCanBeNull()
    {
        $this->storeCourse(['description' => null])
            ->assertJsonMissingValidationErrors('description');
    }

    /** @test */
    public function testDescriptionMustBeString()
    {
        $this->storeCourse(['description' => 12345])
            ->assertJsonValidationErrors('description');
    }

    /** @test */
    public function testDescriptionCannotBeLongerThan65535Characters()
    {
        $this->storeCourse(['description' => str_repeat('*', 65536)])
            ->assertJsonValidationErrors('description');
    }

    /** @test */
    public function testCanStoreCourse()
    {
        $this->storeCourse()
            ->assertJsonMissingValidationErrors()
            ->assertCreated()
            ->assertJson($this->data);
    }

    /**
     * Send a request to store the course.
     *
     * @param  array  $overrides
     * @return \Illuminate\Testing\TestResponse
     */
    protected function storeCourse(array $overrides = [])
    {
        return $this->postJson(
            route('courses.store'),
            array_merge($this->data, $overrides)
        );
    }
}
