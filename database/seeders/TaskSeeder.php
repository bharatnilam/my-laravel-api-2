<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $users = User::all();

        $statuses = ['pending', 'in_progress', 'completed', 'cancelled'];
        $priorities = ['low', 'medium', 'high', 'urgent'];

        for ($i = 0; $i < 40; $i++) {
            $dueDate = now()->addDays(rand(1,30));

            Task::create([
                'title' => fake()->sentence(),
                'description' => fake()->paragraph(),
                'user_id' => $users->random()->id,
                'status' => $statuses[array_rand($statuses)],
                'priority' => $priorities[array_rand($priorities)],
                'due_date' => $dueDate
            ]);
        }
    }
}
