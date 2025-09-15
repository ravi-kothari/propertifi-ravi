@extends('layout.admin.dashboard')

@section('content')

<div class="page-heading">
        <div class="page-title">
            <div class="row">
                
                <div class="col-12 col-md-12 order-md-2">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{url('/admin')}}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ url('/admin/account-managers'); }}">Account Managers</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit Account Manager</li>
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
                                <label for="basicInput">User Roles</label>
                                <select class="form-select" id="role_id" name="role_id">
                                <option value="">Select User Role</option>
                                @foreach($roles as $key => $role)
                                <option @if($rowData->role_id == $role->id) selected @endif value="{{$role->id}}">{{$role->title}}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="basicInput">Full Name</label>
                            <input type="text" class="form-control" name="company_name" value="{{$rowData->company_name}}" id="company_name">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="basicInput">Email ID</label>
                            <input type="text" class="form-control" name="email" value="{{$rowData->email}}" id="email">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="basicInput">Email Varification</label>
                            <select class="form-select" id="email_varification" name="email_varification">
                            <option @if($rowData->email_varification == 2) selected @endif value="2">No</option>
                            <option @if($rowData->email_varification == 1) selected @endif value="1">Yes</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="basicInput">Country Code.</label>
                            <select class="form-select" id="country_code" name="country_code">
                                <option @if($rowData->country_code == 1) selected @endif value="1">US (+1)</option>
                                <option @if($rowData->country_code == 91) selected @endif value="91">IN (+91)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="basicInput">Contact No.</label>
                            <input type="text" class="form-control" maxlength="10" name="contact" value="{{$rowData->mobile}}" id="contact">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="basicInput">Password</label>
                            <input type="password" class="form-control" name="password" id="password">
                        </div>
                    </div>
             
                       
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
                    </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
<!-- end plugin js -->
<script>
    let saveDataURL = "{{url('/admin/edit-account-managers/'.$row_id)}}";
    let returnURL = "{{url('/admin/account-managers')}}";
</script>
<script src="{{ asset('public/admin/js/pages/account-manager/add-page.js') }}"></script>
<script>
$(document).ready(function(){
	$('#page_main_title').html('Edit Account Manager');
});
function getCities(stateID){
	if(stateID > 0){
		$.ajax({
			type: 'POST',
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			url: "{{route('admin.get-cities')}}",
			data: {stateID:stateID},
			success: function(msg){
				$('#city').html(msg);
			},error: function(ts) {
				
			}
		});
	}
}
</script>

@endsection


