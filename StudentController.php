<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $studentQuery = Student::query();
    
        // Filter by year
        if ($request->has('year')) {
            $studentQuery->where('year', $request->year);
        }
    
        // Filter by course
        if ($request->has('course')) {
            $studentQuery->where('course', $request->course);
        }
    
        // Filter by section
        if ($request->has('section')) {
            $studentQuery->where('section', $request->section);
        }
    
        // Sort by a field
        if ($request->has('sort')) {
            $sortField = $request->sort;
            $sortOrder = $request->get('order', 'asc'); // default order is ascending
            $studentQuery->orderBy($sortField, $sortOrder);
        }
    
        // Search by firstname or lastname
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $studentQuery->where(function ($q) use ($searchTerm) {
                $q->where('firstname', 'like', '%' . $searchTerm . '%')
                  ->orWhere('lastname', 'like', '%' . $searchTerm . '%');
            });
        }
    
        // Limit and offset
        $limit = $request->get('limit', 10); // default limit is 10
        $offset = $request->get('offset', 0);
        $studentQuery->limit($limit)->offset($offset);
    
        // Select specific fields
        if ($request->has('fields')) {
            $fields = explode(',', $request->fields);
            $studentQuery->select($fields);
        }
    
        // Execute the query
        $students = $studentQuery->get();
    
        return response()->json([
            'metadata' => [
                'count' => $students->count(),
                'search' => $request->search,
                'limit' => $limit,
                'offset' => $offset,
                'fields' => $request->fields ?? [],
            ],
            'students' => $students,
        ]);
    }
    
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'birthdate' => 'required|date_format:Y-m-d',
            'sex' => 'required|in:MALE,FEMALE',
            'address' => 'required|string|max:255',
            'year' => 'required|integer',
            'course' => 'required|string|max:255',
            'section' => 'required|string|max:255',
        ]);

        $student = Student::create($validatedData);

        return response()->json($student, 201);
    }

    public function show($id)
    {
        try {
            $student = Student::findOrFail($id);
            return response()->json($student);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Student not found.'], 404);
        }
    }
    
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'firstname' => 'sometimes|required|string|max:255',
            'lastname' => 'sometimes|required|string|max:255',
            'birthdate' => 'sometimes|required|date_format:Y-m-d',
            'sex' => 'sometimes|required|in:MALE,FEMALE',
            'address' => 'sometimes|required|string|max:255',
            'year' => 'sometimes|required|integer',
            'course' => 'sometimes|required|string|max:255',
            'section' => 'sometimes|required|string|max:255',
        ]);

        $student = Student::findOrFail($id);
        $student->update($validatedData);

        return response()->json($student);
    }
}

