@php
$adminId = Session::get('admin_id');
@endphp
@if($records->count()>0)
    @foreach($records as $key => $row)
    <tr>
    <td><input type="checkbox" class="checkbox" value="{{$row->id}}" /></td>
        <td>
        <div class="d-flex align-items-center">
        {!! $row->id !!}
        </div>
        </td>
        <td>
        <a href="{{ url('/admin/view-property-managers',base64_encode($row->id)) }}"><strong>{!! $row->company_name !!}</strong></a><br />
        @if($row->email_varification == 2)
        <a title="Unverified Email Address" onclick="resendVarificationEmail('{!!$row->id!!}');" style="cursor:pointer; color:#F00">{!! strtolower($row->email) !!}</a><br />
        @else
        {!! strtolower($row->email) !!}<br />
        @endif
        {{ $row->created_at }}
        </td>
        <td>{!! $row->address !!}</td>
        <td>{!! $row->mobile !!}</td>
        <td>
        <strong>{!! $row->p_contact_name !!}</strong><br />
        {!! $row->p_contact_no !!}
        </td>
        <td>{!! $row->credits !!}</td>
        <td>
        @if($row->email_varification == 2)
        <a title="Unverified Email Address" onclick="resendVarificationEmail('{!!$row->id!!}');" style="cursor:pointer; color:#F00">Pending</a>
        @else
        <span style="color:#093">Verified</span>
        @endif
        </td>
        <td>
        @php
        $propertyPermit = Helper::checkPermission($adminId,3,'edit');
        @endphp
        @if($propertyPermit)
        @if($row->status == 1)
        <a href="javascript:void(0);" onclick="changeStatus('users','{!!$row->id!!}','{!!$row->status!!}');" class="badge bg-success ">Active</a>
        @else
        <a href="javascript:void(0);"  onclick="changeStatus('users','{!!$row->id!!}','{!!$row->status!!}');" class="badge bg-danger">In-Active</a>
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
                    $propertyPermit = Helper::checkPermission($adminId,3,'view');
                    @endphp
                    @if($propertyPermit)
        <li><a class="dropdown-item" href="{{ url('/admin/view-property-managers',base64_encode($row->id)) }}"><i class="bi bi-eye"></i> &nbsp;View Details</a></li>
        @endif
        @php
                    $propertyPermit = Helper::checkPermission($adminId,3,'edit');
                    @endphp
                    @if($propertyPermit)
        <li><a class="dropdown-item" href="{{ url('/admin/edit-property-managers',base64_encode($row->id)) }}"><i class="bi bi-pencil"></i> &nbsp;Edit</a></li>
        @endif
        @php
                    $propertyPermit = Helper::checkPermission($adminId,3,'deletee');
                    @endphp
                    @if($propertyPermit)
        <li><a class="dropdown-item" onclick="deleteData('users','{{ $row->id }}');"><i class="bi bi-trash"></i> &nbsp;Delete</a></li>
        @endif
        @php
                    $propertyPermit = Helper::checkPermission($adminId,3,'addd');
                    @endphp
                    @if($propertyPermit)
        <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#staticBackdrop" onclick="$('#agent_id').val('{{$row->id}}');GetTotalCredit('{{$row->id}}');"><i class="bi bi-plus-circle"></i> &nbsp;Add Credit</a></li>
        @endif
        
        @php
        $propertyPermit = Helper::checkPermission($adminId,3,'edit');
        @endphp
        @if($propertyPermit)
        <li><a class="dropdown-item" target="_blank" href="{{ url('/admin/coverage',$row->id) }}"><i class="bi bi-arrows-move"></i> &nbsp;Coverage & Pricing</a></li>
        @endif
        
        @php
        $propertyPermit = Helper::checkPermission($adminId,3,'edit');
        @endphp
        @if($propertyPermit)
        <li><a class="dropdown-item" target="_blank" href="{{ url('/admin/zipcodes',$row->id) }}"><i class="bi bi-arrows-move"></i> &nbsp;Zipcodes</a></li>
        @endif
        
        @php
        $propertyPermit = Helper::checkPermission($adminId,3,'edit');
        @endphp
        @if($propertyPermit)
        <li><a class="dropdown-item" target="_blank" href="{{ url('/admin/users',$row->id) }}"><i class="bi bi-arrows-move"></i> &nbsp;Users</a></li>
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


