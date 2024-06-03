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
                        Create User
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data" id="basic-form">
                            @csrf
                            <input type="hidden" name="id" id="id" value="{{ isset($data) ? $data->id : '' }}">
                            
                            <h5 class="fw-bolder">{{ 'Basic Information' }}</h5>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="name" class="mt-2">First Name <span class="text-danger">*</span></label>
                                    <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" placeholder="First Name" value="{{ old('first_name', isset($data) ? $data->first_name : '') }}" required>
                                    @error('first_name')
                                        <span class="invalid-feedback form-invalid fw-bold" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="name" class="mt-2"> Last Name <span class="text-danger">*</span></label>
                                    <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" placeholder="Last Name" value="{{ old('last_name', isset($data) ? $data->last_name : '') }}" required>
                                    @error('last_name')
                                        <span class="invalid-feedback form-invalid fw-bold" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="name" class="mt-2"> Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Name" value="{{ old('name', isset($data) ? $data->name : '') }}" required>
                                    @error('name')
                                        <span class="invalid-feedback form-invalid fw-bold" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="name" class="mt-2"> Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email" value="{{ old('email', isset($data) ? $data->email : '') }}" required>
                                    @error('email')
                                        <span class="invalid-feedback form-invalid fw-bold" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                {{-- <div class="form-group col-md-6 mt-auto">
                                    <label for="name" class="mt-2"> Phone <span class="text-danger">*</span></label>
                                    <input type="number" name="phone" class="form-control spin @error('phone') is-invalid @enderror" placeholder="Phone" value="{{ old('phone', isset($data) ? $data->phone : '') }}" min="0" required>
                                    @error('phone')
                                        <span class="invalid-feedback form-invalid fw-bold" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div> --}}

                                <div class="form-group col-md-6">
                                    @if(!empty($data->profileImage))
                                        <div class="mt-3">
                                            <span class="pip" data-title="{{$data->profileImage}}">
                                                <img src="{{ url(config('app.profile_image')).'/'.$data->profileImage ?? '' }}" alt="" width="150" height="100">
                                            </span>
                                        </div>
                                    @endif
                                    <label for="name" class="mt-2"> Profile Image <span class="text-danger info">(Only jpeg, png, jpg files allowed)</span></label>
                                    <input type="file" name="profileImage" class="form-control @error('profileImage') is-invalid @enderror" accept="image/jpeg,image/png">
                                    <input type="hidden" class="form-control" name="profileImageOld" value="{{ isset($data) ? $data->profileImage : ''}}">
                                    @error('profileImage')
                                        <span class="invalid-feedback form-invalid fw-bold" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group col-md-6 password-field">
                                    <label for="name" class="mt-2"> Password  <span class="text-danger">{{ isset($data) && isset($data->id) ? '' : '*' }}</span> <i class="mdi mdi-information-outline" data-toggle="tooltip" data-placement="right" title="Password must contain atleast one Lower case letter, atleast one Upper case letter, atleast one Number and atleast one Special character."></i></label>
                                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password" minlength="8" {{ isset($data) ? '' : 'required' }}>
                                    @error('password')
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

        var input_1 = document.getElementById('store_location');
        var autocomplete_1 = new google.maps.places.Autocomplete(input_1);
        autocomplete_1.addListener('place_changed', function () {
            var place_1 = autocomplete_1.getPlace();

            document.getElementById("store_latitude").value = place_1.geometry['location'].lat();
            document.getElementById("store_longitude").value = place_1.geometry['location'].lng();
        });
    }
}
function roleFunction(roles) {
    var is_password = 0;
    var is_driver = 0;
    var is_vendor = 0;
    var is_staff = 0;
    var is_marketing_manager = 0;

    if(($.inArray('1', roles) !== -1) || ($.inArray('5', roles) !== -1)) {
        is_password = 1;
    }
    if($.inArray('3', roles) !== -1) {
        is_driver = 1;
    }
    if($.inArray('4', roles) !== -1) {
        is_vendor = 1;
    }
    if($.inArray('5', roles) !== -1) {
        var is_staff = 1;
    }
    if($.inArray('7', roles) !== -1) {
        var is_marketing_manager = 1;
    }
    // if(is_password) {
    //     $('.password-field').removeClass("hide");
    // }
    // else {
    //     $('.password-field').addClass("hide");
    // }
    if(is_driver) {
        $('.driverInfoSection').removeClass("hide");
        console.log('driver');
        if($('#id').val() == "") {
            $('.driverInfoSection .is_required').attr('required',"required");
        }
    }
    else {
        $('.driverInfoSection').addClass("hide");
        console.log('driver-1');
        $('.driverInfoSection .is_required').removeAttr('required');
    }
    if(is_staff) {
        $('.staff_section').removeClass("hide");
    }
    else {
        $('.staff_section').addClass("hide");
    }
    if(is_vendor) {
        $('.vendorInfoSection').removeClass("hide");
        if($('#id').val() == "") {
            $('.vendorInfoSection .is_required').attr('required',"required");
        }
    }
    else {
        $('.vendorInfoSection').addClass("hide");
        $('.vendorInfoSection .is_required').removeAttr('required');
    }
    if((is_driver) || (is_vendor)) {
        $('.accountDetailSection').removeClass("hide");
    }
    else {
        $('.accountDetailSection').addClass("hide");
    }
    if(is_marketing_manager) {
        $('.marketingManagerInfoSection').removeClass("hide");
        $('.marketingPartnerAccountDetailSection').removeClass("hide");
        $('.marketingPartnerAccountDetailSection .is_required').attr('required',"required");
    }
    else {
        $('.marketingManagerInfoSection').addClass("hide");
        $('.marketingPartnerAccountDetailSection').addClass("hide");
        $('.marketingPartnerAccountDetailSection .is_required').removeAttr('required');
    }
}
    
$(document).ready(function(){

    $(document).on('change', '.role', function(){
        var roles = $(this).val();

        roleFunction(roles);
    });

    if($('.admin_commission_type').val() == ''){
        $('.admin_commission_required').addClass('hide');
    }
    $(document).on('change', '.admin_commission_type', function(){
        if(this.value != '') {
            $('.admin_commission').attr('required',"required");
            $('.admin_commission_required').removeClass('hide');
        }
        else {
            $('.admin_commission').removeAttr('required');
            $('.admin_commission_required').addClass('hide');
        }
    });

    $(document).on('click', '.selectAll', function(){
        $(".permissions > option").prop("selected", true);
        $(".permissions").trigger("change");
    });

    $(document).on('click', '.deselectAll', function(){
        $(".permissions > option").prop("selected", false);
        $(".permissions").trigger("change");
    });

    var roles = $('.role').val();
    roleFunction(roles);

    $('#add-fund-basic-form').validate();
    $('#revoke-fund-basic-form').validate();

});
</script>
@endsection



