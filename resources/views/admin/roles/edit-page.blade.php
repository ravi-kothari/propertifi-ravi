@extends('layout.admin.dashboard')

@section('content')

<div class="page-heading">
        <div class="page-title">
            <div class="row">
                
                <div class="col-12 col-md-12 order-md-2">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start">
                        <ol class="breadcrumb">
                             <li class="breadcrumb-item"><a href="{{url('/admin')}}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ url('/admin/roles'); }}">User Roles</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit User Role</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="card">
                
                <div class="card-body">
                <form class="form w-100" id="pageForm" action="#">
                    <div class="row">
                    
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="basicInput">Title</label>
                            <input type="text" class="form-control" name="title" value="{{$rowData->title}}" id="title">
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>#ID</th>
                                            <th>Manager</th>
                                            <th>Read Only</th>
                                            <th>Add</th>
                                            <th>Edit</th>
                                            <th>Delete</th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody id="replaceHtml">
                                    	@foreach($managers as $key => $manager)
                                        @php
                                        $addArray = explode(',',$rowData->addd);
                                        $editArray = explode(',',$rowData->edit);
                                        $deleteArray = explode(',',$rowData->deletee);
                                        $viewArray = explode(',',$rowData->view);
                                        @endphp
                                        <tr>
                                            <td>{{$manager->id}}</td>
                                            <td>{{$manager->title}}</td>
                                            <td><input type="checkbox" name="view[]" @if(in_array($manager->id,$viewArray)) checked @endif value="{{$manager->id}}" /></td>
                                            <td><input type="checkbox" name="add[]" @if(in_array($manager->id,$addArray)) checked @endif value="{{$manager->id}}" /></td>
                                            <td><input type="checkbox" name="edit[]" @if(in_array($manager->id,$editArray)) checked @endif value="{{$manager->id}}" /></td>
                                            <td><input type="checkbox" name="delete[]" @if(in_array($manager->id,$deleteArray)) checked @endif value="{{$manager->id}}" /></td>
                                            
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                    </div>
                        
                       
                        
                       
                        <div class="text-left">
                            <!--begin::Submit button-->
                            <button type="button" id="form_submit" class="btn btn-sm btn-primary fw-bolder me-3 my-2">
                                <span class="indicator-label" id="formSubmit">Update</span>
                                <span class="indicator-progress d-none">Please wait...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                            <!--end::Submit button-->
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
<!-- end plugin js -->
<script>
    let saveDataURL = "{{url('/admin/edit-role/'.$row_id)}}";
    let returnURL = "{{url('/admin/edit-role/'.$row_id)}}";
</script>
<script src="{{ asset('public/admin/js/pages/roles/add-page.js') }}"></script>
<script>
$(document).ready(function(){
	$('#page_main_title').html('Edit User Role');
});
</script>

@endsection


