<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = Course::orderBy('id', 'DESC')->get();
        return view('admin.courses.index', ['courses' => $courses]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();

        return view('admin.courses.create', ['categories' => $categories]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:225',
            'category_id' => 'required',
            'cover' => 'required|image|mimes:png,jpg,jpeg,svg'
        ]);

        DB::beginTransaction();

        try {
            if ($request->hasFile('cover')) {
                $coverPath = $request->file('cover')->store('product_covers', 'public');
                $validated['cover'] = $coverPath;
            }
            $validated['slug'] = Str::slug($request->name);
            $newCourses = Course::create($validated);

            // dd($newCourses);

            DB::commit();

            return redirect()->route('dashboardcourses.index');
        } catch (\Exception $e) {
            DB::rollBack();

            $errors = ValidationException::withMessages([
                'system_error' => ['System error' . $e->getMessage()]
            ]);

            throw $errors;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        $students = $course->students()->orderBy('id', 'DESC')->get();
        $questions = $course->questions()->orderBy('id', 'DESC')->get();
        

        return view('admin.courses.manage', [
            'course' => $course,
            'students' => $students,
            'questions' => $questions
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course)
    {
        $categories = Category::all();
        return view('admin.courses.edit', [
            'course' => $course,
            'categories' => $categories
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:225',
            'category_id' => 'required',
            'cover' => 'sometimes|image|mimes:png,jpg,jpeg,svg'
        ]);

        DB::beginTransaction();

        try {
            if ($request->hasFile('cover')) {
                $coverPath = $request->file('cover')->store('product_covers', 'public');
                $validated['cover'] = $coverPath;
            }
            $validated['slug'] = Str::slug($request->name);

            $newCourses = $course->update($validated);

            // dd($newCourses);

            DB::commit();

            return redirect()->route('dashboardcourses.index');
        } catch (\Exception $e) {
            DB::rollBack();

            $errors = ValidationException::withMessages([
                'system_error' => ['System error' . $e->getMessage()]
            ]);

            throw $errors;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        try {
            $course->delete();
            return redirect()->route('dashboardcourses.index');
        } catch (\Exception $e) {
            DB::rollBack();

            $errors = ValidationException::withMessages([
                'system_error' => ['System error' . $e->getMessage()]
            ]);

            throw $errors;
        }
    }
}
