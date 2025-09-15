@extends('layout.admin.dashboard')

@section('content')
@php
$adminType = Session::get('admin_type');
$adminID = Session::get('admin_id');
@endphp
<style>
.single_zipcode {
    border: 1px solid #ccc;
    padding: 5px;
    margin-bottom: 10px;
    border-radius: 4px;
    cursor: pointer;
}
</style>
<div class="page-heading">
        
        <section class="section">
            <div class="card">
               <!--begin::Card body-->
                <div class="card-body">
                    <!--begin::Compact form-->
                    <form id="searchForm" name="searchForm" class="float-start">
                    <input type="hidden" name="agentID" value="{{$agentID}}" />
                        <div class="d-flex align-items-center  w-md-800px">
                            <!--begin::Input group-->
                            <div class="position-relative w-md-200px me-md-2">
                                <input id="city_name" name="city_name" confirmation="false" class="form-control" placeholder="City Name">
                 				<div class="AutoCityNameDiv" style="position:relative"></div>
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
                    
                    
                </div>
                <!--end::Card body-->
            </div>
        </section>
        <!-- Table head options start -->
        <section class="section">
            <div class="row" id="table-head">
                <div id="replaceHtml" class="col-12">
                    
                </div>
            </div>
        </section>
        <!-- Table head options end -->
    </div>
     <!-- Modal -->
     
     <!-- Modal -->
     <!-- Modal -->
    
    
<script>
$(function() {
	$("#city_name" ).autocomplete({
		appendTo: '.AutoCityNameDiv',
		source: "{{route('admin.search-zipcode-auto')}}",
		minLength: 2,
		select: function( event, ui ) {
			$("#city_name").val(ui.item.value);
		}
	});
});
$(document).ready(function(){
    filterData('simple');
	$('#page_main_title').html('Zipcodes');
});
function selectZipcode(zipcode,divID=null){
	$.ajax({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type: 'POST',
		data:{zipcode:zipcode,agentID:'{{$agentID}}'},
		url: "{{ route('admin.set-user-zipcodes') }}",
		success: function(response){
			showToastr('Success','Record saved successfully');
			if(divID == null){
				filterData('simple');
			}else{
				getSingleZipcode(divID);
				
			}
		}
	});
}
function getSingleZipcode(county){
	$.ajax({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type: 'POST',
		data:{county:county,agentID:'{{$agentID}}'},
		url: "{{ route('admin.get-single-country-data') }}",
		success: function(response){
			$('#'+county).html(response);
		}
	});
}
function filterData(type = null){
    if(type =='search'){$('#searchbuttons').html('Searching..');}
	$.ajax({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type: 'POST',
		data: $('#searchForm').serialize(),
		url: "{{ url('/admin/zipcodes_paginate') }}",
		success: function(response){
			$('#replaceHtml').html(response);
            $('#searchbuttons').html('Search');
		}
	});
}
function removeAll(cityName){
	if(cityName != ""){
		
		
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
				data:{cityName:cityName,agentID:'{{$agentID}}'},
				url: "{{ route('admin.remove-all-zipcodes') }}",
				success: function(response){
					filterData('simple');
				}
			});
        } else {
            swal("Your record is safe!");
        }
        });
	}
}
function selectAll(){
	var cityName = $("#city_name").val();
	if(cityName != ""){
		$('#select_all_btn').html('Processing...');
		$.ajax({
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			type: 'POST',
			data:{cityName:cityName,agentID:'{{$agentID}}'},
			url: "{{ route('admin.set-all-zipcodes') }}",
			success: function(response){
				filterData('simple');
			}
		});
	}
}
function showZipcode(county){
	$('#'+county).slideToggle();
}



</script>

@endsection


