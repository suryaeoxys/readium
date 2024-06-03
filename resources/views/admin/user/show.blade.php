@extends('layouts.master') 
@section('content')

<div class="content-wrapper">
    <!-- Content -->

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header border-bottom">
                       User Details
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <div class="row">

                                <h5 class="fw-bolder">Basic Information</h5>

                                <div class="col-md-4 ">
                                    <div class="p-3 listViewclr">
                                        <h6 class="fw-bolder">First Name</h6>
                                        <p class="mb-0">{{ !empty($data->first_name) ? ucfirst($data->first_name) : '-'}}</p>
                                    </div>
                                </div>

                                <div class="col-md-4 ">
                                    <div class="p-3 listViewclr">
                                        <h6 class="fw-bolder">Last Name</h6>
                                        <p class="mb-0">{{ !empty($data->last_name) ? ucfirst($data->last_name) : '-'}}</p>
                                    </div>
                                </div>
                                <div class="col-md-4 ">
                                    <div class="p-3 listViewclr">
                                        <h6 class="fw-bolder">Nickname</h6>
                                        <p class="mb-0">{{ !empty($data->nickname) ? ucfirst($data->nickname) : '-'}}</p>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="p-3 listViewclr">
                                        <h6 class="fw-bolder">Phone</h6>
                                        <p class="mb-0">{{ $data->phone ?? '-'}}</p>
                                    </div>
                                </div>

                                

                                <div class="col-md-4">
                                    <div class="p-3 listViewclr">
                                        <h6 class="fw-bolder">Email</h6>
                                        <p class="mb-0">{{ $data->email ?? '-'}}</p>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="p-3 listViewclr">
                                        <h6 class="fw-bolder">Profile Image</h6>

                                        @if(!empty($data->profile_image))
                                            <div class="even mt-3">
                                                <div class="parc">
                                                    <span class="pip" data-title="{{$data->profile_image}}">
                                                        <img src="{{ url(config('app.profile_image')).'/'.$data->profile_image ?? '' }}" alt="" width="150" height="100">
                                                    </span>
                                                </div>
                                            </div>
                                        @else
                                            <p class="mb-0"> No Image Found </p>
                                        @endif
                                    </div>
                                </div>
                               
                                {{-- <div class="col-md-4">
                                    <div class="p-3 listViewclr">  
                                        <h6 class="fw-bolder">Status</h6>
                                        <p class="mb-0">{{ isset($data) && ($data->status == 1) ? 'Active' : 'In-Active'}}</p>
                                    </div>
                                </div> --}}
                            </div> 
                            <a class="btn btn-danger btn_back" href="{{ url()->previous() }}">
                                {{ 'Back to list' }}
                            </a>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
