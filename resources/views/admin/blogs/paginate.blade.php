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
        {!! $row->heading !!}
        </td>
        <td>
        {!! $row->cate_name !!}
        </td>
        <td>
        @if($row->banner != '')
        <img src="{{URL::asset('public/img/blogs/')}}/{!! $row->banner !!}" width="100">
        @else
        Not Available
        @endif
        </td>
        
        <td>
        @php
        $blogPermit = Helper::checkPermission($adminId,6,'edit');
        @endphp
        @if($blogPermit)
        @if($row->status == 1)
        <a href="javascript:void(0);" onclick="changeStatus('blogs','{!!$row->id!!}','{!!$row->status!!}');" class="badge bg-success ">Active</a>
        @else
        <a href="javascript:void(0);"  onclick="changeStatus('blogs','{!!$row->id!!}','{!!$row->status!!}');" class="badge bg-danger">In-Active</a>
        @endif
        @endif
        </td>
        <td>{{$row->created_at}}</td>
        <td>
        <div class="dropdown">
        <a class="dropdown-toggle" style="font-size:23px;" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-three-dots-vertical"></i>
        </a>
        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
        @php
        $blogPermit = Helper::checkPermission($adminId,6,'edit');
        @endphp
        @if($blogPermit)
        <li><a class="dropdown-item" href="{{ url('/admin/edit-blog',base64_encode($row->id)) }}"><i class="bi bi-pencil"></i> &nbsp;Edit</a></li>
        @else
        <li><a class="dropdown-item" href="{{ url('/admin/edit-blog',base64_encode($row->id)) }}"><i class="bi bi-eye"></i> &nbsp;View</a></li>
        @endif
        
        @php
        $blogPermit = Helper::checkPermission($adminId,6,'deletee');
        @endphp
        @if($blogPermit)
        <li><a class="dropdown-item" onclick="deleteData('blogs','{{ $row->id }}');"><i class="bi bi-trash"></i> &nbsp;Delete</a></li>
        @endif
        
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


