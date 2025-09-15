@extends('layout.admin.dashboard')

@section('content')

<div class="page-heading">
        
        <section class="section">
            <div class="card">
                
                <div class="card-body">
                <form class="form w-100" id="updateForm" action="#">
                <input type="hidden" name="old_file" id="old_file" value="{!! $setting->logo !!}" />
                    <div class="row">
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="basicInput">Company Name</label>
                                <input type="text" class="form-control" value="{!! $setting->company_name !!}" id="company_name" name="company_name">
                            </div>
                        </div>                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="basicInput">Company Contact Number</label>
                                <input type="text" class="form-control" maxlength="10" value="{!! $setting->mobile !!}" id="mobile" name="mobile">
                            </div>
                        </div>                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="basicInput">Company Contact Email</label>
                                <input type="text" class="form-control" value="{!! $setting->email !!}" id="email" name="email">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="basicInput">Business Address</label>
                                 <input type="text" class="form-control" value="{!! $setting->business_address !!}" id="business_address" name="business_address">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="basicInput">Footer Content</label>
                                <input type="text" class="form-control" value="{!! $setting->footer_content !!}" id="footer_content" name="footer_content">
                            </div>
                        </div>
                        <!--<div class="col-md-4">
                            <div class="form-group">
                                <label for="basicInput">Per Credit Amount ($)</label>
                                <input type="text" class="form-control" value="{!! $setting->per_credit_amt !!}" id="per_credit_amt" name="per_credit_amt">
                            </div>
                        </div>-->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="basicInput">Company Logo</label>
                                <input class="form-control" type="file" name="file" id="file" autocomplete="off" accept="image/png, image/jpg" />
                            </div>
                        </div>
                        @if(!empty($setting->logo))
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="basicInput">Company Logo</label>
                                <div class="col-lg-6 fv-row fv-plugins-icon-container">
                                    <div class="cropped" id="cropped">
                                        <div class="cropped" id="cropped"><img src="{{URL::asset('public/admin/images/profile/')}}/{!! $setting->logo !!}" width="150"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        
						
                        <div class="text-left">
                            <!--begin::Submit button-->
                            <button type="button" id="profile_submit" class="btn btn-sm btn-primary fw-bolder me-3 my-2">
                                <span class="indicator-label" id="formSubmit">Submit</span>
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
<script>
$(document).ready(function(){
	$('#page_main_title').html('Settings');
});
</script>
<script>
    let saveDataURL = "{{url('/admin/save-setting')}}";
</script>
<script src="{{ asset('public/admin/js/pages/update-settings.js') }}"></script>


@endsection


