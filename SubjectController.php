<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class SubjectController extends Controller
{
    public function index(Request $request, $student_id)
    {
        try {
            $student = Student::findOrFail($student_id);
            $subjects = $student->subjects()->paginate($request->input('limit', 10));

            return response()->json([
                'metadata' => [
                    'count' => $subjects->total(),
                    'search' => $request->search,
                    'limit' => $subjects->perPage(),
                    'offset' => $subjects->currentPage(),
                    'fields' => $request->fields ?? [],
                ],
                'subjects' => $subjects,
            ]);
        } catch (ModelNotFoundException $e) {
            Log::error('Student not found: ' . $e->getMessage());
            return response()->json(['error' => 'Student not found.'], 404);
        } catch (\Exception $e) {
            Log::error('Failed to fetch subjects: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch subjects.'], 500);
        }
    }

    public function store(Request $request, $student_id)
    {
        try {
            $validatedData = $request->validate([
                'subject_code' => 'required|string|max:255',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'instructor' => 'nullable|string|max:255',
                'schedule' => 'nullable|string|max:255',
                'prelims' => 'nullable|numeric',
                'midterms' => 'nullable|numeric',
                'pre_finals' => 'nullable|numeric',
                'finals' => 'nullable|numeric',
                'date_taken' => 'required|date_format:Y-m-d',
            ]);

            $student = Student::findOrFail($student_id);
            $subject = $student->subjects()->create($validatedData);

            // Calculate average grade and remarks
            $average_grade = ($subject->prelims + $subject->midterms + $subject->pre_finals + $subject->finals) / 4 ?? null;
            $remarks = $average_grade >= 3.0 ? 'PASSED' : 'FAILED';

            $subject->update([
                'average_grade' => $average_grade,
                'remarks' => $remarks,
            ]);

            return response()->json($subject, 201);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (ModelNotFoundException $e) {
            Log::error('Student not found: ' . $e->getMessage());
            return response()->json(['error' => 'Student not found.'], 404);
        } catch (\Exception $e) {
            Log::error('Failed to store subject: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to store subject.'], 500);
        }
    }

    public function show($student_id, $subject_id)
    {
        try {
            $student = Student::findOrFail($student_id);
            $subject = $student->subjects()->findOrFail($subject_id);

            return response()->json($subject);
        } catch (ModelNotFoundException $e) {
            Log::error('Subject not found: ' . $e->getMessage());
            return response()->json(['error' => 'Subject not found.'], 404);
        } catch (\Exception $e) {
            Log::error('Failed to fetch subject: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch subject.'], 500);
        }
    }

    public function update(Request $request, $student_id, $subject_id)
    {
        try {
            $validatedData = $request->validate([
                'subject_code' => 'sometimes|required|string|max:255',
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'instructor' => 'nullable|string|max:255',
                'schedule' => 'nullable|string|max:255',
                'prelims' => 'nullable|numeric',
                'midterms' => 'nullable|numeric',
                'pre_finals' => 'nullable|numeric',
                'finals' => 'nullable|numeric',
                'date_taken' => 'sometimes|required|date_format:Y-m-d',
            ]);

            $student = Student::findOrFail($student_id);
            $subject = $student->subjects()->findOrFail($subject_id);

            // Update subject with validated data
            $subject->update($validatedData);

            // Recalculate average grade and remarks
            $average_grade = ($subject->prelims + $subject->midterms + $subject->pre_finals + $subject->finals) / 4 ?? null;
            $remarks = $average_grade >= 3.0 ? 'PASSED' : 'FAILED';

            $subject->update([
                'average_grade' => $average_grade,
                'remarks' => $remarks,
            ]);

            return response()->json($subject);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (ModelNotFoundException $e) {
            Log::error('Student or Subject not found: ' . $e->getMessage());
            return response()->json(['error' => 'Student or Subject not found.'], 404);
        } catch (\Exception $e) {
            Log::error('Failed to update subject: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update subject.'], 500);
        }
    }
}
