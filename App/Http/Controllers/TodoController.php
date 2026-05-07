<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    public function index()
    {
        $todos = Todo::latest()->paginate(10);
        return view('todos.index', compact('todos'));
    }

    public function create()
    {
        return view('todos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high'
        ]);

        Todo::create($request->all());

        return redirect()->route('todos.index')
            ->with('success', 'Todo created successfully!');
    }

    public function show(Todo $todo)
    {
        return view('todos.show', compact('todo'));
    }

    public function edit(Todo $todo)
    {
        return view('todos.edit', compact('todo'));
    }

    public function update(Request $request, Todo $todo)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high'
        ]);

        $todo->update($request->all());

        return redirect()->route('todos.index')
            ->with('success', 'Todo updated successfully!');
    }

    public function toggleStatus(Todo $todo)
    {
        $todo->update(['status' => !$todo->status]);
        
        return redirect()->route('todos.index')
            ->with('success', 'Todo status updated!');
    }

    public function destroy(Todo $todo)
    {
        $todo->delete();

        return redirect()->route('todos.index')
            ->with('success', 'Todo deleted successfully!');
    }
}
