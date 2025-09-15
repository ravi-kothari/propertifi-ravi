@php
$adminType = Session::get('admin_type');
$adminId = Session::get('admin_id');
@endphp


@php
$html = '';
$counter = 0;
foreach($records as $key => $row){
    $zipcodeExist = Helper::GetZipcodeSet($agentID,$row->zip_code);
    if($zipcodeExist == 0){
    	$html .= '<div onclick="selectZipcode('.$row->zip_code.');" class="col-6 col-md-2"><div class="single_zipcode">'.$row->zip_code.' <i title="Select This Zipcode" style="color:#ccc; float:right" class="bi bi-exclamation-circle"></i></div></div>';
        $counter++;
    }
}
@endphp
@if($counter > 0)
<div class="card">
    
    <div class="card-content" style="padding:15px;">
    
    <div class="row mb-2" >
    <div class="col-12 col-md-12 text-right">
    <button type="button" class="btn btn-sm btn-primary" id="select_all_btn" onclick="selectAll();" data-kt-menu-dismiss="true">Select All</button>
    </div>
    </div>
       <div class="row" >
      
        {!! $html !!}
          
       </div> 
    </div>
</div>
@endif

@foreach($counties as $key => $county)
@php
$itemID = strtolower($county->county);
$itemID = str_replace(" ","_",$itemID);
$zipcodeExistInCity = Helper::getZipcodeExist($agentID,$county->county);
@endphp
<div class="card">
    <div class="card-content" style="padding:15px;">
    
       <div style="cursor:pointer" ><h5 onclick="showZipcode('{{$itemID}}');" style="float:left">{{$county->county}} ({{count($county->zipcodes)}})</h5> 
       @if($zipcodeExistInCity > 0)
       <button type="button" class="btn btn-sm btn-danger" id="remove_all_btn" onclick="removeAll('{{$county->county}}');" style="float:right;" data-kt-menu-dismiss="true">Remove All</button>
       @endif
       </div>
       
       <div class="row" id="{{$itemID}}" style="display:none; clear:both; padding-top:10px">
        @foreach($county->zipcodes as $key => $zipcode)
        @if($zipcode->status == 1)
        <div onclick="selectZipcode('{{$zipcode->zipcode}}','{{$itemID}}');" class="col-6 col-md-2"><div class="single_zipcode">{{$zipcode->zipcode}} <i title="Remove This Zipcode" style="color:#093; float:right" class="bi bi-check-circle-fill"></i></div></div>
        @else
        <div onclick="selectZipcode('{{$zipcode->zipcode}}','{{$itemID}}');" class="col-6 col-md-2"><div class="single_zipcode">{{$zipcode->zipcode}} <i title="Remove This Zipcode" style="color:#f00; float:right" class="bi bi-x-circle-fill"></i></div></div>
        @endif
        @endforeach
       </div> 
    </div>
    
</div>
@endforeach



        


