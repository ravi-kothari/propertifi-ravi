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
        {!! $row->city !!}
        </td>
        <td>{!! $row->state_name !!}</td>
        <td>{!! $row->seo_title !!}</td>
        <td>{!! $row->seo_keywords !!}</td>
        <td>{!! $row->seo_description !!}</td>
        
        <td>
        @php
        $cityPermit = Helper::checkPermission($adminId,15,'edit');
        @endphp
        @if($cityPermit)
        @if($row->id > 1)
        @if($row->status == 1)
        <a href="javascript:void(0);" onclick="changeStatus('cities','{!!$row->id!!}','{!!$row->status!!}');" class="badge bg-success ">Active</a>
        @else
        <a href="javascript:void(0);"  onclick="changeStatus('cities','{!!$row->id!!}','{!!$row->status!!}');" class="badge bg-danger">In-Active</a>
        @endif
        @endif
        @endif
        </td>
        <td>
        
        <div class="dropdown">
        <a class="dropdown-toggle" style="font-size:23px;" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-three-dots-vertical"></i>
        </a>
        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
        @php
        $cityPermit = Helper::checkPermission($adminId,15,'edit');
        @endphp
        @if($cityPermit)
        <li><a class="dropdown-item" href="{{ url('/admin/edit-city',base64_encode($row->id)) }}"><i class="bi bi-pencil"></i> &nbsp;Edit</a></li>
        @else
        <li><a class="dropdown-item" href="{{ url('/admin/edit-city',base64_encode($row->id)) }}"><i class="bi bi-eye"></i> &nbsp;View</a></li>
        @endif
        @if($row->id > 1)
        @php
        $cityPermit = Helper::checkPermission($adminId,15,'deletee');
        @endphp
        @if($cityPermit)
        <li><a class="dropdown-item" onclick="deleteData('cities','{{ $row->id }}');"><i class="bi bi-trash"></i> &nbsp;Delete</a></li>
        @endif
        @endif
        </ul>
        </div>
        
       </td>
        
        
    </tr>

    @endforeach
        @else
        <tr>
            <td align="center" colspan="10">Record not found</td>
        </tr>
        @endif
        <tr>
            <td align="center" colspan="10">
                <div id="pagination">{!! $records->links('pagination.front') !!}</div>
            </td>
        </tr>


