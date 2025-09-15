@php
$adminId = Session::get('admin_id');
@endphp
@extends('layout.admin.dashboard')

@section('content')

<div class="page-heading">
        <div class="page-title">
            <div class="row">
                
                <div class="col-12 col-md-12 order-md-2">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{url('/admin')}}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ url('/admin/property-managers'); }}">Property Managers</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{$rowData->company_name}}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
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
        {{$rowData->mobile}}
        </div>
        <div class="col-md-1">
        @if($rowData->status == 1)
        <a class="btn btn-sm btn-success fw-bolder me-3 my-2">ACTIVE</a>
        @else
        <a class="btn btn-sm btn-danger fw-bolder me-3 my-2">INACTIVE</a>
        @endif
        
        

        </div>
        <div class="col-md-1 pt-2">
        <div class="dropdown">
        <a class="dropdown-toggle" style="font-size:23px;" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-three-dots-vertical"></i>
        </a>
        @php
        $propertyPermit = Helper::checkPermission($adminId,3,'edit');
        @endphp
        @if($propertyPermit)

        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
        <li><a class="dropdown-item" href="{{ url('/admin/edit-property-managers',base64_encode($rowData->id)) }}"><i class="bi bi-pencil"></i> &nbsp;Edit</a></li>
        </ul>
        @endif
        </div>
        </div>
        </div>
        </div>
        </div>
        </section>
        @php
        $varified = 'Verified';
        if($rowData->email_varification == 2){
        	$varified = 'Pending';
        }
        @endphp
        <section class="section">
            <div class="card">
                
                <div class="card-body">
                <form class="form w-100" id="pageForm" action="#">
                    <div class="row">
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label style="color:#000; font-size:19px;" for="basicInput"><strong>Address</strong></label><br /><br />
                           {{$rowData->address}}
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label style="color:#000; font-size:19px;" for="basicInput"><strong>Verification</strong></label><br /><br />
                           {{$varified}}
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label style="color:#000; font-size:19px;" for="basicInput"><strong>Point of Contact</strong></label><br /><br />
                           {{$rowData->p_contact_no}} | {{$rowData->p_contact_name}} | {{$rowData->p_contact_email}}
                        </div>
                    </div>
                     
                        
                    </div>
                    
                    <hr />
                    
                    <div class="row">
                    
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="basicInput">About</label><br />
                           {!! nl2br($rowData->about) !!}
                        </div>
                    </div>
                    
                     
                        
                    </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
<!-- end plugin js -->
<script>
    let saveDataURL = "{{url('/admin/edit-property-managers/'.$row_id)}}";
    let returnURL = "{{url('/admin/property-managers')}}";
</script>
<script src="{{ asset('public/admin/js/pages/agents/add-page.js') }}"></script>
<script>
$(document).ready(function(){
	$('#page_main_title').html('Edit Property Manager');
});
</script>

@endsection


