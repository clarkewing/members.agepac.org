<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CoursePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the course.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Course  $course
     * @return bool
     */
    public function update(User $user, Course $course): bool
    {
        return $course->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the course.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Course  $course
     * @return bool
     */
    public function delete(User $user, Course $course): bool
    {
        return $this->update($user, $course);
    }
}
