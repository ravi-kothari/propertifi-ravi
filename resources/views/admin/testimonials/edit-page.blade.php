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
                            <li class="breadcrumb-item active" aria-current="page">Edit Testimonial</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="card">
                
                <div class="card-body">
                <form class="form w-100" id="pageForm" action="#">
                <input type="hidden" name="old_file" value="{{$rowData->banner}}" id="old_file">
                    <div class="row">
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="basicInput">User Name</label>
                            <input type="text" class="form-control" @if($permission == 'view') disabled="disabled" @endif name="heading" value="{{$rowData->heading}}" id="heading">
                        </div>
                    </div>
                    <div class="col-md-4"></div>
                    <div class="col-md-4"></div>
                    
                    <div class="col-md-4">
                            <div class="form-group">
                                <label for="basicInput">Designation</label>
                                <input type="text" class="form-control" @if($permission == 'view') disabled="disabled" @endif value="{{$rowData->designation}}" name="designation" id="designation">
                            </div>
                        </div>
                        <div class="col-md-4"></div>
                        <div class="col-md-4"></div>
                    
                    
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="basicInput">Banner</label>
                                <input type="file" class="form-control" @if($permission == 'view') disabled="disabled" @endif name="file" id="file">
                            </div>
                        </div>
                        @if($rowData->banner != "")
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label for="basicInput">&nbsp;</label>
                                    <img src="{{URL::asset('public/img/testimonials/')}}/{!! $rowData->banner !!}" width="100">
                                </div>
                            </div>
                        @endif
                        <div class="col-md-4"></div>
                        
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="basicInput">Description</label>
                                <textarea class="form-control" rows="10" @if($permission == 'view') disabled="disabled" @endif name="description" id="description">{{$rowData->description}}</textarea>
                            </div>
                        </div>
                        
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="basicInput">Status</label>
                                <select class="form-select" id="status" @if($permission == 'view') disabled="disabled" @endif name="status">
                                <option @if($rowData->status == 1) selected @endif value="1">Active</option>
                                <option @if($rowData->status == 2) selected @endif value="2">Inactive</option>
                                </select>
                            </div>
                        </div>
                        
                       @if($permission == 'edit')
                        <div class="text-left">
                            <!--begin::Submit button-->
                            <button type="button" id="form_submit" class="btn btn-sm btn-primary fw-bolder me-3 my-2">
                                <span class="indicator-label" id="formSubmit">Update</span>
                                <span class="indicator-progress d-none">Please wait...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                            <!--end::Submit button-->
                        </div>
                        @endif
                    </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
<!-- end plugin js -->
<script>
    let saveDataURL = "{{url('/admin/edit-testimonial/'.$row_id)}}";
    let returnURL = "{{url('/admin/testimonials')}}";
</script>
<script src="{{ asset('public/admin/js/pages/testimonials/add-page.js') }}"></script>
<script>
$(document).ready(function(){
	$('#page_main_title').html('Edit Testimonial');
});
</script>

@endsection


