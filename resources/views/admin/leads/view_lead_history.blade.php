@extends('layout.admin.dashboard')

@section('content')

<div class="page-heading">
        <div class="page-title">
            <div class="row">
                
                <div class="col-12 col-md-12 order-md-2">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{url('/admin')}}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ url('/admin/leads'); }}">Lead History</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Lead ID: {{$rowData->unique_id}}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        
        <!-- Table head options start -->
        <section class="section">
        <div class="card">
        <div class="card-body">
        <div class="row">
        
        <div class="col-md-10">
        <h4>{{ucwords($rowData->name)}}</h4>
       {{strtolower($rowData->email)}}<br />
        {{$rowData->phone}}
        </div>
        
        <div class="col-md-2 text-right">
        <a class="btn btn-sm btn-success fw-bolder me-3 my-2">Lead ID: {{$rowData->unique_id}}</a>
        </div>
        </div>
        </div>
        </div>
        </section>
        <!-- Table head options end -->
        @php
        $category = 'N/A';
        if($rowData->category == 1){
        $category = 'Single Family';
        }else if($rowData->category == 2){
        $category = 'Multi Family';
        }else if($rowData->category == 3){
        $category = 'Association Property';
        }else if($rowData->category == 8){
        $category = 'Commercial Property';
        }
        @endphp
        <section class="section">
        <div class="card">
        <div class="card-body">
        <div class="row">
        
        <div class="col-md-3">
        <strong>Property Address</strong><br /><br />
        {{$rowData->address}}
        </div>
        <div class="col-md-2">
        <strong>Property Type</strong><br /><br />
        {{$category}}
        </div>
        <div class="col-md-2">
        <strong>Property Value Range</strong><br /><br />
        {{$rowData->price_range}}
        </div>
        <div class="col-md-2">
        <strong>Name Your Price</strong><br /><br />
        ${{$rowData->price}}
        </div>
        <div class="col-md-2">
        <strong>Date</strong><br /><br />
        {{$rowData->created_at}}
        </div>
        
        <div class="col-md-1">
        <strong>Other Details</strong><br /><br />
        @if($rowData->questions != '')
        <a data-bs-toggle="modal" data-bs-target="#questions" onclick="getQuestionAnswers('{{$rowData->id}}');" style="cursor:pointer; color:#00F"><i class="bi bi-eye"></i></a>
        @endif
        </div>
        
        </div>
        </div>
        </div>
        </section>
        
        <section class="section">
        <div class="">
        <div class="card-body">
        <div class="row">
        
        <div class="col-md-4"></div>
        <div class="col-md-2"></div>
        <div class="col-md-2"></div>
        <div class="col-md-2"></div>
        <div class="col-md-2"><input id="title" name="title" confirmation="false" class="form-control" placeholder="Search By Company Name"></div>
        
        </div>
        </div>
        </div>
        </section>
        
        <section class="section">
            <div class="row" id="table-head">
                <div class="col-12">
                    <div class="card">

                        <div class="card-content">
                            <!-- table head dark -->
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Company Name</th>
                                            <th>Email ID</th>
                                            <th>Address</th>
                                            <th>Contact No.</th>
                                            <th>Del Speed Pre</th>
                                            <th>Location Match</th>
                                            <th>Category Match</th>
                                            <th>Distribution Fairness</th>
                                            <th>Tier Preference</th>
                                            <th>Total Score</th>
                                        </tr>
                                    </thead>
                                    <tbody id="replaceHtml">
                                    @foreach($users as $key => $user)
                                    <tr>
                                    <td>{{$user->company_name}}</td>
                                    <td>{{$user->email}}</td>
                                    <td>{{$user->address}}</td>
                                    <td>{{$user->mobile}}</td>
                                    <td>{{$user->delivery_speed_preference}}</td>
                                    <td>{{$user->location_match}}</td>
                                    <td>{{$user->category_match}}</td>
                                    <td>{{$user->distribution_fairness}}</td>
                                    <td>{{$user->tier_preference}}</td>
                                    <td>{{$user->total_score}}</td>
                                    </tr>
                                    @endforeach
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <div class="modal fade" id="questions" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog" style="max-width: 678px;">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="staticBackdropLabel">Question/Answers</h5>
          </div>
          <div class="modal-body">
            <div class="row">
            <div class="col-md-12">
                <div class="form-group" id="question_answer_data">
                
                Loading...
                
                </div>
            </div>
            
            
            </div>
          </div>
          <div style="position:relative" class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
        
    </div>
     <!-- Modal -->
    
<script>
$(document).ready(function(){
	$('#page_main_title').html('Lead Distribution');
});

function getQuestionAnswers(leadID){
	$('#question_answer_data').html('Loading...');
	$.ajax({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type: 'POST',
		data:{leadID:leadID},
		url: "{{ url('/admin/lead/question-answer') }}",
		success: function(response){
			$('#question_answer_data').html(response);
		}
	});
}

</script>

@endsection


