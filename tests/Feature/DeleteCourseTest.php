<?php

namespace Tests\Feature;

use App\Course;
use App\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class DeleteCourseTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling();
    }

    /** @test */
    public function testGuestCannotDeleteCourse()
    {
        $this->deleteCourse(1)
            ->assertUnauthorized();
    }

    /** @test */
    public function testOnlyAuthorizedUserCanDeleteCourse()
    {
        $course = create(Course::class);

        $this->signIn();

        $this->deleteCourse($course->id)
            ->assertForbidden();

        $this->signIn(User::find($course->user_id));

        $this->deleteCourse($course->id)
            ->assertSuccessful();
    }

    /** @test */
    public function testCourseMustExist()
    {
        $this->signIn();

        $this->deleteCourse(999)
            ->assertNotFound();
    }

    /** @test */
    public function testCourseOwnerCanDeleteIt()
    {
        $this->signIn();

        $course = create(Course::class, ['user_id' => Auth::id()]);

        $this->assertDatabaseHas('courses', ['id' => $course->id]);

        $this->deleteCourse($course)
            ->assertNoContent();

        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }

    /**
     * Send a request to delete the course.
     *
     * @param  \App\Course|int  $course
     * @return \Illuminate\Testing\TestResponse
     */
    protected function deleteCourse($course)
    {
        return $this->deleteJson(route(
            'courses.destroy',
            $course
        ));
    }
}
