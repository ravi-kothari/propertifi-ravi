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
                                <input id="unique_id" name="unique_id" confirmation="false" class="form-control" placeholder="Search Key..">
                            </div>
                            <div class="position-relative w-md-200px me-md-2">
                                <select name="category" id="category" class="form-select">
                                <option value="">Property Type</option>
                                <option value="1">Single Family</option>
                                <option value="2">Multi Family</option>
                                <option value="3">Association Property</option>
                                <option value="8">Commercial Property</option>
                                </select>
                            </div>
                            <div class="position-relative w-md-200px me-md-2">
                                <input id="s_date" name="s_date" confirmation="false" class="form-control" placeholder="From Date">
                            </div>
                            <div class="position-relative w-md-200px me-md-2">
                                <input id="e_date" name="e_date" confirmation="false" class="form-control" placeholder="To Date">
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
                    <a onclick="exportData();" id="export_btn" class="btn icon btn-sm btn-outline-success float-end">Export Data</a>
                    <!--<a href="{{url('/admin/add-brand')}}" class="btn icon btn-sm btn-outline-success float-end">Add New Brand</a>-->
                    
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
                                            <th>Customer Information</th>
                                            <th>Lead ID</th>
                                            <th>Property Address</th>
                                            <th>Contact No.</th>
                                            <th>Property Type</th>
                                            <th>Other Details</th>
                                            <th>Date</th>
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
        <div class="modal fade" id="questions" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog" style="max-width: 678px;">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="staticBackdropLabel">Question/Answers</h5>
          </div>
          <div class="modal-body">
            <div class="row">
            <div class="col-md-12">
                <div class="form-group" id="question_answer_data">
                
                Loading...
                
                </div>
            </div>
            
            
            </div>
          </div>
          <div style="position:relative" class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    </div>
     <!-- Modal -->
    
<script>
$(document).ready(function(){
    filterData('simple');
	$('#page_main_title').html('Lead History');
	jQuery('#s_date').datepicker({});
	jQuery('#e_date').datepicker({});
});
function exportData(){
	$('#export_btn').html('Processing...');
	$.ajax({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type: 'POST',
		data: $('#searchForm').serialize(),
		url: "{{ url('/admin/export_leads') }}",
		success: function(response){
			$('#export_btn').html('Export Data');
			window.location.href= response;
		}
	});
}
function filterData(type = null){
    if(type =='search'){$('#searchbuttons').html('Searching..');}
	$.ajax({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type: 'POST',
		data: $('#searchForm').serialize(),
		url: "{{ url('/admin/leads_paginate') }}",
		success: function(response){
			$('#replaceHtml').html(response);
            $('#searchbuttons').html('Search');
		}
	});
}
function getQuestionAnswers(leadID){
	$('#question_answer_data').html('Loading...');
	$.ajax({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type: 'POST',
		data:{leadID:leadID},
		url: "{{ url('/admin/lead/question-answer') }}",
		success: function(response){
			$('#question_answer_data').html(response);
		}
	});
}


</script>

@endsection


