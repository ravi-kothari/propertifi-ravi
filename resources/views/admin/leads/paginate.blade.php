@if($records->count()>0)
    @foreach($records as $key => $row)
    @php
    $category = 'N/A';
    if($row->category == 1){
    $category = 'Single Family';
    }else if($row->category == 2){
    $category = 'Multi Family';
    }else if($row->category == 3){
    $category = 'Association Property';
    }else if($row->category == 8){
    $category = 'Commercial Property';
    }
    @endphp
    <tr>
        <td>
  
        <strong>{{ucwords($row->name)}}</strong><br>
        {{strtolower($row->email)}}
    
        </td>
        <td>{{$row->unique_id}}</td>
        <td>{{$row->address}}, {{$row->city}}, {{$row->zipcode}}</td>
        <td>{{$row->phone}}</td>
        <td>{{$category}}</td>
        <td>
        @if($row->questions != '')
        <a data-bs-toggle="modal" data-bs-target="#questions" onclick="getQuestionAnswers('{{$row->id}}');" style="cursor:pointer; color:#00F"><i class="bi bi-eye"></i></a>
        @else
        N/A
        @endif
        </td>
        <td>{{$row->created_at}}</td>
        <td>
        @if($row->user_type == 'Admin')
        <div class="dropdown">
        <a class="dropdown-toggle" style="font-size:23px;" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-three-dots-vertical"></i>
        </a>
        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
        <li><a class="dropdown-item" href="{{ url('/admin/view-lead-history',base64_encode($row->id)) }}"><i class="bi bi-eye"></i> &nbsp;View Details</a></li>
        </ul>
        </div>
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


