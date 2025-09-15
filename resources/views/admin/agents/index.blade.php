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
                                <input id="title" name="title" confirmation="false" class="form-control" placeholder="Search By Company Name">
                            </div>
                            <div class="position-relative w-md-200px me-md-2">
                                <input id="email" name="email" confirmation="false" class="form-control" placeholder="Search By Email">
                            </div>
                            <div class="position-relative w-md-200px me-md-2">
                                <select class="form-select" id="email_varification" name="email_varification">
                                <option value="">Verification</option>
                                <option value="1">Verified</option>
                                <option value="2">Pending</option>
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
                    $propertyPermit = Helper::checkPermission($adminId,3,'deletee');
                    @endphp
                    @if($propertyPermit)
                    <a href="javascript:void(0);" onclick="deleteAll()" class="btn icon btn-sm btn-outline-danger" id="assign_tailor_btn" style="display:none;margin-left:5px">Delete</a>
                    @endif
                     <a onclick="exportData();" id="export_btn" style="margin-left:10px" class="btn icon btn-sm btn-outline-success float-end">Export Data</a>
                    @php
                    $propertyPermit = Helper::checkPermission($adminId,3,'addd');
                    @endphp
                    @if($propertyPermit)
                    <a href="{{url('/admin/add-property-managers')}}" class="btn icon btn-sm btn-outline-primary float-end">Add Property Manager</a>
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
                                        	<th>#</th>
                                            <th>ID</th>
                                            <th>Company Name</th>
                                            <th>Address</th>
                                            <th>Contact No.</th>
                                            <th>Point of Contact</th>
                                            <th>Credits</th>
                                            <th>Verification</th>
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
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="staticBackdropLabel">Add Credit</h5>
             <span id="total_credit">Current Balance: 0 Credits</span> 
          </div>
          <div class="modal-body">
          <form id="creditForm" name="creditForm" class="float-start">
            <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <input type="text" class="form-control" id="credit" onkeyup="calculatePrice();" onblur="calculatePrice();" name="credit">
                    <input type="hidden" id="agent_id" name="agent_id" value="0" />
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="form-group">
                    <input type="radio" name="defind_credit" onchange="$('#credit').val('50');calculatePrice();" value="50"> 50
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="form-group">
                    <input type="radio" name="defind_credit" onchange="$('#credit').val('100');calculatePrice();" value="100"> 100
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="form-group">
                    <input type="radio" name="defind_credit" onchange="$('#credit').val('150');calculatePrice();" value="150"> 150
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="form-group">
                    <input type="radio" name="defind_credit" onchange="$('#credit').val('200');calculatePrice();" value="200"> 200
                </div>
            </div>
            <div class="col-md-12"> <i style="font-size:13px;">1 Credit = ${{$settings->per_credit_amt}}</i></div>
            </div>
            </form>
          </div>
          <div style="position:relative" class="modal-footer">
          <span style="position:absolute;left:14px; font-size:20px">Total: <strong id="totalAMount">$0</strong></span>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" onclick="AddCredit();" id="addCreditBtn" class="btn btn-primary">Add Credit</button>
          </div>
        </div>
      </div>
    </div>
    
<script>
$(document).ready(function(){
    filterData('simple');
	$('#page_main_title').html('Property Managers ({{$agentCount}})');
});
var selected = new Array();
$(document).on('click','.checkbox',function(){
  
  $("input[type=checkbox]:checked").each(function(){
	 selected.push(this.value);
  });
  if(selected.length > 0){
	 $('#assign_tailor_btn').show();
  }else{
	 $('#assign_tailor_btn').hide();
  }
});
function exportData(){
	$('#export_btn').html('Processing...');
	$.ajax({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type: 'POST',
		data: $('#searchForm').serialize(),
		url: "{{ url('/admin/export_property_managers') }}",
		success: function(response){
			$('#export_btn').html('Export Data');
			window.location.href= response;
		}
	});
}
function resendVarificationEmail(id){
	swal({
        title: "Are you sure want to resend email?",
        text: "",
        icon: "warning",
        buttons: true,
        dangerMode: true,
        })
        .then((willDelete) => {
        if (willDelete) {
            $.ajax({
				headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
				type: 'POST',
				data: {id:id},
				url: "{{ url('/admin/resend-varification-email') }}",
				success: function(response){
					if(response == 'Success'){
						swal({
							title: "Success!",
							text: 'Email sent successfully',
							type: "success",
							timer: 3000
						});
					}else{
						swal({
							title: "Error!",
							text: 'Internal error occur',
							type: "error",
							timer: 3000
						});
					}
				}
			});
        } else {
            swal("No action performed!");
        }
        });
}
function deleteAll(){
	
	swal({
        title: "Are you sure?",
        text: "Once deleted, you will not be able to recover this record!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
        })
        .then((willDelete) => {
        if (willDelete) {
            $.ajax({
				headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
				type: 'POST',
				data: {ids:selected},
				url: "{{ url('/admin/delete/property-managers') }}",
				success: function(response){
					filterData('simple');
				}
			});
        } else {
            swal("Your record is safe!");
        }
        });
		
	
}
function filterData(type = null){
    if(type =='search'){$('#searchbuttons').html('Searching..');}
	$.ajax({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type: 'POST',
		data: $('#searchForm').serialize(),
		url: "{{ url('/admin/property_managers_paginate') }}",
		success: function(response){
			$('#replaceHtml').html(response);
            $('#searchbuttons').html('Search');
		}
	});
}
function calculatePrice(){
	var total = 0;
	var perCreditPrice = '{{$settings->per_credit_amt}}';
	var credit = $('#credit').val();
	if(credit >= 0){
		var total = credit*perCreditPrice;
	}
	$('#totalAMount').html('$'+total);
}
function AddCredit(){
	if($.trim($('#credit').val()) == ''){
		showToastr('Error','Please enter credits');
	}else{
		$('#addCreditBtn').html('Processing...');
		$.ajax({
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			type: 'POST',
			data: $('#creditForm').serialize(),
			url: "{{ url('/admin/add-credit') }}",
			success: function(response){
				const obj = JSON.parse(response);
				window.location.href = "{{ route('stripe.checkout') }}?o_id="+obj.o_id;
				//$('#staticBackdrop').modal('hide');
				$('#addCreditBtn').html('Add Credit');
				/*filterData('simple');
				swal({
						title: "Success!",
						text: 'Credit added successfully',
						type: "success",
						timer: 3000
					});*/
			}
		});
	}
	
}
function GetTotalCredit(id){
	$.ajax({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type: 'POST',
		data: {id:id},
		url: "{{ url('/admin/get-total-credits') }}",
		success: function(response){
			$('#total_credit').html('Current Balance: '+response+' Credits');
			calculatePrice();
		}
	});
}

</script>

@endsection


