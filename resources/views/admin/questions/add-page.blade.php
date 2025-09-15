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
                            <li class="breadcrumb-item active" aria-current="page">Add Question</li>
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
                                <label for="basicInput">Property Type</label>
                                <select class="form-select" name="category" id="category">
                                <option value="">Select</option>
                                <option value="1">Single Family</option>
                                <option value="2">Multi Family</option>
                                <option value="3">Association</option>
                                <option value="8">Commercial</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4"></div>
                        <div class="col-md-4"></div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="basicInput">Question</label>
                                <input type="text" class="form-control" name="question" id="question">
                            </div>
                        </div>
                        <div class="col-md-4"></div>
                        <div class="col-md-4"></div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="basicInput">Type</label>
                                <select class="form-select" name="type" id="type" onchange="selectType(this.value);">
                                <option value="single">Single Select</option>
                                <option value="multi">Multi Select</option>
                                <option value="fill_blank">Fill In The Blank</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4"></div>
                        <div class="col-md-4"></div>
                        <div class="row" id="option_div">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="basicInput">Options 1</label>
                                <input type="text" class="form-control" name="option_1" id="option_1">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="basicInput">Options 2</label>
                                <input type="text" class="form-control" name="option_2" id="option_2">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="basicInput">Options 3</label>
                                <input type="text" class="form-control" name="option_3" id="option_3">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="basicInput">Options 4</label>
                                <input type="text" class="form-control" name="option_4" id="option_4">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="basicInput">Options 5</label>
                                <input type="text" class="form-control" name="option_5" id="option_5">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="basicInput">Options 6</label>
                                <input type="text" class="form-control" name="option_6" id="option_6">
                            </div>
                        </div>
                        </div>
                        
                        <div class="col-md-12">
                        <div class="form-group">
                            <label for="basicInput">Heading</label>
                            <input type="text" class="form-control"  name="content_heading" id="content_heading">
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                    
                    <div class="form-group">
                    <label for="basicInput">Description</label>

                    <textarea class="form-control editorBox" rows="10" name="content_description" id="content_description"></textarea>
                    
                    </div>
                    
                    </div>
                      
                        
                        <div class="col-md-4">
                            <!--begin::Submit button-->
                            <button type="button" id="form_submit" class="btn btn-sm btn-primary fw-bolder me-3 my-2">
                                <span class="indicator-label" id="formSubmit">Add</span>
                                <span class="indicator-progress d-none">Please wait...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                            <!--end::Submit button-->
                        </div>
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
    let saveDataURL = "{{url('/admin/add-question')}}";
    let returnURL = "{{url('/admin/questions')}}";
</script>
<script src="{{ asset('public/admin/js/pages/questions/add-page.js') }}"></script>
<script>
$(document).ready(function(){
	$('#page_main_title').html('Add Question');
});
function selectType(type){
	$('#option_div').show();
	if(type == 'fill_blank'){
		$('#option_div').hide();
	}
}
</script>

@endsection


