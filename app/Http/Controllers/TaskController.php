<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskResource;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Get a list of all tasks.
     *
     * @return AnonymousResourceCollection
     */
    public function index()
    {
        //
        $tasks = Task::with('user')->get();

        // return response()->json($tasks);
        return TaskResource::collection($tasks);

    }

    /**
     * Create a new task.
     * 
     */
    public function store(StoreTaskRequest $request)
    {
        //
        $validatedData = $request->validated();

        // $task = Task::create($validatedData);
        $task = new Task($validatedData);

        $task->user_id = auth()->id();

        $task->save();

        return response()->json([
            'message' => 'Task created successfully!',
            // 'task' => $task
            'task' => new TaskResource($task)
        ], 201);
    }

    /**
     * Get details for a specific task.
     * 
     */
    public function show(Task $task)
    {
        //
    $task->load('user');

        // return response()->json($task);
        return new TaskResource($task);
    }

    /**
     * Update the specified task.
     * 
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        //
        $validatedData = $request->validated();

        $task->fill($validatedData);

        $task->save();

        return response()->json([
            'message' => 'Task updated successfully',
            // 'task' => $task
            'task' => new TaskResource($task)
        ]);
    }

    /**
     * Delete a specific task.
     * 
     */
    public function destroy(Task $task)
    {
        //
        $task->delete();

        return response()->noContent();
    }
}
