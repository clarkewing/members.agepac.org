<?php

namespace Tests\Feature;

use App\Course;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class EditCourseTest extends TestCase
{
    /**
     * @var \App\Course
     */
    protected $course;

    public function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling()->signIn();

        $this->course = create(Course::class, ['user_id' => Auth::id()]);
    }

    /** @test */
    public function testGuestCannotUpdateCourse()
    {
        Auth::logout();

        $this->updateCourse()
            ->assertUnauthorized();
    }

    /** @test */
    public function testOnlyAuthorizedUserCanUpdateCourse()
    {
        $this->updateCourse()
            ->assertOk();

        $this->signIn(); // Other user

        $this->updateCourse()
            ->assertForbidden();
    }

    /** @test */
    public function testTitleIsRequiredIfSet()
    {
        $this->updateCourse(['title' => null])
            ->assertJsonValidationErrors('title');
    }

    /** @test */
    public function testTitleMustBeString()
    {
        $this->updateCourse(['title' => 12345])
            ->assertJsonValidationErrors('title');
    }

    /** @test */
    public function testTitleCannotBeLongerThan255Characters()
    {
        $this->updateCourse(['title' => str_repeat('*', 256)])
            ->assertJsonValidationErrors('title');
    }

    /** @test */
    public function testSchoolIsRequiredIfSet()
    {
        $this->updateCourse(['school' => null])
            ->assertJsonValidationErrors('school');
    }

    /** @test */
    public function testSchoolMustBeString()
    {
        $this->updateCourse(['school' => 12345])
            ->assertJsonValidationErrors('school');
    }

    /** @test */
    public function testSchoolCannotBeLongerThan255Characters()
    {
        $this->updateCourse(['school' => str_repeat('*', 256)])
            ->assertJsonValidationErrors('school');
    }

    /** @test */
    public function testLocationIsRequiredIfSet()
    {
        $this->updateCourse(['location' => null])
            ->assertJsonValidationErrors('location');
    }

    /** @test */
    public function testLocationMustBeValid()
    {
        $this->updateCourse(['location' => 'foobar'])
            ->assertJsonValidationErrors('location');

        $this->updateCourse(['location' => []])
            ->assertJsonValidationErrors('location');
    }

    /** @test */
    public function testStartDateIsRequiredIfSet()
    {
        $this->updateCourse(['start_date' => null])
            ->assertJsonValidationErrors('start_date');
    }

    /** @test */
    public function testStartDateMustBeDateInIsoFormat()
    {
        // Set end_date to null to prevent conflicts.
        $this->course->update(['end_date' => null]);

        $this->updateCourse(['start_date' => 'foobar'])
            ->assertJsonValidationErrors('start_date');

        $this->updateCourse(['start_date' => 12345678])
            ->assertJsonValidationErrors('start_date');

        $this->updateCourse(['start_date' => '01/01/2020'])
            ->assertJsonValidationErrors('start_date');
    }

    /** @test */
    public function testEndDateCanBeNull()
    {
        $this->updateCourse(['end_date' => null])
            ->assertJsonMissingValidationErrors('end_date');
    }

    /** @test */
    public function testEndDateMustBeDateInIsoFormat()
    {
        $this->updateCourse(['end_date' => 'foobar'])
            ->assertJsonValidationErrors('end_date');

        $this->updateCourse(['end_date' => 12345678])
            ->assertJsonValidationErrors('end_date');

        $this->updateCourse(['end_date' => '01/01/2020'])
            ->assertJsonValidationErrors('end_date');
    }

    /** @test */
    public function testStartAndEndDatesMustBeChronological()
    {
        $this->updateCourse([
            'start_date' => '2000-01-01',
            'end_date' => '1999-12-31',
        ])->assertJsonValidationErrors(['start_date', 'end_date']);
    }

    /** @test */
    public function testStartDateMustBeBeforeExistingEndDate()
    {
        $this->course->update(['end_date' => '1999-12-31']);

        $this->updateCourse(['start_date' => '2000-01-01'])
            ->assertJsonValidationErrors('start_date');
    }

    /** @test */
    public function testEndDateMustBeAfterExistingStartDate()
    {
        $this->course->update(['start_date' => '2000-01-01']);

        $this->updateCourse(['end_date' => '1999-12-31'])
            ->assertJsonValidationErrors('end_date');
    }

    /** @test */
    public function testEndDateMustBeInPast()
    {
        $this->updateCourse(['end_date' => '2099-12-31'])
            ->assertJsonValidationErrors('end_date');
    }

    /** @test */
    public function testNullEndDateDoesNotLimitStartDate()
    {
        $this->updateCourse([
            'start_date' => '2000-01-01',
            'end_date' => null,
        ])
            ->assertJsonMissingValidationErrors('start_date')
            ->assertOk();

        $this->updateCourse(['start_date' => '2000-01-01'])
            ->assertJsonMissingValidationErrors('start_date');
    }

    /** @test */
    public function testDescriptionCanBeNull()
    {
        $this->updateCourse(['description' => null])
            ->assertJsonMissingValidationErrors('description');
    }

    /** @test */
    public function testDescriptionMustBeString()
    {
        $this->updateCourse(['description' => 12345])
            ->assertJsonValidationErrors('description');
    }

    /** @test */
    public function testDescriptionCannotBeLongerThan65535Characters()
    {
        $this->updateCourse(['description' => str_repeat('*', 65536)])
            ->assertJsonValidationErrors('description');
    }

    /** @test */
    public function testCanUpdateCourse()
    {
        $data = [
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

        $this->updateCourse($data)
            ->assertJsonMissingValidationErrors()
            ->assertOk()
            ->assertJson($data);
    }

    /**
     * Send a request to update the course.
     *
     * @param  array  $data
     * @param  null  $course
     * @return \Illuminate\Testing\TestResponse
     */
    protected function updateCourse(array $data = [], $course = null)
    {
        return $this->patchJson(
            route(
                'courses.update',
                $course ?? $this->course
            ),
            $data
        );
    }
}
