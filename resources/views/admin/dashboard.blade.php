@php
$adminType = Session::get('admin_type');
$adminID = Session::get('admin_id');
if($adminType == 'Agent'){
	$profileComplete = Helper::getProfileCompleted($adminID);
	$ZipcodePreference = Helper::getZipcodePreference($adminID);
    $pTypePreference = Helper::getPropertTypePreference($adminID);
}
@endphp

@extends('layout.admin.dashboard')
@section('content')

    @if($adminType == 'Agent')
    @if($profileComplete < 50)
    <div class="error_notification">Your profile is {{$profileComplete}}% complete. Please complete your profile. <a style="color:#06F" href="{{url('/admin/edit-agent-profile')}}">Click Here</a></div>
    @endif
    @if($pTypePreference == 0)
    <div class="error_notification">Set your property type preferences. <a style="color:#06F" href="{{url('/admin/coverage')}}">Click Here</a></div>
    @endif
    @if($ZipcodePreference == 0)
    <div class="error_notification">Set your zipcode preferences. <a style="color:#06F" href="{{url('/admin/zipcodes')}}">Click Here</a></div>
    @endif
    @endif
    
    @php
    $AnalyticPermit = Helper::checkPermission($adminID,14,'view');
    @endphp
    
    <div class="page-heading">
        <h3>Welcome to your Dashbaord</h3>
        @if($AnalyticPermit)
        <h5>Below data is for last 30 Days</h5>
        @endif
    </div>
    
    @if($AnalyticPermit)
    <section class="row">
    <div class="col-12 col-lg-12">
    <div class="row">
    <div class="col-6 col-lg-3 col-md-6">
    <div class="card">
    <div class="card-body px-3 py-2-5">
     <div class="row">
    <div class="col-md-3">
    <div class="stats-icon purple">
    <i class="iconly-boldShow"></i>
    </div>
    </div>
    <div class="col-md-9">
    <h6 class="font-semibold">{{$leadCount}}</h6>
    <h6 class="font-extrabold mb-0">Total Leads <i class="bi bi-chevron-right"></i></h6>
    </div>
    </div>
    </div>
    </div>
    </div>
    <div class="col-6 col-lg-3 col-md-6">
    <div class="card">
    <div class="card-body px-3 py-2-5">
    <div class="row">
    <div class="col-md-3">
    <div class="stats-icon blue">
    <i class="iconly-boldProfile"></i>
    </div>
    </div>
    <div class="col-md-9">
    <h6 class="font-semibold">{{$totalCredit}}</h6>
    <h6 class="font-extrabold mb-0">Total Sold Credits <i class="bi bi-chevron-right"></i></h6>
    </div>
    </div>
    </div>
    </div>
    </div>
    <div class="col-6 col-lg-3 col-md-6">
    <div class="card">
    <div class="card-body px-3 py-2-5">
    <div class="row">
    <div class="col-md-3">
    <div class="stats-icon green">
    <i class="iconly-boldAdd-User"></i>
    </div>
    </div>
    <div class="col-md-9">
    <h6 class="font-semibold">${{$totalCreditAmt}}</h6>
    <h6 class="font-extrabold mb-0">Revenue from Sold Credits <i class="bi bi-chevron-right"></i></h6>
    </div>
    </div>
    </div>
    </div>
    </div>
    <div class="col-6 col-lg-3 col-md-6">
    <div class="card">
    <div class="card-body px-3 py-2-5">
    <div class="row">
    <div class="col-md-3">
    <div class="stats-icon red">
    <i class="iconly-boldBookmark"></i>
    </div>
    </div>
    <div class="col-md-9">
    <h6 class="font-semibold">{{$agents}}</h6>
    <h6 class="font-extrabold mb-0">Total Property Managers <i class="bi bi-chevron-right"></i></h6>
    </div>
    </div>
    </div>
    </div>
    </div>
    </div>
    </div>
    </section>
    @endif
    
    @php
    $leadPermit = Helper::checkPermission($adminID,2,'view');
    @endphp
    @if($leadPermit)
    
    <div class="page-title">
    <div class="row">
    <div class="col-12 col-md-6 order-md-1 order-last">
    <h4>Recent Leads (5)</h4>
    </div>
    <div class="col-12 col-md-6 order-md-2 order-first" style="text-align:right">
    <a style="color:#2D74E6; font-weight:bold; font-size:18px" href="{{url('/admin/leads')}}">View All Leads <i class="bi bi-chevron-right"></i></a>
    </div>
    </div>
    </div>
    <div class="page-content">
    <section class="section">
    <div class="row" id="table-head">
    <div class="col-12">
    <div class="card">
    
    <div class="card-content">
    <!-- table head dark -->
    <div class="table-responsive">
    <table class="table mb-0">
    <thead class="thead-dark">
    <tr>
    <th>Customer Information</th>
    <th>Lead ID</th>
    <th>Property Address</th>
    <th>Contact No.</th>
    <th>Property Type</th>
    <th>Date</th>
    </tr>
    </thead>
    <tbody id="replaceHtml">
    @foreach($leads as $key => $row)
    @php
    $category = 'N/A';
    if($row->category == 1){
    $category = 'Single Family';
    }else if($row->category == 2){
    $category = 'Multi Family';
    }else if($row->category == 3){
    $category = 'Association Property';
    }else if($row->category == 8){
    $category = 'Commercial Property';
    }
    @endphp
    <tr>
    <td><strong>{{ucwords($row->name)}}</strong><br>{{strtolower($row->email)}}</td>
    <td>{{$row->unique_id}}</td>
    <td>{{$row->address}}</td>
    <td>{{$row->phone}}</td>
    <td>{{$category}}</td>
    <td>{{$row->created_at}}</td>
    </tr>
    @endforeach
    
    </tbody>
    </table>
    </div>
    </div>
    </div>
    </div>
    </div>
    </section>
    
    <!--<section class="row">
    <div class="col-12 col-lg-9">
    <div class="row">
    <div class="col-6 col-lg-3 col-md-6">
    <div class="card">
    <div class="card-body px-3 py-4-5">
    <div class="row">
    <div class="col-md-4">
    <div class="stats-icon purple">
    <i class="iconly-boldShow"></i>
    </div>
    </div>
    <div class="col-md-8">
    <h6 class="text-muted font-semibold">Product Out / Stock</h6>
    <h6 class="font-extrabold mb-0">0</h6>
    </div>
    </div>
    </div>
    </div>
    </div>
    <div class="col-6 col-lg-3 col-md-6">
    <div class="card">
    <div class="card-body px-3 py-4-5">
    <div class="row">
    <div class="col-md-4">
    <div class="stats-icon blue">
    <i class="iconly-boldProfile"></i>
    </div>
    </div>
    <div class="col-md-8">
    <h6 class="text-muted font-semibold">No Of Products</h6>
    <h6 class="font-extrabold mb-0">0</h6>
    </div>
    </div>
    </div>
    </div>
    </div>
    <div class="col-6 col-lg-3 col-md-6">
    <div class="card">
    <div class="card-body px-3 py-4-5">
    <div class="row">
    <div class="col-md-4">
    <div class="stats-icon green">
    <i class="iconly-boldAdd-User"></i>
    </div>
    </div>
    <div class="col-md-8">
    <h6 class="text-muted font-semibold">No Of Orders</h6>
    <h6 class="font-extrabold mb-0">0</h6>
    </div>
    </div>
    </div>
    </div>
    </div>
    <div class="col-6 col-lg-3 col-md-6">
    <div class="card">
    <div class="card-body px-3 py-4-5">
    <div class="row">
    <div class="col-md-4">
    <div class="stats-icon red">
    <i class="iconly-boldBookmark"></i>
    </div>
    </div>
    <div class="col-md-8">
    <h6 class="text-muted font-semibold">Total Income</h6>
    <h6 class="font-extrabold mb-0">0</h6>
    </div>
    </div>
    </div>
    </div>
    </div>
    </div>
    <div class="row">
    <div class="col-12">
    <div class="card">
    <div class="card-header">
    <h4>Profile Visit</h4>
    </div>
    <div class="card-body">
    <div id="chart-profile-visit"></div>
    </div>
    </div>
    </div>
    </div>
    <div class="row">
    <div class="col-12 col-xl-4">
    <div class="card">
    <div class="card-header">
    <h4>Profile Visit</h4>
    </div>
    <div class="card-body">
    <div class="row">
    <div class="col-6">
    <div class="d-flex align-items-center">
    <svg class="bi text-primary" width="32" height="32" fill="blue"
    style="width:10px">
    <use
    xlink:href="assets/vendors/bootstrap-icons/bootstrap-icons.svg#circle-fill" />
    </svg>
    <h5 class="mb-0 ms-3">Europe</h5>
    </div>
    </div>
    <div class="col-6">
    <h5 class="mb-0">862</h5>
    </div>
    <div class="col-12">
    <div id="chart-europe"></div>
    </div>
    </div>
    <div class="row">
    <div class="col-6">
    <div class="d-flex align-items-center">
    <svg class="bi text-success" width="32" height="32" fill="blue"
    style="width:10px">
    <use
    xlink:href="assets/vendors/bootstrap-icons/bootstrap-icons.svg#circle-fill" />
    </svg>
    <h5 class="mb-0 ms-3">America</h5>
    </div>
    </div>
    <div class="col-6">
    <h5 class="mb-0">375</h5>
    </div>
    <div class="col-12">
    <div id="chart-america"></div>
    </div>
    </div>
    <div class="row">
    <div class="col-6">
    <div class="d-flex align-items-center">
    <svg class="bi text-danger" width="32" height="32" fill="blue"
    style="width:10px">
    <use
    xlink:href="assets/vendors/bootstrap-icons/bootstrap-icons.svg#circle-fill" />
    </svg>
    <h5 class="mb-0 ms-3">Indonesia</h5>
    </div>
    </div>
    <div class="col-6">
    <h5 class="mb-0">1025</h5>
    </div>
    <div class="col-12">
    <div id="chart-indonesia"></div>
    </div>
    </div>
    </div>
    </div>
    </div>
    <div class="col-12 col-xl-8">
    <div class="card">
    <div class="card-header">
    <h4>Latest Comments</h4>
    </div>
    <div class="card-body">
    <div class="table-responsive">
    <table class="table table-hover table-lg">
    <thead>
    <tr>
    <th>Name</th>
    <th>Comment</th>
    </tr>
    </thead>
    <tbody>
    <tr>
    <td class="col-3">
    <div class="d-flex align-items-center">
    <div class="avatar avatar-md">
    <img src="{{ asset('public/admin/images/faces/5.jpg') }}">
    </div>
    <p class="font-bold ms-3 mb-0">Si Cantik</p>
    </div>
    </td>
    <td class="col-auto">
    <p class=" mb-0">Congratulations on your graduation!</p>
    </td>
    </tr>
    <tr>
    <td class="col-3">
    <div class="d-flex align-items-center">
    <div class="avatar avatar-md">
    <img src="{{ asset('public/admin/images/faces/2.jpg') }}">
    </div>
    <p class="font-bold ms-3 mb-0">Si Ganteng</p>
    </div>
    </td>
    <td class="col-auto">
    <p class=" mb-0">Wow amazing design! Can you make another
    tutorial for
    this design?</p>
    </td>
    </tr>
    </tbody>
    </table>
    </div>
    </div>
    </div>
    </div>
    </div>
    </div>
    <div class="col-12 col-lg-3">
    <div class="card">
    <div class="card-body py-4 px-5">
    <div class="d-flex align-items-center">
    <div class="avatar avatar-xl">
    <img src="{{ asset('public/admin/images/faces/1.jpg') }}" alt="Face 1">
    </div>
    <div class="ms-3 name">
    <h5 class="font-bold">{{Session::get('admin_name')}}</h5>
    <h6 class="text-muted mb-0">{{Session::get('admin_email')}}</h6>
    </div>
    </div>
    </div>
    </div>
    @if(Session::get('admin_type') == 'Admin')
    <div class="card">
    <div class="card-body px-3 py-4-5">
    <div class="row">
    <div class="col-md-4">
    <div class="stats-icon blue">
    <i class="iconly-boldProfile"></i>
    </div>
    </div>
    <div class="col-md-8">
    <h6 class="text-muted font-semibold">No Of Customers</h6>
    <h6 class="font-extrabold mb-0">0</h6>
    </div>
    </div>
    </div>
    </div>
    @endif
    <div class="card">
    <div class="card-header">
    <h4>Recent Messages</h4>
    </div>
    <div class="card-content pb-4">
    <div class="recent-message d-flex px-4 py-3">
    <div class="avatar avatar-lg">
    <img src="{{ asset('public/admin/images/faces/4.jpg') }}">
    </div>
    <div class="name ms-4">
    <h5 class="mb-1">Hank Schrader</h5>
    <h6 class="text-muted mb-0">@johnducky</h6>
    </div>
    </div>
    <div class="recent-message d-flex px-4 py-3">
    <div class="avatar avatar-lg">
    <img src="{{ asset('public/admin/images/faces/5.jpg') }}">
    </div>
    <div class="name ms-4">
    <h5 class="mb-1">Dean Winchester</h5>
    <h6 class="text-muted mb-0">@imdean</h6>
    </div>
    </div>
    <div class="recent-message d-flex px-4 py-3">
    <div class="avatar avatar-lg">
    <img src="{{ asset('public/admin/images/faces/1.jpg') }}">
    </div>
    <div class="name ms-4">
    <h5 class="mb-1">John Dodol</h5>
    <h6 class="text-muted mb-0">@dodoljohn</h6>
    </div>
    </div>
    <div class="px-4">
    <button class='btn btn-block btn-xl btn-light-primary font-bold mt-3'>Start
    Conversation</button>
    </div>
    </div>
    </div>
    <div class="card">
    <div class="card-header">
    <h4>Visitors Profile</h4>
    </div>
    <div class="card-body">
    <div id="chart-visitors-profile"></div>
    </div>
    </div>
    </div>
    </section>-->
    </div>
    @endif
    <script src="{{ asset('public/admin/vendors/apexcharts/apexcharts.js') }}"></script>
    <script src="{{ asset('public/admin/js/pages/dashboard.js') }}"></script>
    <script>
$(document).ready(function(){
	$('#page_main_title').html('');
});
</script>
@endsection
