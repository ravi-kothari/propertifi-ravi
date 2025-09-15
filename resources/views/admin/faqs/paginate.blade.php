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
        @if($userData->type != 'Agent')
        <td>
        {!! $row->type !!}
        </td>
        @endif
        <td>
        {!! $row->question !!}
        </td>
        <td>
        {!! $row->answer !!}
        </td>
        @if($userData->type != 'Agent')
        <td>
        @php
        $faqPermit = Helper::checkPermission($adminId,7,'edit');
        @endphp
        @if($faqPermit)
         @if($row->status == 1)
            <a href="javascript:void(0);" onclick="changeStatus('faqs','{!!$row->id!!}','{!!$row->status!!}');" class="badge bg-success ">Active</a>
            @else
            <a href="javascript:void(0);"  onclick="changeStatus('faqs','{!!$row->id!!}','{!!$row->status!!}');" class="badge bg-danger">In-Active</a>
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
        $faqPermit = Helper::checkPermission($adminId,7,'edit');
        @endphp
        @if($faqPermit)

        <li><a class="dropdown-item" href="{{ url('/admin/edit-faq',base64_encode($row->id)) }}"><i class="bi bi-pencil"></i> &nbsp;Edit</a></li>
        @else
        <li><a class="dropdown-item" href="{{ url('/admin/edit-faq',base64_encode($row->id)) }}"><i class="bi bi-eye"></i> &nbsp;View</a></li>
        @endif
        @php
        $faqPermit = Helper::checkPermission($adminId,7,'deletee');
        @endphp
        @if($faqPermit)
        <li><a class="dropdown-item" onclick="deleteData('faqs','{{ $row->id }}');"><i class="bi bi-trash"></i> &nbsp;Delete</a></li>
        @endif
        
        </ul>
        </div>
        
       </td>
        @endif
        
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


