@extends('layouts.master')
@section('content')
<div class="content-wrapper">
    <!-- Content -->

    <div class="container-xxl flex-grow-1 container-p-y">
        @if(Session::has('success'))
            @section('scripts')
                <script>swal("Good job!", "{{ Session::get('success') }}", "success");</script>
            @endsection
        @endif

        @if(Session::has('error'))
            @section('scripts')
                <script>swal("Oops...", "{{ Session::get('error') }}", "error");</script>
            @endsection
        @endif
        <div class="row">
            <div class="col-lg-12">

                <div class="card">
                    <div class="card-header border-bottom">
                        {{isset($data) && isset($data->id) ? 'Edit Subscription ' : 'Create Subscription'}}
                        {{-- {{ isset($data) && isset($data->id) ? 'Edit Subscription ' : 'Create Subscription ' }} --}}
                    </div>
                    <div class="card-body">
                        
                        <form action="{{ route('admin.subscription-plan.store') }}" method="POST" enctype="multipart/form-data" id="basic-form">
                            @csrf
                            <input type="hidden" name="id" id="id" value="{{ isset($data) ? $data->id : '' }}">
                            
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="mt-2">Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" placeholder="Title" value="{{ old('title', isset($data) ? $data->title : '') }}" required>
                                    @error('title')
                                        <span class="invalid-feedback form-invalid fw-bold" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>

                               <div class="form-group col-md-6">
                                    <label class="mt-2">Sub Title <span class="text-danger">*</span></label>
                                    <input type="text" name="sub_title" class="form-control @error('sub_title') is-invalid @enderror" placeholder="Sub Title" value="{{ old('sub_title', isset($data) ? $data->sub_title : '') }}" required>
                                    @error('sub_title')
                                        <span class="invalid-feedback form-invalid fw-bold" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="name" class="mt-2"> Amount <span class="text-danger">*</span></label>
                                    <input type="text" name="amount" class="form-control @error('title') is-invalid @enderror" placeholder="amount" value="{{ old('amount', isset($data) ? $data->amount : '') }}" required>
                                    @error('title')
                                        <span class="invalid-feedback form-invalid fw-bold" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>

                               
                            <div class="form-group">
                                <label for="name" class="mt-2">Description </label>
                                <textarea name="description" class="@error('description') is-invalid @enderror w-100" rows="5" cols="50">{{ empty(old('description')) ? (isset($data) ? $data->description : '') : old('description') }}</textarea>
                                @error('description')
                                    <span class="invalid-feedback form-invalid fw-bold" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                           
                            <div class="form-group">
                                <label for="name" class="mt-2"> Features <span class="text-danger">*</span></label>
                                <textarea name="features" type="text" class="ckeditor @error('features') is-invalid @enderror" id="ckeditor" required>{{ empty(old('features')) ? (isset($data) ? $data->features : '') : old('features') }}</textarea>
                                @error('about')
                                    <span class="invalid-feedback form-invalid fw-bold" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                            
                            
                            <div class="mt-3">
                                <input class="btn btn-primary submit-btn" type="submit" value="{{ isset($data) && isset($data->id) ? 'Update' : 'Save' }}">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
</script>
@endsection