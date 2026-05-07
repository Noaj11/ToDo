@extends('layouts.app')

@section('title', 'All Todos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-list"></i> My Todos ({{ $todos->total() }})</h1>
    <a href="{{ route('todos.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Todo
    </a>
</div>

<div class="row">
    @forelse($todos as $todo)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card {{ $todo->status ? 'border-success' : 'border-warning' }}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="card-title {{ $todo->status ? 'text-decoration-line-through text-muted' : '' }}">
                            {{ $todo->title }}
                        </h5>
                        <span class="badge bg-{{ $todo->priority == 'high' ? 'danger' : ($todo->priority == 'medium' ? 'warning' : 'secondary') }}">
                            {{ ucfirst($todo->priority) }}
                        </span>
                    </div>
                    
                    @if($todo->description)
                        <p class="card-text small text-muted">{{ Str::limit($todo->description, 100) }}</p>
                    @endif
                    
                    <div class="d-flex gap-2 mt-3">
                        <form action="{{ route('todos.toggle', $todo) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm {{ $todo->status ? 'btn-success' : 'btn-outline-success' }}">
                                <i class="fas fa-{{ $todo->status ? 'check' : 'square' }}"></i>
                                {{ $todo->status ? 'Completed' : 'Complete' }}
                            </button>
                        </form>
                        
                        <a href="{{ route('todos.edit', $todo) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        
                        <form action="{{ route('todos.destroy', $todo) }}" method="POST" style="display: inline;" 
                              onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                <h3>No todos yet!</h3>
                <a href="{{ route('todos.create') }}" class="btn btn-primary">Create your first todo</a>
            </div>
        </div>
    @endforelse
</div>

<div class="d-flex justify-content-center">
    {{ $todos->links() }}
</div>
@endsection
