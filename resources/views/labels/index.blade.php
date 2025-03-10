@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Generate New Label</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('labels.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Product Name</label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="type" class="form-label">Barcode Type</label>
                            <select name="type" id="type" class="form-select @error('type') is-invalid @enderror">
                                <option value="0" {{ old('type') == '0' ? 'selected' : '' }}>Code128</option>
                                <option value="1" {{ old('type') == '1' ? 'selected' : '' }}>QR Code</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantity</label>
                            <input type="number" name="quantity" id="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity', 1) }}" min="1" required>
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Generate & Save</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Saved Labels</h5>
                    <a href="{{ route('labels.export.pdf') }}" class="btn btn-light btn-sm">Export to PDF</a>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('labels.search') }}" class="mb-3">
                        <div class="input-group">
                            <input type="text" name="query" class="form-control" placeholder="Search labels...">
                            <button type="submit" class="btn btn-secondary">Search</button>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Unique ID</th>
                                    <th>Type</th>
                                    <th>Quantity</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($labels as $label)
                                    <tr>
                                        <td>{{ $label->name }}</td>
                                        <td>{{ $label->unique_id }}</td>
                                        <td>{{ $label->type == 0 ? 'Code128' : 'QR Code' }}</td>
                                        <td>{{ $label->quantity }}</td>
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
                                        <td colspan="5" class="text-center">No labels found.</td>
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