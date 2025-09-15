@extends('layout.admin.dashboard')

@section('content')

<div class="page-heading">
        <div class="page-title">
            <div class="row">
                
                <div class="col-12 col-md-12 order-md-2">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{url('/admin')}}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ url('/admin/blog-categories'); }}">Blog Categories</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit Blog Category</li>
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
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="basicInput">Title</label>
                            <input type="text" class="form-control" @if($permission == 'view') disabled="disabled" @endif name="title" value="{{$rowData->title}}" id="title">
                        </div>
                    </div>
                    
                    
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="basicInput">Status</label>
                                <select @if($permission == 'view') disabled="disabled" @endif  class="form-select" id="status" name="status">
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
    let saveDataURL = "{{url('/admin/edit-blog-category/'.$row_id)}}";
    let returnURL = "{{url('/admin/edit-blog-category/'.$row_id)}}";
</script>
<script src="{{ asset('public/admin/js/pages/blog_categories/add-page.js') }}"></script>
<script>
$(document).ready(function(){
	$('#page_main_title').html('Edit Blog Category');
});
</script>

@endsection


