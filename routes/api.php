<?php

use App\Http\Controllers\Api\TodoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
// Get all todos (with filters)
Route::get('/todos', [TodoController::class, 'index'])
    ->name('todos.index');

// Get single todo
Route::get('/todos/{todo}', [TodoController::class, 'show'])
    ->name('todos.show');

// Create new todo (Auth required)
Route::post('/todos', [TodoController::class, 'store'])
    ->middleware('auth:sanctum')
    ->name('todos.store');

// Update todo (Auth required)
Route::put('/todos/{todo}', [TodoController::class, 'update'])
    ->middleware('auth:sanctum')
    ->name('todos.update');

// Toggle todo status (Auth required)
Route::patch('/todos/{todo}/toggle', [TodoController::class, 'toggleStatus'])
    ->middleware('auth:sanctum')
    ->name('todos.toggle');

// Delete todo (Auth required)
Route::delete('/todos/{todo}', [TodoController::class, 'destroy'])
    ->middleware('auth:sanctum')
    ->name('todos.destroy');

// Stats endpoint
Route::get('/todos/stats', [TodoController::class, 'stats'])
    ->name('todos.stats');

// Filter endpoints
Route::get('/todos/pending', [TodoController::class, 'pending'])
    ->name('todos.pending');
Route::get('/todos/completed', [TodoController::class, 'completed'])
    ->name('todos.completed');

// Search todos
Route::get('/todos/search/{query}', [TodoController::class, 'search'])
    ->name('todos.search');

## 🆕 **Required API Controller**

### **app/Http/Controllers/Api/TodoController.php**
```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TodoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Todo::query()
            ->withCount('comments')
            ->latest();

        // Filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        $todos = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $todos,
            'message' => 'Todos retrieved successfully'
        ]);
    }

    public function stats(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'total' => Todo::count(),
                'pending' => Todo::pending()->count(),
                'completed' => Todo::completed()->count(),
                'high_priority' => Todo::where('priority', 'high')->count(),
            ]
        ]);
    }

    public function pending(): JsonResponse
    {
        $todos = Todo::pending()->latest()->limit(10)->get();
        return response()->json(['success' => true, 'data' => $todos]);
    }

    public function completed(): JsonResponse
    {
        $todos = Todo::completed()->latest()->limit(10)->get();
        return response()->json(['success' => true, 'data' => $todos]);
    }

    public function search($query): JsonResponse
    {
        $todos = Todo::where('title', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->limit(10)
            ->get();

        return response()->json(['success' => true, 'data' => $todos]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high'
        ]);

        $todo = Todo::create($request->all());

        return response()->json([
            'success' => true,
            'data' => $todo->loadCount('comments'),
            'message' => 'Todo created successfully!'
        ], 201);
    }

    public function show(Todo $todo): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $todo->loadCount('comments')
        ]);
    }

    public function update(Request $request, Todo $todo): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high'
        ]);

        $todo->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $todo->fresh()->loadCount('comments'),
            'message' => 'Todo updated successfully!'
        ]);
    }

    public function toggleStatus(Todo $todo): JsonResponse
    {
        $todo->update(['status' => !$todo->status]);

        return response()->json([
            'success' => true,
            'data' => $todo->fresh(),
            'message' => 'Todo status updated!'
        ]);
    }

    public function destroy(Todo $todo): JsonResponse
    {
        $todo->delete();

        return response()->json([
            'success' => true,
            'message' => 'Todo deleted successfully!'
        ]);
    }
}
