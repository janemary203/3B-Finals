<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'subject_code', 'name', 'description', 'instructor', 'schedule',
        'prelims', 'midterms', 'pre_finals', 'finals', 'date_taken', 'average_grade', 'remarks',
    ];

    protected $casts = [
        'grades' => 'array', // Cast grades as array
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
