@foreach($zipcodes as $key => $zipcode)
@if($zipcode->status == 1)
<div onclick="selectZipcode('{{$zipcode->zipcode}}','{{$itemID}}');" class="col-6 col-md-2"><div class="single_zipcode">{{$zipcode->zipcode}} <i title="Remove This Zipcode" style="color:#093; float:right" class="bi bi-check-circle-fill"></i></div></div>
@else
<div onclick="selectZipcode('{{$zipcode->zipcode}}','{{$itemID}}');" class="col-6 col-md-2"><div class="single_zipcode">{{$zipcode->zipcode}} <i title="Remove This Zipcode" style="color:#f00; float:right" class="bi bi-x-circle-fill"></i></div></div>
@endif
@endforeach