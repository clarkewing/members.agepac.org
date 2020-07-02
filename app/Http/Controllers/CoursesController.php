<?php

namespace App\Http\Controllers;

use App\Course;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class CoursesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCourseRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreCourseRequest $request)
    {
        $course = tap($request->user()->profile->addEducation($request->all()))
            ->setLocation($request->input('location'));

        return Response::json($course, HttpResponse::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCourseRequest  $request
     * @param  \App\Course  $course
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateCourseRequest $request, Course $course)
    {
        $course->update($request->all());

        if ($request->filled('location')) {
            $course->setLocation($request->input('location'));
        }

        return Response::json($course->fresh());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Course  $course
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Exception
     */
    public function destroy(Course $course)
    {
        $this->authorize('delete', $course);

        $course->delete();

        return Response::noContent();
    }
}
