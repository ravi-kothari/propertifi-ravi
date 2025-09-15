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
        {!! $row->title !!}
        </td>
       
        
        <td>
         @if($row->status == 1)
            <a href="javascript:void(0);" onclick="changeStatus('agent_roles','{!!$row->id!!}','{!!$row->status!!}');" class="badge bg-success ">Active</a>
            @else
            <a href="javascript:void(0);"  onclick="changeStatus('agent_roles','{!!$row->id!!}','{!!$row->status!!}');" class="badge bg-danger">In-Active</a>
            @endif
        </td>
        <td>
        <div class="dropdown">
        <a class="dropdown-toggle" style="font-size:23px;" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-three-dots-vertical"></i>
        </a>
        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
        <li><a class="dropdown-item" href="{{ url('/admin/edit-agent-role',base64_encode($row->id)) }}"><i class="bi bi-pencil"></i> &nbsp;Edit</a></li>
        <li><a class="dropdown-item" onclick="deleteData('agent_roles','{{ $row->id }}');"><i class="bi bi-trash"></i> &nbsp;Delete</a></li>
        
        </ul>
        </div>
        
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


