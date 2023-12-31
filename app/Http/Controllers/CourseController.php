<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Dtos\CourseDetailResponse;
use Illuminate\Http\JsonResponse;
use App\Constants\SampleData;
use Illuminate\Http\Request;

use Carbon\Carbon;

class CourseController extends Controller
{
    // コース一覧取得
    public function index()
    {
        /** @var \App\Dtos\CourseDetailResponse[] $sampleData */
        $sampleData = SampleData::$courses;
        return response()->json($sampleData);
    }

    /**
     * コース取得
     *
     * @path /course/{id}
     */
    public function getCourseById($id): JsonResponse
    {
        $course = Course::where('course_id', $id)->first();

        if (!$course) {
          return response()->json(['message' => 'Video not found'], 404);
        }

        $responseCourse = [
            'courseId' => $course->course_id,
            'name' => $course->name,
            'difficulty' => $course->difficulty,
            'leadSentence' => $course->lead_sentence,
            'imageSrc' => $course->image_src,
            'createdAt' => Carbon::parse($course->created_at)->format('Y-m-d-H:i:s'),
            'updatedAt' => Carbon::parse($course->updated_at)->format('Y-m-d-H:i:s'),
        ];
        return response()->json($responseCourse);

        // $course = Course::find($id);

        // // レスポンスの作成
        // $response = new CourseDetailResponse();
        // $response->code = $course->code;
        // $response->CourseId = $course->CourseId;
        // $response->title = $course->title;
        // $response->imageSrc = $course->imageSrc;

        // return response()->json($response);
    }

    /**
     * DBから一覧のデータを取得したい
     *
     * @path /courses2
     */
    public function getCourses(): JsonResponse
    {
        // ユーザーを取得する処理など
        $courses = Course::all();

        $responseCourses = $courses->map(function ($course) {
          return [
              'courseId' => $course->course_id,
              'name' => $course->name,
              'difficulty' => $course->difficulty,
              'leadSentence' => $course->lead_sentence,
              'imageSrc' => $course->image_src,
              'createdAt' => Carbon::parse($course->created_at)->format('Y-m-d-H:i:s'),
              'updatedAt' => Carbon::parse($course->updated_at)->format('Y-m-d-H:i:s'),
          ];
        });
        return response()->json($responseCourses);
    }
}
