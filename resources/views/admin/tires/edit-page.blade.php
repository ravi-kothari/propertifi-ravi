@extends('layout.admin.dashboard')

@section('content')

<div class="page-heading">
        <div class="page-title">
            <div class="row">
                
                <div class="col-12 col-md-12 order-md-2">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{url('/admin')}}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ url('/admin/tiers'); }}">Tiers</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit Tier</li>
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
                            <label for="basicInput">Title</label>
                            <input type="text" class="form-control" @if($permission == 'view') disabled="disabled" @endif name="title" value="{{$rowData->title}}" id="title">
                        </div>
                    </div>
                    <div class="col-md-4"></div>
                    <div class="col-md-4"></div>
                    
                    <div class="col-md-4">
                            <div class="form-group">
                                <label for="basicInput">Price</label>
                                <input type="text" class="form-control" @if($permission == 'view') disabled="disabled" @endif value="{{$rowData->price}}" name="price" id="price">
                            </div>
                        </div>
                        <div class="col-md-4"></div>
                        <div class="col-md-4"></div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="basicInput">Timings(In Minutes)</label>
                                <input type="text" class="form-control" @if($permission == 'view') disabled="disabled" @endif value="{{$rowData->timings}}" name="timings" id="timings">
                            </div>
                        </div>
                        <div class="col-md-4"></div>
                        <div class="col-md-4"></div>
                        
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="basicInput">Status</label>
                                <select class="form-select" @if($permission == 'view') disabled="disabled" @endif id="status" name="status">
                                <option @if($rowData->status == 1) selected @endif value="1">Active</option>
                                <option @if($rowData->status == 2) selected @endif value="2">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4"></div>
                        <div class="col-md-4"></div>
                        
                        @if($permission == 'edit')
                        <div class="col-md-4">
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
                        <div class="col-md-4"></div>
                        <div class="col-md-4"></div>
                        
                       
                        
                    </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
<!-- end plugin js -->
<script>
    let saveDataURL = "{{url('/admin/edit-tier/'.$row_id)}}";
    let returnURL = "{{url('/admin/tiers')}}";
</script>
<script src="{{ asset('public/admin/js/pages/tiers/add-page.js') }}"></script>
<script>
$(document).ready(function(){
	$('#page_main_title').html('Edit Tier');
});
</script>

@endsection


