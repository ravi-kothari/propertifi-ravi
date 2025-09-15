@if($records->count()>0)
    @foreach($records as $key => $row)
    
    <tr>
        <td>
        <div class="d-flex align-items-center">
        {!! $row->id !!}
        </div>
        </td>
        <td>{!! $row->company_name !!}</td>
        <td>{!! $row->name !!}</td>
        <td>{!! $row->email !!}</td>
        <td>{!! $row->contact !!}</td>
        <td>{!! $row->created_at !!}</td>
        <td><a style="cursor:pointer" onclick="$('#contact_{!! $row->id !!}').slideToggle();"><i class="bi bi-eye"></i> &nbsp;View Details</a></td>
        
        
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


