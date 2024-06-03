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
                        Notification
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.notifications.store') }}" method="POST" enctype="multipart/form-data" id="basic-form">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <input type="hidden" name="_is_special_offer" value="1">
                                    <div class="row">
                                        <div class="form-group">
                                            <label class="form-label">Title <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="title" placeholder="Title"
                                                value="{{ old('title') }}" required>
                                                @error('title')
                                                <span class="invalid-feedback form-invalid fw-bold" role="alert">
                                                    {{ $message }}
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- <div class="row">
                                        <div class="form-group">
                                            <label for="name" class="mt-2">Location <span class="text-danger">*</span></label>
                                            <input type="text" name="location" id="location" class="form-control @error('location') is-invalid @enderror" placeholder="Location" value="{{ old('location', isset($data) ? $data->location : '') }}" required>
                                            @error('location')
                                                <span class="invalid-feedback form-invalid fw-bold" role="alert">
                                                    {{ $message }}
                                                </span>
                                            @enderror
                                        </div>

                                        <!-- <div class="form-group">
                                            <label class="form-label">Select Users</label>
                                            <select name="users[]"  class="form-control select2" multiple>
                                                @foreach($user as $item)
                                                @if(!empty($item->name))
                                                <option value="{{$item->id ?? ''}}">{{$item->name ?? ''}}</option>
                                                @endif
                                                @endforeach
                                            </select>
                                            @error('users')
                                                <span class="invalid-feedback form-invalid fw-bold" role="alert">
                                                    {{ $message }}
                                                </span>
                                            @enderror
                                    </div> -->
                                    </div> --}}

                                    {{-- <div class="row">
                                        <div class="form-group col-md-6">
                                            <label for="name" class="mt-2"> Latitude </label>
                                            <input type="text" name="latitude" id="latitude" class="form-control @error('latitude') is-invalid @enderror" placeholder="Latitude" value="{{ old('latitude', isset($data) ? $data->latitude : '') }}" readonly>
                                            @error('latitude')
                                                <span class="invalid-feedback form-invalid fw-bold" role="alert">
                                                    {{ $message }}
                                                </span>
                                            @enderror
                                        </div>
                                        
                                        <div class="form-group col-md-6">
                                            <label for="name" class="mt-2"> Longitude </label>
                                            <input type="text" name="longitude" id="longitude" class="form-control @error('longitude') is-invalid @enderror" placeholder="Longitude" value="{{ old('longitude', isset($data) ? $data->longitude : '') }}" readonly>
                                            @error('longitude')
                                            <span class="invalid-feedback form-invalid fw-bold" role="alert">
                                                {{ $message }}
                                            </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group">
                                            <label class="mt-2">Notification Range <span class="text-danger">*</span></label>
                                            <select name="notificationRange" class="form-control form-select @error('notificationRange') is-invalid @enderror" required>
                                                <option value="" {{ old('notificationRange') ? ((old('notificationRange') == '') ? 'selected' : '' ) : ( (isset($data) && $data->notification_range == 0) ? 'selected' : '' ) }} >Select Range</option>
                                                @foreach($range as $key => $value) 
                                                    <option value={{$key}} {{ old('notificationRange') ? ((old('notificationRange') == $key) ? 'selected' : '' ) : ( (isset($data) && $data->notification_range == $key) ? 'selected' : '' ) }} >{{ $value }}</option>
                                                @endforeach
                                            </select>
                                            @error('notificationRange')
                                                <span class="invalid-feedback form-invalid fw-bold" role="alert">
                                                    {{ $message }}
                                                </span>
                                            @enderror
                                        </div>
                                    </div> --}}

                                    <div class="row mb-3">
                                        <div class="form-group">
                                            <label for="name" class="mt-2"> Message </label>
                                            <textarea name="body" class="@error('content') is-invalid @enderror w-100"></textarea>
                                            @error('content')
                                                <span class="invalid-feedback form-invalid fw-bold" role="alert">
                                                    {{ $message }}
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            
                            
                                <div class="mt-3">
                                    <input class="btn btn-primary" type="submit" value="{{ isset($data) && isset($data->id) ? 'Update' : 'Save' }}">
                                </div>
                        
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
<script async
    src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_GEOCODE_API_KEY') }}&libraries=places&callback=initMap">
</script>
<script>
function initMap() {
    window.addEventListener('load', initialize);
    function initialize() {
        var input = document.getElementById('location');
        var autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.addListener('place_changed', function () {
            var place = autocomplete.getPlace();
            
            document.getElementById("latitude").value = place.geometry['location'].lat();
            document.getElementById("longitude").value = place.geometry['location'].lng();
        });
    }
}
</script>
@endsection