@extends('layouts.master')
@section('content')
<div class="content-wrapper">
    <!-- Content -->

    <div class="container-xxl flex-grow-1 container-p-y">
        @if(Session::has('success'))
        @section('scripts')
        <script>
            swal("Good job!", "{{ Session::get('success') }}", "success");
        </script>
        @endsection
        @endif

        @if(Session::has('error'))
        @section('scripts')
        <script>
            swal("Oops...", "{{ Session::get('error') }}", "error");
        </script>
        @endsection
        @endif
        <div class="row">
            <div class="col-lg-12">

                <div class="card">
                    <div class="card-header border-bottom">
                        {{ isset($data) && isset($data->id) ? 'Edit Author' : 'Create Author' }}
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.authors.store') }}" method="POST" enctype="multipart/form-data" id="basic-form">
                            @csrf
                            <input type="hidden" name="id" id="id" value="{{ isset($data) ? $data->id : '' }}">
                            <input type="hidden" name="slug" id="slug" value="{{ isset($data) ? $data->slug : '' }}">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="name" class="mt-2"> Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="name" value="{{ old('name', isset($data) ? $data->name : '') }}" required>
                                    @error('name')
                                    <span class="invalid-feedback form-invalid fw-bold" role="alert">
                                        {{ $message }}
                                    </span>
                                    @enderror
                                </div>

                                <div class="form-group {{ isset($data) ? 'col-md-6' : '' }} col-md-6">
                                    <label for="name" class="mt-2"> Image  <span class="text-danger info">(Only jpeg, png, jpg files allowed)</span></label>
                                    <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/jpeg,image/png" {{ isset($data) && isset($data->id) ? '' : '' }}>
                                    <input type="hidden" class="form-control" name="imageOld" value="{{ isset($data) ? $data->image : ''}}">
                                    @error('image')
                                    <span class="invalid-feedback form-invalid fw-bold" role="alert">
                                        {{ $message }}
                                    </span>
                                    @enderror
                                </div>

                                @if(!empty($data->image))
                                <div class="form-group col-md-6">
                                    <div class="mt-3">
                                        <span class="pip" data-title="{{$data->image}}">
                                            <img src="{{ url(config('app.author_image')).'/'.$data->image ?? '' }}" alt="" width="150" height="100">
                                        </span>
                                    </div>
                                </div>
                                @endif
                                <div class="form-group col-md-6">
                                    <label class="mt-2"> Status <span class="text-danger">*</span></label>
                                    <select name="status" class="form-control form-select @error('status') is-invalid @enderror" required>
                                        <option value="" {{ old('status') ? ((old('status') == '') ? 'selected' : '' ) : ( (isset($data) && $data->status == 0) ? 'selected' : '' ) }}>Select Status</option>
                                        <option value="1" {{ old('status') ? ((old('status') == 1) ? 'selected' : '' ) : ( (isset($data) && $data->status == 1) ? 'selected' : '' ) }}>Active</option>
                                        <option value="0" {{ old('status') ? ((old('status') == 0) ? 'selected' : '' ) : ( (isset($data) && $data->status == 0) ? 'selected' : '' ) }}>In-Active</option>
                                    </select>
                                    @error('status')
                                    <span class="invalid-feedback form-invalid fw-bold" role="alert">
                                        {{ $message }}
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-3">
                                <input class="btn btn-primary" type="submit" value="{{ isset($data) && isset($data->id) ? 'Update' : 'Save' }}">
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

@endsection