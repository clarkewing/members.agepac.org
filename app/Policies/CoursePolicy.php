<?php

namespace App\Policies;

use App\Course;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CoursePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the course.
     *
     * @param  \App\User  $user
     * @param  \App\Course  $course
     * @return bool
     */
    public function update(User $user, Course $course): bool
    {
        return $course->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the course.
     *
     * @param  \App\User  $user
     * @param  \App\Course  $course
     * @return bool
     */
    public function delete(User $user, Course $course): bool
    {
        return $this->update($user, $course);
    }
}
