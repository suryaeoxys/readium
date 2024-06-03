<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

	<title>{{ config('app.name', 'Readium') }}</title>
	
	<!-- plugins:css -->
	<link rel="stylesheet" href="{{ asset('admin/assets/vendors/feather/feather.css') }}">
	<link rel="stylesheet" href="{{ asset('admin/assets/vendors/mdi/css/materialdesignicons.min.css') }}">
	<link rel="stylesheet" href="{{ asset('admin/assets/vendors/ti-icons/css/themify-icons.css') }}">
	<link rel="stylesheet" href="{{ asset('admin/assets/vendors/css/vendor.bundle.base.css') }}">
	<link rel="stylesheet" href="{{ asset('admin/assets/css/vertical-layout-light/style.css') }}">

	<link rel="stylesheet" href="{{ asset('admin/assets/css/select2.min.css') }}">

	<!-- Custom Css Start -->
	<link rel="stylesheet" href="{{ asset('css/custom.css') }}">
	<!-- Custom Css End -->

	<!-- <link rel="shortcut icon" href="images/favicon.png" /> -->

	@yield('styles')

</head>

<body>
	
	<div class="container-scroller">
		@include('includes.admin.header')
		<div class="container-fluid page-body-wrapper">
			@include('includes.admin.sidebar')
			<div class="main-panel master-pages">
				
				@yield('content')

				{{-- <footer class="footer">
					<div class="d-sm-flex justify-content-center justify-content-sm-between">
					<span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Copyright © 2024. All rights reserved.</span>
					</div>
				</footer> --}}
			</div>
			<!-- main-panel ends -->
		</div>
		<!-- page-body-wrapper ends -->
	</div>
	<!-- container-scroller -->


<!-- plugins:js -->
<script src="{{ asset('admin/assets/vendors/js/vendor.bundle.base.js') }}"></script>
<script src="{{ asset('admin/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('admin/assets/js/off-canvas.js') }}"></script>
<script src="{{ asset('admin/assets/js/hoverable-collapse.js') }}"></script>
<script src="{{ asset('admin/assets/js/template.js') }}"></script>
<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>

<!-- jQuery UI file -->
<!-- <script src="{{ asset('admin/assets/js/jquery-ui.min.js') }}" type="text/javascript"></script> -->

<!-- jQuery validation file -->
<script src="{{ asset('admin/assets/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('admin/assets/js/sweetalert.min.js') }}" type="text/javascript"></script>

<script src="{{ asset('admin/assets/js/select2.full.min.js') }}" type="text/javascript"></script>

<!-- Custom js Start -->
<script src="{{ asset('js/custom.js') }}"></script>
<!-- Custom js End -->

<script src="{{ asset('admin/assets/js/ckeditor/ckeditor.js') }}" type="text/javascript"></script>

@yield('scripts')
<script>
    $(document).ready(function(){
        var url = window.location.href;
        var type = url.split('?')[1]; 
        type = type ? type.split('=')[1] : null; 

        if (type && type.toLowerCase() === 'sub-category') {
            console.log("Setting sub-category as active");
            $("#category").removeClass('active');
            $("#subcategory").addClass('active');
        } else if (type && type.toLowerCase() === 'category') {
            console.log("Setting category as active");
            $("#category").addClass('active');
            $("#subcategory").removeClass('active');
        } else {
            console.log("No type parameter found or invalid value.");
        }
    });
</script>

</body>

</html>