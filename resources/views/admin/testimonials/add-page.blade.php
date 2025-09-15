@extends('layout.admin.dashboard')

@section('content')

<div class="page-heading">
        <div class="page-title">
            <div class="row">
                
                <div class="col-12 col-md-12 order-md-2">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{url('/admin')}}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ url('/admin/testimonials'); }}">Testimonials</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Add Testimonial</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="card">
                
                <div class="card-body">
                <form class="form w-100" id="pageForm" action="#">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="basicInput">User Name</label>
                                <input type="text" class="form-control" name="heading" id="heading">
                            </div>
                        </div>
                        <div class="col-md-4"></div>
                        <div class="col-md-4"></div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="basicInput">Designation</label>
                                <input type="text" class="form-control" name="designation" id="designation">
                            </div>
                        </div>
                        <div class="col-md-4"></div>
                        <div class="col-md-4"></div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="basicInput">Banner</label>
                                <input type="file" class="form-control" name="file" id="file">
                            </div>
                        </div>
                        <div class="col-md-4"></div>
                        <div class="col-md-4"></div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="basicInput">Description</label>
                                <textarea class="form-control" rows="10" name="description" id="description"></textarea>
                            </div>
                        </div>
                        
                        
                     
                        <div class="text-right">
                            <!--begin::Submit button-->
                            <button type="button" id="form_submit" class="btn btn-sm btn-primary fw-bolder me-3 my-2">
                                <span class="indicator-label" id="formSubmit">Add</span>
                                <span class="indicator-progress d-none">Please wait...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                            <!--end::Submit button-->
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
<!-- end plugin js -->
<script>
    let saveDataURL = "{{url('/admin/add-testimonial')}}";
    let returnURL = "{{url('/admin/testimonials')}}";
</script>
<script src="{{ asset('public/admin/js/pages/testimonials/add-page.js') }}"></script>
<script>
$(document).ready(function(){
	$('#page_main_title').html('Add Testimonial');
});
</script>

@endsection


