<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_unauthenticated_users_cannot_view_tasks(): void {
        $response = $this->getJson('/api/tasks');

        $response->assertStatus(401);
    }

    public function test_authenticated_users_can_view_tasks(): void {
        $user = User::factory()->create();

        $task = Task::factory()->create(['user_id' => $user->id]);

        $token = $user->createToken('test_token')->plainTextToken;

        $response = $this->getJson('/api/tasks', [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'title' => $task->title,
            'description' => $task->description,
            'is_complete' => (int)$task->is_complete,
            'user_id' => $user->id
        ]);

        $response->assertJsonStructure([
            '*' => ['id', 'title', 'description', 'is_complete', 'user_id', 'user']
        ]);
    }

    public function test_unauthenticated_users_cannot_create_tasks(): void {
        $task = [
            'title' => 'Task from unauthenticated test',
            'description' => 'This should not be created'
        ];

        $response = $this->postJson('/api/tasks', $task);

        $response->assertStatus(401);

        $this->assertDatabaseMissing('tasks', [
            'title' => $task['title'],
        ]);
    }

    public function test_authenticated_users_can_create_tasks(): void {
        $user = User::factory()->create();

        $token = $user->createToken('test_token')->plainTextToken;
        
        $task = [
            'title' => 'Task from authenticated test',
            'description' => 'This should be created',
            'is_complete' => false
        ];

        $response = $this->postJson('/api/tasks', $task, [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(201);

        $response->assertJsonFragment([
            'title' => $task['title'],
            'description' => $task['description'],
            'is_complete' => $task['is_complete'],
            'user_id' => $user->id
        ]);

        $response->assertJsonStructure([
            'message',
            'task' => [
                'id', 'title', 'description', 'is_complete', 'user_id', 'created_at', 'updated_at'
            ]
        ]);

        $this->assertDatabaseHas('tasks', [
            'title' => $task['title'],
            'description' => $task['description'],
            'is_complete' => $task['is_complete'],
            'user_id' => $user->id
        ]);
    }

    public function test_authenticated_users_get_validation_errors_when_creating_task_with_invalid_data(): void {
        $user = User::factory()->create();

        $token = $user->createToken('test_token')->plainTextToken;
        
        $task = [
            // 'title' => 'Task from authenticated test',
            'description' => 'Attempt to create task with missing title',
            'is_complete' => false
        ];

        $response = $this->postJson('/api/tasks', $task, [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['title']);
    }

    public function test_unauthenticated_users_cannot_view_specific_task(): void {
        $user = User::factory()->create();

        $task = Task::factory()->create(['user_id' => $user->id]);
        
        $response = $this->getJson('/api/tasks/' . $task['id']);

        $response->assertStatus(401);
    }

    public function test_authenticated_users_can_view_specific_task(): void {
        $user = User::factory()->create();

        $task = Task::factory()->create(['user_id' => $user->id]);

        $token = $user->createToken('test_token')->plainTextToken;

        $response = $this->getJson('/api/tasks/' . $task['id'], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertJsonFragment([
            'title' => $task->title,
            'description' => $task->description,
            'is_complete' => (int)$task->is_complete,
            'user_id' => $user->id
        ]);

        $response->assertJsonStructure([
            'id',
            'title',
            'description',
            'is_complete',
            'user_id',
            'user'
        ]);
    }

    public function test_authenticated_users_get_404_for_viewing_non_existent_task(): void {
        $user = User::factory()->create();

        $task = Task::factory()->create(['user_id' => $user->id]);

        $token = $user->createToken('test_token')->plainTextToken;

        $nonExistentTaskId = 999;

        $response = $this->getJson('/api/tasks/' . $nonExistentTaskId, [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(404);
    }

    public function test_unauthenticated_users_cannot_update_specific_task(): void {
        $user = User::factory()->create();

        $task = Task::factory()->create(['user_id' => $user->id]);

        $updateData = [
            'title' => 'Updated title without auth',
            'is_complete' => true
        ];

        $response = $this->putJson('/api/tasks/' . $task['id'], $updateData);

        $response->assertStatus(401);
    }

    public function test_authenticated_users_can_update_specific_task_with_valid_data(): void {
        $user = User::factory()->create();

        $task = Task::factory()->create(['user_id' => $user->id]);

        $token = $user->createToken('test_token')->plainTextToken;

        $updateData = [
            'title' => 'Updated title for this task',
            'description' => 'This description is updated',
            'is_complete' => true
        ];

        $response = $this->putJson('/api/tasks/' . $task['id'], $updateData, [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'id' => $task->id,
            'title' => $updateData['title'],
            'description' => $updateData['description'],
            'is_complete' => $updateData['is_complete'],
            'user_id' => $user->id
        ]);

        $response->assertJsonStructure([
            'message',
            'task' => [
                'id', 'title', 'description', 'is_complete', 'user_id', 'created_at', 'updated_at'
            ]
        ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => $updateData['title'],
            'description' => $updateData['description'],
            'is_complete' => $updateData['is_complete'],
            'user_id' => $user->id
        ]);
    }

    public function test_authenticated_users_get_validation_errors_when_updating_with_invalid_data(): void {
        $user = User::factory()->create();

        $task = Task::factory()->create(['user_id' => $user->id]);

        $token = $user->createToken('test_token')->plainTextToken;

        $updateData = [
            'title' => '',
            'description' => 'Attempt to update task with invalid title'
        ];

        $response = $this->putJson('/api/tasks/' . $task['id'], $updateData, [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(422);
        
        $response->assertJsonValidationErrors(['title']);
    }

    public function test_authenticated_users_get_404_for_updating_non_existent_task(): void {
        $user = User::factory()->create();

        $task = Task::factory()->create(['user_id' => $user->id]);

        $token = $user->createToken('test_token')->plainTextToken;

        $updateData = [
            'title' => 'Task does not exist',
            'description' => 'No task should be updated'
        ];

        $nonExistentTaskId = 999;

        $response = $this->putJson('/api/tasks/' . $nonExistentTaskId, $updateData, [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(404);
    }

    public function test_unauthenticated_users_cannot_delete_specific_task(): void {
        $user = User::factory()->create();

        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->deleteJson('/api/tasks/' . $task['id']);

        $response->assertStatus(401);
    }

    public function test_authenticated_users_can_delete_specific_task(): void {
        $user = User::factory()->create();

        $task = Task::factory()->create(['user_id' => $user->id]);

        $token = $user->createToken('test_token')->plainTextToken;

        $response = $this->deleteJson('/api/tasks/' . $task['id'], [], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(204);

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id
        ]);
    }

    public function test_authenticated_users_get_404_for_deleting_non_existent_task(): void {
        $user = User::factory()->create();

        $task = Task::factory()->create(['user_id' => $user->id]);

        $token = $user->createToken('test_token')->plainTextToken;

        $nonExistentTaskId = 999;

        $response = $this->deleteJson('/api/tasks/' . $nonExistentTaskId, [], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(404);
    }
}
