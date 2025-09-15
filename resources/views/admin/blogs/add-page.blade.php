@extends('layout.admin.dashboard')

@section('content')

<div class="page-heading">
        <div class="page-title">
            <div class="row">
                
                <div class="col-12 col-md-12 order-md-2">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{url('/admin')}}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ url('/admin/blogs'); }}">Blogs</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Add Blog</li>
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
                    	<div class="col-md-2">
                        <div class="form-group">
                            <label for="basicInput">Blog Category</label>
                            <select class="form-select" id="blog_category_id" name="blog_category_id">
                            <option value="">Select</option>
                            @foreach($blogCategories as $key => $blogCategory)
                            <option value="{{$blogCategory->id}}">{{$blogCategory->title}}</option>
                            @endforeach
                            </select>
                        </div>
                    </div>
                        <div class="col-md-10">
                            <div class="form-group">
                                <label for="basicInput">Heading</label>
                                <input type="text" class="form-control" name="heading" id="heading">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="basicInput">Banner (Jpg and Webp Only)</label>
                                <input type="file" class="form-control" name="file" id="file">
                            </div>
                        </div>
                        <div class="col-md-4"></div>
                        <div class="col-md-4"></div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="basicInput">Description</label>
                                <textarea class="form-control editorBox" rows="10" name="description" id="description"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="basicInput">SEO Title</label>
                                <input type="text" class="form-control" name="seo_title" id="seo_title">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="basicInput">SEO Description</label>
                                <textarea class="form-control" rows="5" name="seo_keywords" id="seo_keywords"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="basicInput">SEO Keywords</label>
                                <textarea class="form-control" rows="5" name="seo_description" id="seo_description"></textarea>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="basicInput">Post Date</label>
                                <input type="date" class="form-control" name="post_date" id="post_date">
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="basicInput">Featured</label>
                                <select class="form-select" id="featured" name="featured">
                                <option value="1">Yes</option>
                                <option value="2">No</option>
                                </select>
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
    let saveDataURL = "{{url('/admin/add-blog')}}";
    let returnURL = "{{url('/admin/blogs')}}";
</script>
<script src="{{ asset('public/admin/js/pages/blogs/add-page.js') }}"></script>
<script>
$(document).ready(function(){
	$('#page_main_title').html('Add Blog');
});
</script>

@endsection


