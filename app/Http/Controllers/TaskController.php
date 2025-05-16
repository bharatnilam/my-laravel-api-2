<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $tasks = Task::with('user')->get();

        return response()->json($tasks);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'sometimes|string',
            'is_complete' => 'sometimes|boolean'
        ]);

        // $task = Task::create($validatedData);
        $task = new Task($validatedData);

        $task->user_id = auth()->id();

        $task->save();

        return response()->json([
            'message' => 'Task created successfully!',
            'task' => $task
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        //
        return response()->json($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        //
        $validatedData = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|nullable',
            'is_complete' => 'sometimes|boolean'
        ]);

        $task->fill($validatedData);

        $task->save();

        return response()->json([
            'message' => 'Task updated successfully',
            'task' => $task
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        //
        $task->delete();

        return response()->noContent();
    }
}
