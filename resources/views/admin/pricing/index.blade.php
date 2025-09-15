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
                                <input id="title" name="title" confirmation="false" class="form-control" placeholder="Keywords..">
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
                    <a data-bs-toggle="modal" data-bs-target="#uploadCsvModal" class="btn icon btn-sm btn-outline-success float-end">Set Per Credit Amount</a>
                    
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
                                            <th>Property Type</th>
                                            <th>Sub category Of</th>
                                            <th>Initial Bid Price (Credits)</th>
                                            <th>Status</th>
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
    <div class="modal fade" id="uploadCsvModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Set Per Credit Amount</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="form-group">
            <label for="basicInput">Per Credit Amount ($)</label>
            <input type="text" class="form-control" value="{!! $setting->per_credit_amt !!}" name="per_credit_amt" id="per_credit_amt">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" onclick="updateAmount();" id="updateAmountBtn" class="btn btn-primary">Submit</button>
          </div>
        </div>
      </div>
    </div>
<script>
$(document).ready(function(){
    filterData('simple');
	$('#page_main_title').html('Coverage & Pricing');
});
function filterData(type = null){
    if(type =='search'){$('#searchbuttons').html('Searching..');}
	$.ajax({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type: 'POST',
		data: $('#searchForm').serialize(),
		url: "{{ url('/admin/pricings_paginate') }}",
		success: function(response){
			$('#replaceHtml').html(response);
            $('#searchbuttons').html('Search');
		}
	});
}
function updatePrice(id){
$('#update_btn_'+id).html('Processing...');
	var price = $('#price_input_'+id).val();
	$.ajax({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type: 'POST',
		data: {id:id,price:price},
		url: "{{ url('/admin/pricing/update') }}",
		success: function(response){
			$('#update_btn_'+id).html('Submit');
			if(response == 'Success'){
				showToastr('Success','Record saved successfully');
				resetFilterForm();
			}else{
				showToastr('Error','Invalid price amount');
			}
			
		}
	});
}
function updateTitle(id){
$('#update_title_btn_'+id).html('Processing...');
	var category = $('#title_input_'+id).val();
	$.ajax({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type: 'POST',
		data: {id:id,category:category},
		url: "{{ url('/admin/category/update') }}",
		success: function(response){
			$('#update_title_btn_'+id).html('Submit');
			if(response == 'Success'){
				showToastr('Success','Record saved successfully');
				resetFilterForm();
			}else{
				showToastr('Error','Invalid price amount');
			}
			
		}
	});
}
function updateAmount(id){
$('#updateAmountBtn').html('Processing...');
	$.ajax({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type: 'POST',
		data: {per_credit_amt:$('#per_credit_amt').val()},
		url: "{{ url('/admin/save-credit-amt') }}",
		success: function(response){
			$('#updateAmountBtn').html('Submit');
			var obj = JSON.parse(response);
			if(obj.success){
				$('#uploadCsvModal').modal('hide');
				showToastr('Success',obj.msg);
			}else{
				showToastr('Error',obj.msg);
			}
			
		}
	});
}

</script>

@endsection


