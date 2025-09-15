@if($records->count()>0)
    @foreach($records as $key => $row)
    
    <tr>
        <td>
        <div class="d-flex align-items-center">
        {!! $row->id !!}
        </div>
        </td>
        <td>{!! $row->email !!}</td>
        <td>{!! $row->created_at !!}</td>
        <td><a style="cursor:pointer" class="dropdown-item" onclick="deleteData('newsletters','{{ $row->id }}');"><i class="bi bi-trash"></i> &nbsp;Delete</a></td>
        
        
    </tr>
    <tr style="display:none" id="contact_{!! $row->id !!}">
    <td colspan="6">
    <h5>Message</h5>
    <p>{!! $row->message !!}</p>
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


