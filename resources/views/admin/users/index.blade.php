@extends('layout.admin.dashboard')

@section('content')

<div class="page-heading">
        
        <section class="section">
            <div class="card">
               <!--begin::Card body-->
                <div class="card-body">
                    <!--begin::Compact form-->
                    <form id="searchForm" name="searchForm" class="float-start">
                        <div class="d-flex align-items-center  w-md-800px">
                            <!--begin::Input group-->
                            <div class="position-relative w-md-200px me-md-2">
                                <input id="title" name="title" confirmation="false" class="form-control" placeholder="Search By Name">
                            </div>
                            <div class="position-relative w-md-200px me-md-2">
                            <select class="form-select" id="role_id" name="role_id">
                                <option value="">User Role</option>
                                @foreach($roles as $key => $role)
                                <option value="{{$role->id}}">{{$role->title}}</option>
                                @endforeach
                                </select>
                                </div>
                            <div class="position-relative w-md-200px me-md-2">
                                <select class="form-select" id="status" name="status">
                                <option value="">Status</option>
                                <option value="1">Active</option>
                                <option value="2">Inactive</option>
                                </select>
                            </div>
                            <!--end::Input group-->
                            <!--begin:Action-->
                            <div class="d-flex align-items-center">
                                <button type="button" id="searchbuttons" onclick="filterData('search');" style="margin-right:10px;" class="btn btn-sm btn-primary" data-kt-menu-dismiss="true">Search</button>
                                <button type="reset" class="btn btn-sm btn-dark btn-active-light-primary me-5" data-kt-menu-dismiss="true"  onclick="resetFilterForm();">Reset</button>
                            </div>
                            <!--end:Action-->
                        </div>
                        <input type="hidden" name="agentID" value="{{$agentID}}" />
                    </form>
                    <a href="javascript:void(0);" onclick="deleteAll()" class="btn icon btn-sm btn-outline-danger" id="assign_tailor_btn" style="display:none;margin-left:5px">Delete</a>
                    @if(Session::get('admin_type') == 'Agent')
                    <a href="{{url('/admin/add-user')}}" class="btn icon btn-sm btn-outline-primary float-end">Add User</a>
                    @endif
                    
                </div>
                <!--end::Card body-->
            </div>
        </section>
        <!-- Table head options start -->
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
                                        
                                            <th>#ID</th>
                                            <th>ROLE</th>
                                            <th>Name</th>
                                            <th>Email Address</th>
                                            <th>Contact No.</th>
                                             
                                            <th>Status</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="replaceHtml">
                                        <tr>
                                            <td colspan="10" class="text-center"><img src="{{ asset('public/admin/images/svg/oval.svg') }}" class="me-4" style="width: 3rem" alt="audio"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Table head options end -->
    </div>
     <!-- Modal -->
     <!-- Modal -->
    
    
<script>
$(document).ready(function(){
    filterData('simple');
	$('#page_main_title').html('Users');
});

function filterData(type = null){
    if(type =='search'){$('#searchbuttons').html('Searching..');}
	$.ajax({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type: 'POST',
		data: $('#searchForm').serialize(),
		url: "{{ url('/admin/users_paginate') }}",
		success: function(response){
			$('#replaceHtml').html(response);
            $('#searchbuttons').html('Search');
		}
	});
}

</script>

@endsection


