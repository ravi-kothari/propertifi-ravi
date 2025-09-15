@php
$adminId = Session::get('admin_id');
@endphp
@if($records->count()>0)
    @foreach($records as $key => $row)
    @php
    $varified = 'Verified';
    if($row->email_varification == 2){
    $varified = 'Pending';
   	}
    @endphp
    <tr>
        <td>
        <div class="d-flex align-items-center">
        {!! $row->id !!}
        </div>
        </td>
        <td>
        {!! $row->page_name !!}
        </td>
        
        
        <td>
        @php
        $innerPermit = Helper::checkPermission($adminId,8,'edit');
        @endphp
        @if($innerPermit)
        @if($row->status == 1)
        <a href="javascript:void(0);" onclick="changeStatus('inner_pages','{!!$row->id!!}','{!!$row->status!!}');" class="badge bg-success ">Active</a>
        @else
        <a href="javascript:void(0);"  onclick="changeStatus('inner_pages','{!!$row->id!!}','{!!$row->status!!}');" class="badge bg-danger">In-Active</a>
        @endif
        @endif
        </td>
        <td>{{$row->created_at}}</td>
        <td>
        @php
        $innerPermit = Helper::checkPermission($adminId,8,'edit');
        @endphp
        @if($innerPermit)
        <a href="{{ url('/admin/edit-inner-page',base64_encode($row->id)) }}"><i class="bi bi-pencil"></i> &nbsp;Edit</a>
        @else
        <a href="{{ url('/admin/edit-inner-page',base64_encode($row->id)) }}"><i class="bi bi-eye"></i> &nbsp;View</a>
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


