<?php

// File: database/factories/SubjectFactory.php

namespace Database\Factories;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubjectFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Subject::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'subject_code' => $this->faker->unique()->lexify('???###'),
            'name' => $this->faker->sentence(2),
            'student_id' => \App\Models\Student::factory(),
            'description' => $this->faker->paragraph(),
            'instructor' => $this->faker->name(),
            'schedule' => $this->faker->sentence(),
            'prelims' => $this->faker->randomFloat(2, 0, 100),
            'midterms' => $this->faker->randomFloat(2, 0, 100),
            'pre_finals' => $this->faker->randomFloat(2, 0, 100),
            'finals' => $this->faker->randomFloat(2, 0, 100),
            'average_grade' => null,
            'remarks' => null,
            'date_taken' => $this->faker->date(),
        ];
    }
}
