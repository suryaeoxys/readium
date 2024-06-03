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
                        {{ isset($data) && isset($data->id) ? 'Edit Product' : 'Create Product' }}
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" id="basic-form">
                            @csrf
                            <input type="hidden" name="id" id="id" value="{{ isset($data) ? $data->id : '' }}">

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="mt-2"> Category <span class="text-danger">*</span></label>
                                    <select name="category" class="form-control form-select category" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                        <option value="{{ $category->id }}" @if(isset($data) && $data->category_id == $category->id) Selected @endif>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <input type="hidden" name="sub_category_id" id="sub_category_id" value="{{ isset($data) && isset($data->subCategories) ? $data->subCategories->pluck('id') : '' }}">
                                    <label class="mt-2">Sub Category <span class="text-danger">*</span></label>
                                    <select name="sub_cat[]" class="select2 subCategory form-control form-select" multiple required>
                                    </select>
                                </div>


                            </div>

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="mt-2"> Publisher <span class="text-danger">*</span></label>
                                    <select name="publisher" class="form-control form-select publisher" required>
                                        <option value="">Select Publisher</option>
                                        @foreach($publishers as $publisher)
                                        <option value="{{ $publisher->id }}" @if(isset($data) && $data->publisher_id == $publisher->id) Selected @endif>{{ $publisher->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('publisher')
                                    <span class="invalid-feedback form-invalid fw-bold" role="alert">
                                        {{ $message }}
                                    </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="mt-2"> Author <span class="text-danger">*</span></label>
                                    <select name="author" class="form-control form-select author" required>
                                        <option value="">Select author</option>
                                        @foreach($authors as $author)
                                        <option value="{{ $author->id }}" @if(isset($data) && $data->author_id == $author->id) Selected @endif>{{ $author->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('author')
                                    <span class="invalid-feedback form-invalid fw-bold" role="alert">
                                        {{ $message }}
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="title" class="mt-2"> Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" placeholder="Title" value="{{ old('title', isset($data) ? $data->name : '') }}" required>
                                    @error('title')
                                    <span class="invalid-feedback form-invalid fw-bold" role="alert">
                                        {{ $message }}
                                    </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="yop" class="mt-2"> Year of publication <span class="text-danger">*</span></label>
                                    <input type="number" name="yop" class="form-control @error('yop') is-invalid @enderror" placeholder="Year of publication"  type="number" min="1500" max="2099" step="1" value="{{ old('yop', isset($data) ? $data->year_of_publication : '') }}" required>
                                    @error('yop')
                                    <span class="invalid-feedback form-invalid fw-bold" role="alert">
                                        {{ $message }}
                                    </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="name" class="mt-2"> Original Title <span class="text-danger">*</span></label>
                                    <input type="text" name="original_title" class="form-control @error('original_title') is-invalid @enderror" placeholder="Original Title" value="{{ old('title', isset($data) ? $data->original_title : '') }}" required>
                                    @error('original_title')
                                    <span class="invalid-feedback form-invalid fw-bold" role="alert">
                                        {{ $message }}
                                    </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="isbn" class="mt-2"> ISBN <span class="text-danger">*</span></label>
                                    <input type="text" name="isbn" class="form-control @error('isbn') is-invalid @enderror" placeholder="ISBN" value="{{ old('isbn', isset($data) ? $data->isbn : '') }}" required>
                                    @error('isbn')
                                    <span class="invalid-feedback form-invalid fw-bold" role="alert">
                                        {{ $message }}
                                    </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="no_of_page" class="mt-2"> Number Of Pages <span class="text-danger">*</span></label>
                                    <input type="number" name="no_of_page" class="form-control @error('no_of_page') is-invalid @enderror" placeholder="No of pages" value="{{ old('no_of_page', isset($data) ? $data->no_of_page : '') }}" required>
                                    @error('no_of_page')
                                    <span class="invalid-feedback form-invalid fw-bold" role="alert">
                                        {{ $message }}
                                    </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    @if(!empty($data->main_image))
                                    <div class="mt-3">
                                        <span class="pip" data-title="{{$data->image}}">
                                            <img src="{{ url(config('app.product_image')).'/'.$data->main_image ?? '' }}" alt="" width="150" height="100">
                                        </span>
                                    </div>
                                    @endif
                                    <label for="name" class="mt-2"> Image <span class="text-danger">*</span> <span class="text-danger info">(Only jpeg, png, jpg files allowed)</span></label>
                                    <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/jpeg,image/png" {{ isset($data) && isset($data->id) ? '' : 'required' }}>
                                    <input type="hidden" class="form-control" name="imageOld" value="{{ isset($data) ? $data->main_image : ''}}">
                                    @error('image')
                                    <span class="invalid-feedback form-invalid fw-bold" role="alert">
                                        {{ $message }}
                                    </span>
                                    @enderror
                                </div>


                            </div>


                     


                            <div class="row">
                            <div class="form-group col-md-6 d-flex">
                                    @if(!empty($data->pdf_mp3))
                                    <div class="mt-5 col-md-6">
                                        <span class="pip" data-title="{{$data->pdf_mp3}}">
                                            <a href="{{url(config('app.media_content').'/'.$data->pdf_mp3)}}" class="btn btn-sm btn-info" download>Download</a>
                                        </span>
                                    </div>
                                    @endif
                                    <div class="col-md-6">
                                        <label for="name" class="mt-2"> MP3 /PDF <span class="text-danger">*</span> </label>
                                        <input type="file" name="pdf_mp3" class="form-control @error('pdf_mp3') is-invalid @enderror"  {{ isset($data) && isset($data->id) ? '' : 'required' }}>
                                        <input type="hidden" class="form-control" name="pdf_mp3Old" value="{{ isset($data) ? $data->pdf_mp3 : ''}}">
                                        @error('pdf_mp3')
                                        <span class="invalid-feedback form-invalid fw-bold" role="alert">
                                            {{ $message }}
                                        </span>
                                        @enderror
                                    </div>
                                </div>
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
                            <div class="form-group">
                                <label for="name" class="mt-2"> Content </label>
                                <textarea name="content" class="ckeditor @error('content') is-invalid @enderror" id="ckeditor">{{ empty(old('content')) ? (isset($data) ? $data->discription : '') : old('content') }}</textarea>
                                @error('content')
                                <span class="invalid-feedback form-invalid fw-bold" role="alert">
                                    {{ $message }}
                                </span>
                                @enderror
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
<script>
    $(document).ready(function() {
        var category_id = $('.category').val();
        var selected_sub_category = $("#sub_category_id").val();
        getSubCategory(category_id, selected_sub_category);
    });
    $(document).on('change', '.category', function() {
        var category_id = $(this).val();
        if (category_id == '') {
            category_id = 0;
        }
        getSubCategory(category_id);
    });


    function getSubCategory(category_id, selected_sub_category = []) {
        var route = "/get-child-cat/" + category_id + '?selected_sub_category=' + selected_sub_category;
        console.log(selected_sub_category);
        $.ajax({
            method: 'GET',
            url: route,
            success: function(response) {
                if (response.success == true) {
                    $('.subCategory').html(response.data);
                }
            },
        });
    }
</script>

@endsection