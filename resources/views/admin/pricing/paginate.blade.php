@php
$adminId = Session::get('admin_id');
@endphp
@if($records->count()>0)
    @foreach($records as $key => $row)
    
    <tr>
    <td>
            <div class="d-flex align-items-center">
                {!! $row->id !!}
            </div>
        </td>
        <td>
        <div id="title_{{$row->id}}">{{$row->property_type}} <a style="cursor:pointer" onclick="$('#title_{{$row->id}}').hide();$('#title_div_{{$row->id}}').show();"><i class="bi bi-pencil"></i></a></div>
        
        <div style="display:none" id="title_div_{{$row->id}}">
        <table>
        <tr>
        <td><input type="text" class="form-control" id="title_input_{{$row->id}}" value="{{$row->property_type}}" /></td>
        <td>&nbsp;<button type="reset" class="btn btn-sm btn-dark btn-active-light-primary me-5" id="update_title_btn_{{$row->id}}" data-kt-menu-dismiss="true"  onclick="updateTitle('{{$row->id}}');">Submit</button></td>
        </tr>
        </table>
        </div>
        
        </td>
        <td>{{$row->sub_category}}</td>
        <td>
        @php
        $testPermit = Helper::checkPermission($adminId,5,'edit');
        @endphp
        @if($testPermit)
        <div id="price_{{$row->id}}">{{$row->bid_price}} <a style="cursor:pointer" onclick="$('#price_{{$row->id}}').hide();$('#price_div_{{$row->id}}').show();"><i class="bi bi-pencil"></i></a></div>@endif
        <div style="display:none" id="price_div_{{$row->id}}">
        <table>
        <tr>
        <td><input type="text" class="form-control" id="price_input_{{$row->id}}" value="{{$row->bid_price}}" style="width:120px" /></td>
        <td>&nbsp;<button type="reset" class="btn btn-sm btn-dark btn-active-light-primary me-5" id="update_btn_{{$row->id}}" data-kt-menu-dismiss="true"  onclick="updatePrice('{{$row->id}}');">Submit</button></td>
        </tr>
        </table>
        </div>
        </td>
        <td>
        @php
        $testPermit = Helper::checkPermission($adminId,5,'edit');
        @endphp
        @if($testPermit)
        @if($row->status == 1)
        <a href="javascript:void(0);" onclick="changeStatus('pricings','{!!$row->id!!}','{!!$row->status!!}');" class="badge bg-success ">Active</a>
        @else
        <a href="javascript:void(0);"  onclick="changeStatus('pricings','{!!$row->id!!}','{!!$row->status!!}');" class="badge bg-danger">In-Active</a>
        @endif
        @endif
        </td>
        
    </tr>

    @endforeach
        @else
        <tr>
            <td align="center" colspan="6">Record not found</td>
        </tr>
        @endif
        <tr>
            <td align="center" colspan="10">
                <div id="pagination">{!! $records->links('pagination.front') !!}</div>
            </td>
        </tr>


