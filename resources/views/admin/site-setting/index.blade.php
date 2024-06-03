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
                        Site Setting
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.site-setting.store') }}" method="POST" enctype="multipart/form-data" id="basic-form">
                            @csrf

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <div class="row">
                                        <div class="col-md-7">
                                            <label for="name" class="mt-2"> Logo 1 <span class="text-danger info">(Only jpeg, png, jpg files allowed)</span></label>
                                            <input type="file" name="logo_1" class="form-control @error('logo_1') is-invalid @enderror" accept="image/jpeg,image/png">
                                            <input type="hidden" class="form-control" name="logo_1_old" value="{{ isset($data) && isset($data['logo_1']) ? $data['logo_1'] : ''}}">
                                            @error('logo_1')
                                                <span class="invalid-feedback form-invalid fw-bold" role="alert">
                                                    {{ $message }}
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-md-5 mt-auto">
                                        @if(!empty($data['logo_1']))
                                            <div class="mt-3">
                                                <span class="pip" data-title="{{ $data['logo_1'] }}">
                                                    <img src="{{ url(config('app.logo')).'/'.$data['logo_1'] ?? '' }}" alt="" width="150" height="100px">
                                                </span>
                                            </div>
                                        @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    <div class="row">
                                        <div class="col-md-7">
                                            <label for="name" class="mt-2"> Logo 2 <span class="text-danger info">(Only jpeg, png, jpg files allowed)</span></label>
                                            <input type="file" name="logo_2" class="form-control @error('logo_2') is-invalid @enderror" accept="image/jpeg,image/png">
                                            <input type="hidden" class="form-control" name="logo_2_old" value="{{ isset($data) && isset($data['logo_2']) ? $data['logo_2'] : ''}}">
                                            @error('logo_2')
                                                <span class="invalid-feedback form-invalid fw-bold" role="alert">
                                                    {{ $message }}
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-md-5 mt-auto">
                                        @if(!empty($data['logo_2']))
                                            <div class="mt-3">
                                                <span class="pip" data-title="{{ $data['logo_2'] }}">
                                                    <img src="{{ url(config('app.logo')).'/'.$data['logo_2'] ?? '' }}" alt="" width="150" height="100px">
                                                </span>
                                            </div>
                                        @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="name" class="mt-2">Admin Email <span class="text-danger">*</span></label>
                                    <input type="email" name="admin_mail" class="form-control @error('admin_mail') is-invalid @enderror" placeholder="Admin Email" value="{{ old('admin_mail', isset($data) && isset($data['admin_mail']) ? $data['admin_mail'] : '') }}" required>
                                    @error('admin_mail')
                                        <span class="invalid-feedback form-invalid fw-bold" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <!-- <div class="row">
                                <h5 class="fw-bolder">{{ 'Customer Support Details' }}</h5>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="name" class="mt-2">Mobile Number <span class="text-danger">*</span></label>
                                        <input type="number" name="mobile_number" class="form-control mobile_number @error('mobile_number') is-invalid @enderror" placeholder="Mobile Number" value="{{ old('mobile_number', isset($data) && isset($data['mobile_number']) ? $data['mobile_number'] : '' ) }}" min="0" minlength="10" maxlength="10" required>
                                        @error('mobile_number')
                                            <span class="invalid-feedback form-invalid fw-bold" role="alert">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="name" class="mt-2">Landline Number </label>
                                        <input type="number" name="landline_number" class="form-control landline_number @error('landline_number') is-invalid @enderror" placeholder="Landline Number" value="{{ old('landline_number', isset($data) && isset($data['landline_number']) ? $data['landline_number'] : '' ) }}" min="0">
                                        @error('landline_number')
                                            <span class="invalid-feedback form-invalid fw-bold" role="alert">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="name" class="mt-2">Support Email <span class="text-danger">*</span></label>
                                        <input type="email" name="support_email" class="form-control support_email @error('support_email') is-invalid @enderror" placeholder="Support Email" value="{{ old('support_email', isset($data) && isset($data['support_email']) ? $data['support_email'] : '' ) }}" required>
                                        @error('support_email')
                                            <span class="invalid-feedback form-invalid fw-bold" role="alert">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="name" class="mt-2">Whatsapp Number </label>
                                        <input type="number" name="whatsapp_number" class="form-control whatsapp_number @error('whatsapp_number') is-invalid @enderror" placeholder="WhatsApp Number" value="{{ old('whatsapp_number', isset($data) && isset($data['whatsapp_number']) ? $data['whatsapp_number'] : '' ) }}" min="0" minlength="10" maxlength="10">
                                        @error('whatsapp_number')
                                            <span class="invalid-feedback form-invalid fw-bold" role="alert">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div> -->

                            <div class="mt-3">
                                <input class="btn btn-primary" type="submit" value="Save">
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
    $(document).ready(function(){
        if($('.marketing_partner_commission_type').val() == ''){
            $('.marketing_partner_commission_required').addClass('hide');
        }
        $(document).on('change', '.marketing_partner_commission_type', function(){
            if(this.value != '') {
                $('.marketing_partner_commission').attr('required',"required");
                $('.marketing_partner_commission_required').removeClass('hide');
            }
            else {
                $('.marketing_partner_commission').removeAttr('required');
                $('.marketing_partner_commission_required').addClass('hide');
            }
        });
    });
</script>
@endsection