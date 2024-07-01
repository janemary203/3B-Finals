<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;
use App\Models\Student;
use Faker\Factory as Faker;

class SubjectSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Get all student IDs to use for subject seeding
        $studentIds = Student::pluck('id')->toArray();

        foreach (range(1, 50) as $index) {
            Subject::create([
                'student_id' => $faker->randomElement($studentIds),
                'subject_code' => $faker->unique()->lexify('???###'),
                'name' => $faker->sentence(3),
                'description' => $faker->sentence(6),
                'instructor' => $faker->name,
                'schedule' => $faker->randomElement(['MWF 08:00-10:00', 'TTh 13:00-15:00', 'MW 10:00-12:00']),
                'prelims' => $faker->randomFloat(2, 50, 100),
                'midterms' => $faker->randomFloat(2, 50, 100),
                'pre_finals' => $faker->randomFloat(2, 50, 100),
                'finals' => $faker->randomFloat(2, 50, 100),
                'date_taken' => $faker->dateTimeBetween('-4 years', 'now')->format('Y-m-d'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
