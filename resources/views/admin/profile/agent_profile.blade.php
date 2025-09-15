@extends('layout.admin.dashboard')

@section('content')

<div class="page-heading">
        
        <section class="section">
        <div class="card">
        
        <div class="card-body">
        <div class="row">
        <div class="col-md-1">
        @if($rowData->photo != "")
        <img src="{{URL::asset('public/img/users/')}}/{!! $rowData->photo !!}"  width="100">
        @endif
        </div>
        <div class="col-md-9">
        <h4>{{$rowData->company_name}}</h4>
        {{$rowData->email}}<br />
        +{{$rowData->country_code}}{{$rowData->mobile}}
        </div>
        <div class="col-md-1"></div>
        <div class="col-md-1 pt-2">
        <a style="color:#1C6BE4;font-weight:bold;font-size:20px;" href="{{ url('/admin/edit-agent-profile') }}"><i class="bi bi-pencil"></i> &nbsp;Edit</a>
        </div>
        </div>
        </div>
        
        <hr />
        @php
        $state = Helper::GetStateData($rowData->state);
    	$city = Helper::GetCityData($rowData->city);
        @endphp
        <div class="card-body">
        <div class="row">
        <div class="col-md-3">
        <h5>Company Address</h5>
        <h6 style="color:#999"><strong>Address</strong></h6>
        <span>{{$rowData->address}}</span>
        
        </div>
        <div class="col-md-2">
        
        <h5 style="color:#fff">Company Address</h5>
        <h6 style="color:#999"><strong>State</strong></h6>
        <span>{{$state}}</span>
        
        </div>
        <div class="col-md-2">
        
        <h5 style="color:#fff">Company Address</h5>
        <h6 style="color:#999"><strong>City</strong></h6>
        <span>{{$city}}</span>
        
        </div>
        
        <div class="col-md-2">
        
        <h5 style="color:#fff">Company Address</h5>
        <h6 style="color:#999"><strong>Zipcode</strong></h6>
        <span>{{$rowData->zipcode}}</span>
        
        </div>
        <div class="col-md-1"></div>
        
        
        
        </div>
        </div>
        
        
        <hr />
        
        <div class="card-body">
        <div class="row">
        <div class="col-md-3">
        <h5>Communication Preference</h5>
        <h6 style="color:#999"><strong>Name</strong></h6>
        <span>{{$rowData->p_contact_name}}</span>
        
        </div>
        <div class="col-md-2">
        
        <h5 style="color:#fff">Company Address</h5>
        <h6 style="color:#999"><strong>Email ID</strong></h6>
        <span>{{$rowData->p_contact_email}}</span>
        
        </div>
        <div class="col-md-2">
        
        <h5 style="color:#fff">Company Address</h5>
        <h6 style="color:#999"><strong>Test / SMS</strong></h6>
        <span>{{$rowData->p_contact_no}}</span>
        
        </div>
        <div class="col-md-2">
        
        
        
        </div>
        <div class="col-md-5"></div>
        
        
        
        </div>
        </div>
        
        <hr />
        
        <div class="card-body">
        <div class="row">
        <div class="col-md-3">
        <h5>Others</h5>
        <h6 style="color:#999"><strong>Portfolio Type</strong></h6>
        <span>{{$rowData->portfolio_type != "" ? $rowData->portfolio_type : '--'}}</span>
        </div>
        <div class="col-md-3">
        <h5 style="color:#fff">Units under Management</h5>
        <h6 style="color:#999"><strong>Units under Management</strong></h6>
        <span>{{$rowData->units != "" ? $rowData->units : '--'}}</span>
        </div>
        
        </div>
        </div>
        
         <hr />
        
        <div class="card-body">
        <div class="row">
        <div class="col-md-3">
        <h5>Price Range</h5>
        <h6 style="color:#999"><strong>Single Family Property</strong></h6>
        <span>{{$rowData->single_family != "" ? $rowData->single_family : '--'}}</span>
        </div>
        <div class="col-md-3">
        <h5 style="color:#fff">Company Address</h5>
        <h6 style="color:#999"><strong>Multi Family Property</strong></h6>
        <span>{{$rowData->multi_family != "" ? $rowData->multi_family : '--'}}</span>
        </div>
        <div class="col-md-3">
        <h5 style="color:#fff">Company Address</h5>
        <h6 style="color:#999"><strong>Association Property</strong></h6>
        <span>{{$rowData->association_property != "" ? $rowData->association_property : '--'}}</span>
        </div>
        <div class="col-md-3">
        <h5 style="color:#fff">Company Address</h5>
        <h6 style="color:#999"><strong>Commercial Property</strong></h6>
        <span>{{$rowData->commercial_property != "" ? $rowData->commercial_property : '--'}}</span>
        </div>
        </div>
        </div>
        
        
        <hr />
        
        <div class="card-body">
        <div class="row">
        <div class="col-md-12">
        <h5>About</h5>
        <p>{{nl2br($rowData->about)}}</p>
        </div>
        </div>
        </div>
        
        
        </div>
        </section>
        
        
    </div>
 <!-- plugin js -->
 @stack('plugin-scripts')
<!-- end plugin js -->
<script>
$(document).ready(function(){
	$('#page_main_title').html('My Profile');
});
</script>
<script>
    let saveDataURL = "{{url('/admin/save-profile')}}";
</script>
<script src="{{ asset('public/admin/js/pages/update-profile.js') }}"></script>
@stack('custom-scripts')

@endsection


