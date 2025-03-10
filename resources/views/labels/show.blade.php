@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Label: {{ $label->name }}</h5>
                    <a href="{{ route('labels.index') }}" class="btn btn-light btn-sm">Back to Labels</a>
                </div>
                <div class="card-body">
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
                    <form method="POST" action="{{ route('labels.update', $label->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="name" class="form-label">Product Name</label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $label->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="type" class="form-label">Barcode Type</label>
                            <select name="type" id="type" class="form-select @error('type') is-invalid @enderror">
                                <option value="0" {{ old('type', $label->type) == 0 ? 'selected' : '' }}>Code128</option>
                                <option value="1" {{ old('type', $label->type) == 1 ? 'selected' : '' }}>QR Code</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantity</label>
                            <input type="number" name="quantity" id="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity', $label->quantity) }}" min="1" required>
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Update Label</button>
                    </form>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Preview (Unique ID: {{ $label->unique_id }})</h5>
                </div>
                <div class="card-body text-center">
                    <img src="{{ $label->type == 0 ? asset($label->barcode) : 'data:image/png;base64,' . $label->barcode }}" 
                         alt="Barcode" 
                         class="img-fluid" 
                         style="max-height: 200px; width: auto;">
                    <div class="mt-3">
                        <a href="{{ route('labels.export', $label->id) }}" class="btn btn-success btn-sm">Download Preview</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection