@extends('layouts.app')

@section('content')
    <div class="row">
      
        <!-- Generator Form -->
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Generate New Label</h5>
                </div>
                <div class="card-body">
                      {{-- Output errors --}}
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('labels.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Product Name</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="type" class="form-label">Barcode Type</label>
                            <select name="type" id="type" class="form-select">
                                <option value="0">Code128</option>
                                <option value="1">QR Code</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Generate & Save</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Saved Labels -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Saved Labels</h5>
                    <a href="{{ route('labels.export.pdf') }}" class="btn btn-light btn-sm">Export to PDF</a>
                </div>
                <div class="card-body">
                    <!-- Search Form -->
                    <form method="GET" action="{{ route('labels.search') }}" class="mb-3">
                        <div class="input-group">
                            <input type="text" name="query" class="form-control" placeholder="Search labels...">
                            <button type="submit" class="btn btn-secondary">Search</button>
                        </div>
                    </form>

                    <!-- Labels Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($labels as $label)
                                    <tr>
                                        <td>{{ $label->name }}</td>
                                        <td>{{ $label->type == 0 ? 'Code128' : 'QR Code' }}</td>
                                        <td>
                                            <a href="{{ route('labels.show', $label->id) }}" class="btn btn-info btn-sm">View</a>
                                            <a href="{{ route('labels.export', $label->id) }}" class="btn btn-success btn-sm">Export</a>
                                            <form action="{{ route('labels.destroy', $label->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">No labels found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection