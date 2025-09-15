@extends('layout.admin.dashboard')

@section('content')

<div class="page-heading">
        <div class="page-title">
            <div class="row">
                
                <div class="col-12 col-md-12 order-md-2">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{url('/admin')}}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ url('/admin/inner-pages'); }}">Inner Pages</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit Inner Page</li>
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
                            <label for="basicInput">Page Name</label>
                            <input type="text" class="form-control" name="page_name" @if($permission == 'view') disabled="disabled" @endif value="{{$rowData->page_name}}" id="title">
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="basicInput">Heading</label>
                            <input type="text" class="form-control" name="heading" @if($permission == 'view') disabled="disabled" @endif value="{{$rowData->heading}}" id="title">
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                    
                    <div class="form-group">
                    <label for="basicInput">Short Description</label>
                    @if($permission == 'edit')
                    <textarea class="form-control editorBox" name="short_description" id="short_description">{{$rowData->short_description}}</textarea>
                    @else
                    <div>{{$rowData->short_description}}</div>
                    @endif
                    </div>
                    
                    </div>
                    
                    <div class="col-md-12">
                    
                    <div class="form-group">
                    <label for="basicInput">Description</label>
                    @if($permission == 'edit')
                    <textarea class="form-control editorBox" rows="10" name="description" id="description">{{$rowData->description}}</textarea>
                    @else
                    <div>{{$rowData->description}}</div>
                    @endif
                    </div>
                    
                    </div>
                        
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="basicInput">SEO Title</label>
                                <input type="text" class="form-control" @if($permission == 'view') disabled="disabled" @endif name="seo_title" value="{{$rowData->seo_title}}" id="seo_title">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="basicInput">SEO Description</label>
                                <textarea class="form-control" rows="5" @if($permission == 'view') disabled="disabled" @endif name="seo_keywords" id="seo_keywords">{{$rowData->seo_keywords}}</textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="basicInput">SEO Keywords</label>
                                <textarea class="form-control" rows="5" @if($permission == 'view') disabled="disabled" @endif name="seo_description" id="seo_description">{{$rowData->seo_description}}</textarea>
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
    let saveDataURL = "{{url('/admin/edit-inner-page/'.$row_id)}}";
    let returnURL = "{{url('/admin/inner-pages')}}";
</script>
<script src="{{ asset('public/admin/js/pages/inner_pages/add-page.js') }}"></script>
<script>
$(document).ready(function(){
	$('#page_main_title').html('Edit Inner Page');
});
</script>

@endsection


