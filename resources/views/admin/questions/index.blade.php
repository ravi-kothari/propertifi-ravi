@extends('layout.admin.dashboard')
@php
$adminId = Session::get('admin_id');
@endphp
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
                                <input id="question" name="question" confirmation="false" class="form-control" placeholder="Search By Question">
                            </div>
                            <div class="position-relative w-md-200px me-md-2">
                                <select class="form-select" id="category" name="category">
                                <option value="">Category</option>
                                <option value="1">Single Family</option>
                                <option value="2">Multi Family</option>
                                <option value="3">Association</option>
                                <option value="8">Commercial</option>
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
                    </form>
                    @php
                    $questionPermit = Helper::checkPermission($adminId,10,'addd');
                    @endphp
                    @if($questionPermit)
                    <a href="{{url('/admin/add-question')}}" class="btn icon btn-sm btn-outline-primary float-end">Add Question</a>
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
                                            <th>ID</th>
                                            <th>Question</th>
                                            <th>Category</th>
                                            <th>Type</th>
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
	$('#page_main_title').html('Questions');
});
function filterData(type = null){
    if(type =='search'){$('#searchbuttons').html('Searching..');}
	$.ajax({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type: 'POST',
		data: $('#searchForm').serialize(),
		url: "{{ url('/admin/questions_paginate') }}",
		success: function(response){
			$('#replaceHtml').html(response);
            $('#searchbuttons').html('Search');
		}
	});
}


</script>

@endsection


