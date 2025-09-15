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
                            <li class="breadcrumb-item active" aria-current="page">Edit Blog</li>
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
                    
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="basicInput">Blog Category</label>
                            <select @if($permission == 'view') disabled="disabled" @endif class="form-select" id="blog_category_id" name="blog_category_id">
                            <option value="">Select</option>
                                @foreach($blogCategories as $key => $blogCategory)
                                <option @if($blogCategory->id == $rowData->blog_category_id) selected @endif value="{{$blogCategory->id}}">{{$blogCategory->title}}</option>
                                @endforeach
                                </select>
                        </div>
                    </div>
                    
                    <div class="col-md-10">
                        <div class="form-group">
                            <label for="basicInput">Heading</label>
                            <input type="text" class="form-control" @if($permission == 'view') disabled="disabled" @endif name="heading" value="{{$rowData->heading}}" id="heading">
                        </div>
                    </div>
                    
                    
                    
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="basicInput">Banner (Jpg and Webp Only)</label>
                                <input type="file" class="form-control" @if($permission == 'view') disabled="disabled" @endif name="file" id="file">
                            </div>
                        </div>
                        @if($rowData->banner != "")
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label for="basicInput">&nbsp;</label>
                                    <img src="{{URL::asset('public/img/blogs/')}}/{!! $rowData->banner !!}" width="100">
                                </div>
                            </div>
                        @endif
                        <div class="col-md-4"></div>
                        
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="basicInput">Description</label>
                                @if($permission == 'edit')
                                <textarea class="form-control editorBox" rows="10"  name="description" id="description">{{$rowData->description}}</textarea>
                                @else
                                <div>{!! $rowData->description !!}</div>
                                @endif
                            </div>
                        </div>
                        <hr />
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="basicInput">Canonical Tag</label>
                                <input type="text" class="form-control" name="canonical_tag" @if($permission == 'view') disabled="disabled" @endif value="{{$rowData->canonical_tag}}" id="canonical_tag">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="basicInput">Schema Tag</label>
                                <textarea class="form-control" rows="5" name="schema_tag" @if($permission == 'view') disabled="disabled" @endif id="schema_tag">{{$rowData->schema_tag}}</textarea>
                            </div>
                        </div>
                        <hr />
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="basicInput">SEO Title</label>
                                <input type="text" class="form-control" name="seo_title" @if($permission == 'view') disabled="disabled" @endif value="{{$rowData->seo_title}}" id="seo_title">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="basicInput">SEO Description</label>
                                <textarea class="form-control" rows="5" name="seo_keywords" @if($permission == 'view') disabled="disabled" @endif id="seo_keywords">{{$rowData->seo_keywords}}</textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="basicInput">SEO Keywords</label>
                                <textarea class="form-control" rows="5" name="seo_description" @if($permission == 'view') disabled="disabled" @endif id="seo_description">{{$rowData->seo_description}}</textarea>
                            </div>
                        </div>
                        
                         <div class="col-md-3">
                            <div class="form-group">
                                <label for="basicInput">Post Date</label>
                                <input type="date" class="form-control" name="post_date" @if($permission == 'view') disabled="disabled" @endif value="{{$rowData->post_date}}" id="post_date">
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="basicInput">Featured</label>
                                <select @if($permission == 'view') disabled="disabled" @endif class="form-select" id="featured" name="featured">
                                <option @if($rowData->featured == 1) selected @endif value="1">Yes</option>
                                <option @if($rowData->featured == 2) selected @endif value="2">No</option>
                                </select>
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
    let saveDataURL = "{{url('/admin/edit-blog/'.$row_id)}}";
    let returnURL = "{{url('/admin/edit-blog/'.$row_id)}}";
</script>
<script src="{{ asset('public/admin/js/pages/blogs/add-page.js') }}"></script>
<script>
$(document).ready(function(){
	$('#page_main_title').html('Edit Blog');
});
</script>

@endsection


