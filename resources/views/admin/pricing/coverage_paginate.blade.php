@foreach($records as $key => $record)
@php
$pricingSelected = Helper::GetPricingSet($agentID,$record->id);
$subPricings = Helper::GetPricingChild($record->id);
$tiers = Helper::GetTiers();
@endphp
<div class="col-12 col-md-3">

    <div class="card">

         <div class="card-body">
           <div class="coverage_item"><i class="bi bi-house-door-fill"></i></div>
           <div onclick="selectPreference('{{$record->id}}');" class="coverage_select @if($pricingSelected > 0) coverage_select_active @endif"><i class="bi bi-check2"></i></div>
           <input type="hidden" value="{{$pricingSelected}}" />
            <div style="clear:both;margin-bottom:40px;"></div>
           <h5 style="margin-bottom:30px">{{$record->property_type}}</h5>
            @if($subPricings->count() == 0)
            <span style="color:#999; font-weight:bold">Your Bid Price / Lead</span>
            <h5 style="margin-top:15px;">{{$record->bid_price}}</h5>
            @endif
            
            @if($subPricings->count() == 0)
            <div class="min-height"></div>
            @endif
            
            @if($subPricings->count() > 0)
            <table width="100%">
                @foreach($subPricings as $key => $subPricing)
                @php
                $pricingSelected2 = Helper::GetPricingSet($agentID,$subPricing->id);
                @endphp
                <tr>
                <td><input value="{{$subPricing->id}}" onchange="selectPreference('{{$subPricing->id}}');" type="checkbox" @if($pricingSelected2 > 0) checked @endif /></td>
                <td><h5>{{$subPricing->bid_price}}</h5></td>
                <td>{{$subPricing->property_type}}</td>
                </tr>
                @endforeach
            </table>
            @endif
            
           
            <div class="tiers">
            @if($tiers->count() > 0)
            <table width="100%">
            <tr>
            <td></td>
            <td></td>
            <td><strong>Per Lead Credit</strong></td>
            <td><strong>Lead Time</strong></td>
            </tr>
                @foreach($tiers as $key => $tier)
                
                @php
                $tierSelected = Helper::GetTierInfo($agentID,$record->id);
                
                $checked = '';
                if(isset($tierSelected->id) && $tier->id == $tierSelected->tier_id){
                	$checked = 'checked';
                }
                @endphp
                
                <tr>
                <td><input value="{{$tier->id}}" @if($pricingSelected == 0) disabled="disabled" @endif onchange="selectTier('{{$tier->id}}','{{$record->id}}');" name="tier_{{$record->id}}" type="radio" {{$checked}} /></td>
                <td><h5>{{$tier->title}}</h5></td>
                <td>{{$tier->price}}</td>
                <td>{{$tier->timings}} Minutes</td>
                </tr>
                @endforeach
            </table>
            @endif
            </div>
            
            
        </div>
        
        
    </div>
     
</div>
@endforeach