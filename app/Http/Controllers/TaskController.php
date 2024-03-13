<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{

    public function getTask()
    {
        try {
            $user = Auth::user();
            $tasks = $user->tasks()->orderBy('created_at', 'desc')->get(['id', 'task_title', 'task_description', 'status']);
            return response()->json(['tasks' => $tasks]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function storeTask(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string|max:1000',
            ]);
            $user = Auth::user();
            $todo = new Task([
                'task_title' => $request->title,
                'task_description' => $request->description,
                'user_id' => $user->id,
                'status' => 0,
            ]);
            $todo->save();
            return response()->json([
                'message' => 'Todo stored successfully',
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateTask(Request $request, $id)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string|max:1000',
            ]);
            $user = Auth::user();
            $task = Task::where('user_id', $user->id)->findOrFail($id);
            $task->task_title = $request->title;
            $task->task_description = $request->description;
            $task->save();
            return response()->json([
                'message' => 'Task updated successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateStatus($id)
    {
        try {
            $user = Auth::user();
            $task = Task::where('user_id', $user->id)->findOrFail($id);
            $task->status = !$task->status;
            $task->save();
            return response()->json([
                'message' => 'Task updated successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function removeTask($id)
    {
        try {
            $user = Auth::user();
            $task = Task::where('user_id', $user->id)->findOrFail($id);
            $task->delete();
            return response()->json([
                'message' => 'Task Deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
