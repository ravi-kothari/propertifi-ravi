@foreach($results as $key => $result)

@if($key > 0)
<hr />
@endif

<div><span><b>{{$result['question']}}</b></span></div>
<div><span>{{$result['answer']}}</span></div>



@endforeach
