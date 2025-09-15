@extends('layout.admin.dashboard')

@section('content')
<style>
.coverage_item{background-color: #F3F8F9;
    color: #708193;
    float: left;
    font-size: 20px;
    border-radius: 30px;
    padding-top: 11px;
    padding-left: 15px;
    padding-right: 15px;
    padding-bottom: 8px;}
.coverage_select{
	color: #708193;
    float: right;
	color:#fff;
    border-radius: 30px;
    padding-top: 3px;
    padding-left: 6px;
    padding-right: 6px;
    border: 2px solid #708193; cursor:pointer
}
.coverage_select_active{
	border: 2px solid #093 !important;
	background-color:#093;
}
.card {
    min-height: 334px;
}
.tiers{    background: #F0F5F8;
    padding: 12px;
    border-radius: 5px;
    margin-top: 15px;}
.min-height{min-height: 66px;}
</style>
<div class="page-heading">
    <!-- Table head options start -->
    <section class="section">
        <div class="row" id="replaceHtml">
            
        </div>
    </section>
    <!-- Table head options end -->
</div>
 <!-- Modal -->
    
<script>
$(document).ready(function(){
	filterData('simple');
	$('#page_main_title').html('Coverage & Pricing');
});
function filterData(type = null){
	$.ajax({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type: 'POST',
		data: {agentID:'{{$agentID}}'},
		url: "{{ url('/admin/coverage_paginate') }}",
		success: function(response){
			$('#replaceHtml').html(response);
		}
	});
}
function selectPreference(id){
	$.ajax({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type: 'POST',
		data: {id:id,agentID:'{{$agentID}}'},
		url: "{{ url('/admin/coverage/set') }}",
		success: function(response){
			showToastr('Success','Record saved successfully');
			filterData('simple');
		}
	});
}
function selectTier(tid,id){
	$.ajax({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type: 'POST',
		data: {tid:tid,id:id,agentID:'{{$agentID}}'},
		url: "{{ url('/admin/tier/set') }}",
		success: function(response){
			showToastr('Success','Record saved successfully');
			filterData('simple');
		}
	});
}
</script>

@endsection


