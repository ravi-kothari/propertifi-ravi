@extends('layout.admin.dashboard')

@section('content')

<div class="page-heading">
        <div class="page-title">
            <div class="row">
                
                <div class="col-12 col-md-12 order-md-2">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{url('/admin')}}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ url('/admin/questions'); }}">Questions</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit Question</li>
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
                                <label for="basicInput">Property Type</label>
                                <select @if($permission == 'view') disabled="disabled" @endif class="form-select" name="category" id="category">
                                <option value="">Select</option>
                                <option @if($rowData->category == 1) selected @endif value="1">Single Family</option>
                                <option @if($rowData->category == 2) selected @endif value="2">Multi Family</option>
                                <option @if($rowData->category == 3) selected @endif value="3">Association</option>
                                <option @if($rowData->category == 8) selected @endif value="8">Commercial</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4"></div>
                        <div class="col-md-4"></div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="basicInput">Question</label>
                            <input type="text" class="form-control" @if($permission == 'view') disabled="disabled" @endif name="question" value="{{$rowData->question}}" id="question">
                        </div>
                    </div>
                    <div class="col-md-4"></div>
                    <div class="col-md-4"></div>
                    
                    <div class="col-md-4">
                            <div class="form-group">
                                <label for="basicInput">Type</label>
                                <select class="form-select" @if($permission == 'view') disabled="disabled" @endif name="type" id="type" onchange="selectType(this.value);">
                                <option @if($rowData->type == 'single') selected @endif value="single">Single Select</option>
                                <option @if($rowData->type == 'multi') selected @endif value="multi">Multi Select</option>
                                <option @if($rowData->type == 'fill_blank') selected @endif value="fill_blank">Fill In The Blank</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4"></div>
                        <div class="col-md-4"></div>
                        @if($rowData->type == 'single' || $rowData->type == 'multi')
                        <div class="row" id="option_div">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="basicInput">Options 1</label>
                                <input type="text" class="form-control" @if($permission == 'view') disabled="disabled" @endif value="{{$rowData->option_1}}" name="option_1" id="option_1">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="basicInput">Options 2</label>
                                <input type="text" class="form-control" @if($permission == 'view') disabled="disabled" @endif value="{{$rowData->option_2}}" name="option_2" id="option_2">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="basicInput">Options 3</label>
                                <input type="text" class="form-control" @if($permission == 'view') disabled="disabled" @endif value="{{$rowData->option_3}}" name="option_3" id="option_3">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="basicInput">Options 4</label>
                                <input type="text" class="form-control" @if($permission == 'view') disabled="disabled" @endif value="{{$rowData->option_4}}" name="option_4" id="option_4">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="basicInput">Options 5</label>
                                <input type="text" class="form-control" @if($permission == 'view') disabled="disabled" @endif value="{{$rowData->option_5}}" name="option_5" id="option_5">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="basicInput">Options 6</label>
                                <input type="text" class="form-control" @if($permission == 'view') disabled="disabled" @endif value="{{$rowData->option_6}}" name="option_6" id="option_6">
                            </div>
                        </div>
                        </div>
                        @else
                        <div class="row" style="display:none" id="option_div">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="basicInput">Options 1</label>
                                <input type="text" class="form-control" @if($permission == 'view') disabled="disabled" @endif name="option_1" id="option_1">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="basicInput">Options 2</label>
                                <input type="text" class="form-control" @if($permission == 'view') disabled="disabled" @endif name="option_2" id="option_2">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="basicInput">Options 3</label>
                                <input type="text" class="form-control" @if($permission == 'view') disabled="disabled" @endif name="option_3" id="option_3">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="basicInput">Options 4</label>
                                <input type="text" class="form-control" @if($permission == 'view') disabled="disabled" @endif name="option_4" id="option_4">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="basicInput">Options 5</label>
                                <input type="text" class="form-control" @if($permission == 'view') disabled="disabled" @endif name="option_5" id="option_5">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="basicInput">Options 6</label>
                                <input type="text" class="form-control" @if($permission == 'view') disabled="disabled" @endif name="option_6" id="option_6">
                            </div>
                        </div>
                        </div>
                        @endif
                        
                       <div class="col-md-12">
                        <div class="form-group">
                            <label for="basicInput">Heading</label>
                            <input type="text" class="form-control" @if($permission == 'view') disabled="disabled" @endif name="content_heading" value="{{$rowData->content_heading}}" id="content_heading">
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                    
                    <div class="form-group">
                    <label for="basicInput">Description</label>
                    @if($permission == 'edit')
                    <textarea class="form-control editorBox" rows="10" name="content_description" id="content_description">{{$rowData->content_description}}</textarea>
                    @else
                    <div>{{$rowData->content_description}}</div>
                    @endif
                    </div>
                    
                    </div>
                        
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="basicInput">Status</label>
                                <select class="form-select" id="status" @if($permission == 'view') disabled="disabled" @endif name="status">
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
    let saveDataURL = "{{url('/admin/edit-question/'.$row_id)}}";
    let returnURL = "{{url('/admin/questions')}}";
</script>
<script src="{{ asset('public/admin/js/pages/questions/add-page.js') }}"></script>
<script>
$(document).ready(function(){
	$('#page_main_title').html('Edit Question');
});
function selectType(type){
	$('#option_div').show();
	if(type == 'fill_blank'){
		$('#option_div').hide();
	}
}
</script>

@endsection


