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
        {!! $row->designation !!}
        </td>
        <td>
        @if($row->banner != '')
        <img src="{{URL::asset('public/img/testimonials/')}}/{!! $row->banner !!}" width="100">
        @else
        Not Available
        @endif
        </td>
        <td>{!! strlen($row->description) > 50 ? substr($row->description,0,50).'...' : $row->description !!}</td>
        
        <td>
        @php
        $testPermit = Helper::checkPermission($adminId,13,'edit');
        @endphp
        @if($testPermit)
        @if($row->status == 1)
        <a href="javascript:void(0);" onclick="changeStatus('testimonials','{!!$row->id!!}','{!!$row->status!!}');" class="badge bg-success ">Active</a>
        @else
        <a href="javascript:void(0);"  onclick="changeStatus('testimonials','{!!$row->id!!}','{!!$row->status!!}');" class="badge bg-danger">In-Active</a>
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
        $testPermit = Helper::checkPermission($adminId,13,'edit');
        @endphp
        @if($testPermit)
        <li><a class="dropdown-item" href="{{ url('/admin/edit-testimonial',base64_encode($row->id)) }}"><i class="bi bi-pencil"></i> &nbsp;Edit</a></li>
        @else
        <li><a class="dropdown-item" href="{{ url('/admin/edit-testimonial',base64_encode($row->id)) }}"><i class="bi bi-eye"></i> &nbsp;View</a></li>
        @endif
        @php
        $testPermit = Helper::checkPermission($adminId,13,'deletee');
        @endphp
        @if($testPermit)
        <li><a class="dropdown-item" onclick="deleteData('testimonials','{{ $row->id }}');"><i class="bi bi-trash"></i> &nbsp;Delete</a></li>
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


