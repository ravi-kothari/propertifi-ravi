@extends('layout.admin.dashboard')

@section('content')

<div class="page-heading">
        <div class="page-title">
            <div class="row">
                
                <div class="col-12 col-md-12 order-md-2">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{url('/admin')}}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ url('/admin/agent-profile'); }}">My Profile</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit Profile</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="card">
                
                <div class="card-body">
                <form class="form w-100" id="updateForm" action="#">
                    <div class="row">
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="basicInput">Company Name</label>
                            <input type="text" class="form-control" name="company_name" value="{{$rowData->company_name}}" id="company_name">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="basicInput">Email ID</label>
                            <input type="text" class="form-control" readonly="readonly" name="email" value="{{$rowData->email}}" id="email">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="basicInput">Contact No.</label>
                            <input type="text" class="form-control" readonly="readonly" name="contact" value="{{$rowData->mobile}}" id="contact">
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                            <div class="form-group">
                                <label for="basicInput">Address</label>
                                <input type="text" class="form-control" value="{{$rowData->address}}" name="address" id="address">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="basicInput">State</label>
                                <select id="state" name="state" onchange="getCities(this.value)" class="form-select">
                                <option value="">Select State</option>
                                @foreach($states as $key => $state)
                                <option @if($rowData->state == $state->id) selected @endif value="{{$state->id}}">{{$state->state}}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="basicInput">City</label>
                                <select id="city" name="city" class="form-select">
                                <option value="">Select City</option>
                                @foreach($cities as $key => $city)
                                <option @if($rowData->city == $city->id) selected @endif value="{{$city->id}}">{{$city->city}}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="basicInput">Zipcode</label>
                                <input type="text" class="form-control" maxlength="5" value="{{$rowData->zipcode}}" name="zipcode" id="zipcode">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="basicInput">Logo</label>
                                <input type="file" class="form-control" name="file" id="file">
                            </div>
                        </div>
                        @if($rowData->photo != "")
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label for="basicInput">&nbsp;</label>
                                    <img src="{{URL::asset('public/img/users/')}}/{!! $rowData->photo !!}" width="100">
                                </div>
                            </div>
                        @endif
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="basicInput">Portfolio Type</label>
                                <select id="portfolio_type" name="portfolio_type" class="form-select">
                                <option value="">Select Portfolio Type</option>
                                <option @if($rowData->portfolio_type == 'Rentals') selected @endif value="Rentals">Rentals</option>
                                <option @if($rowData->portfolio_type == 'Association') selected @endif value="Association">Association</option>
                                <option @if($rowData->portfolio_type == 'Commercial') selected @endif value="Commercial">Commercial</option>
                                <option @if($rowData->portfolio_type == 'Mixed') selected @endif value="Mixed">Mixed</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="basicInput">Units under Management</label>
                                <input type="text" class="form-control" value="{{$rowData->units}}" name="units" id="units">
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="basicInput">About</label>
                                <textarea class="form-control" rows="10" name="about" id="about">{{$rowData->about}}</textarea>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="basicInput">Point of Contact Name</label>
                                <input type="text" class="form-control" value="{{$rowData->p_contact_name}}" name="p_contact_name" id="p_contact_name">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="basicInput">Point of Contact Email.</label>
                                <input type="text" class="form-control" value="{{$rowData->p_contact_email}}" name="p_contact_email" id="p_contact_email">
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="basicInput">Point of Contact No.</label>
                                <input type="text" class="form-control" maxlength="10" value="{{$rowData->p_contact_no}}" name="p_contact_no" id="p_contact_no">
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="basicInput">Single Family Property Price Range</label>
                                <input type="text" class="form-control" value="{{$rowData->single_family}}" name="single_family" id="single_family">
                            </div>
                        </div>
                         <div class="col-md-3">
                            <div class="form-group">
                                <label for="basicInput">Multi Family Property Price Range</label>
                                <input type="text" class="form-control" value="{{$rowData->multi_family}}" name="multi_family" id="multi_family">
                            </div>
                        </div>
                         <div class="col-md-3">
                            <div class="form-group">
                                <label for="basicInput">Association Property Price Range</label>
                                <input type="text" class="form-control" value="{{$rowData->association_property}}" name="association_property" id="association_property">
                            </div>
                        </div>
                         <div class="col-md-3">
                            <div class="form-group">
                                <label for="basicInput">Commercial Property Price Range</label>
                                <input type="text" class="form-control" value="{{$rowData->commercial_property}}" name="commercial_property" id="commercial_property">
                            </div>
                        </div>
                        
                    
                       
                       
                        <div class="text-left">
                            <!--begin::Submit button-->
                            <button type="button" id="profile_submit" class="btn btn-sm btn-primary fw-bolder me-3 my-2">
                                <span class="indicator-label" id="formSubmit">Submit</span>
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
$(document).ready(function(){
	$('#page_main_title').html('Edit Profile');
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
<script>
    let saveDataURL = "{{url('/admin/save-agent-profile')}}";
	let returnURL = "{{url('/admin/agent-profile')}}";
</script>
<script src="{{ asset('public/admin/js/pages/update-agent-profile.js') }}"></script>

@endsection


