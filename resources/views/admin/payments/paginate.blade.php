@php
$adminType = Session::get('admin_type');
@endphp
@if($records->count()>0)
    @foreach($records as $key => $row)
    @php
    if($adminType == 'Admin' || $adminType == 'AccountManager'){
    	$company = Helper::GetUserData($row->user_id);
    }
    $state = Helper::GetStateData($row->state);
    $city = Helper::GetCityData($row->city);
    @endphp
    <tr>
        <td>
        <div class="d-flex align-items-center">
        {!! $key+1 !!}
        </div>
        </td>
        <td>{!! $row->invoice_id !!}</td>
        <td>
        @if($row->status == 1)
        <a data-bs-toggle="modal" data-bs-target="#txnIdpopup" onclick="$('#strip_session_id').html('{!! $row->stripe_session_id !!}');" style="cursor:pointer; color:#00F" title="{!! $row->stripe_session_id !!}">{!! $row->txn_id !!}</a>
        @else
        {{$row->txn_id}}
        @endif
        
        </td>
        @if($adminType == 'Admin' || $adminType == 'AccountManager')
        <td>{{$company->company_name}}</td>
        @endif
        <td>{!! $state !!}</td>
        <td>{!! $city !!}</td>
        <td>{!! $row->credit !!}</td>
        <td>${!! $row->amount !!}</td>
        <td>{!! $row->created_at !!}</td>
        <td>
        @if($row->status == 1)
        <span class="badge bg-success">Success</span>
        @else
        <span class="badge bg-danger">Failed</span>
        @endif
        </td> 
    </tr>

    @endforeach
        @else
        <tr>
            <td align="center" colspan="16">Record not found</td>
        </tr>
        @endif
        <tr>
            <td align="center" colspan="16">
                <div id="pagination">{!! $records->links('pagination.front') !!}</div>
            </td>
        </tr>


