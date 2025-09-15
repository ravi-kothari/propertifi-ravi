@extends('layout.admin.login')

@section('content')
<style>
.form-group2 label {
    color: rgba(35, 28, 99, 0.7);
    font-weight: 400; margin-bottom:10px;
}
.form-group2{ margin-top:10px;}
.success-msg{
border: 1px solid #093;
background: #093;
padding: 13px;
border-radius: 6px;
color: #fff;}
</style>
<div id="auth">

    <div class="row h-100">



        <div class="col-lg-7 d-none d-lg-block">
            <div id="auth-right" style="padding:25px;">
            <!--<img src="{{ asset('public/admin/images/login-page.jpg') }}" style="height:auto; width:100%" alt="Logo">-->
            </div>
        </div>

        <div class="col-lg-5 col-12">

            <div id="auth-left">

                <div class="auth-logo" style="text-align:center">
                    <a href="https://www.propertifi.co/"><img src="{{ asset('public/admin/images/logo/p-logo.jpg') }}" style="" alt="Logo"></a>
                </div>
                <p class="auth-subtitle mb-3" style="text-align:center;font-size:19px;">Register as a Property Manager</p>

                <form action="#" method="post" id="adminLoginForm">
                <div class="row" id="success_msg_div" style="display:none">
                <div class="col-md-12">
                <div class="success-msg">We have sent the mail for your email address verification. Please check your inbox and follow the steps. </div>
                </div>
                <div class="col-md-12">
                <a href="https://www.propertifi.co/" class="btn btn-primary btn-block btn-lg shadow-lg mt-4" id="">Go To Home</a>
                </div>
                </div>
                <div id="reg_form_div">
                <div class="row">
                <div class="col-md-12">
                <div class="form-group2">
                    <label for="basicInput">Company Name</label>
                    <input type="text" class="form-control" name="company_name" id="company_name">
                    </div>
                </div>
                
                <div class="col-md-5">
                    <div class="form-group2">
                    <label for="basicInput">Email Address</label>
                    <input type="text" class="form-control" name="email" id="email">
                    </div>
                </div>
                
                <div class="col-md-3 col-5">
                    <div class="form-group2">
                    <label for="basicInput">Country Code</label>
                    <select id="country_code" name="country_code" class="form-select">
                    <option value="1">US (+1)</option>
                    <option value="91">IN (+91)</option>
                    </select>
                    </div>
                </div>
                
                <div class="col-md-4 col-7">
                    <div class="form-group2">
                    <label for="basicInput">Mobile Number</label>
                    <input type="text" class="form-control" maxlength="10" name="contact" id="contact">
                    </div>
                </div>
                
                <div class="col-md-6 col-6">
                    <div class="form-group2">
                    <label for="basicInput">Portfolio Type</label>
                    <select id="portfolio_type" name="portfolio_type" class="form-select">
                    <option value="">Select Portfolio Type</option>
                    <option value="Rentals">Rentals</option>
                    <option value="Association">Association</option>
                    <option value="Commercial">Commercial</option>
                    <option value="Mixed">Mixed</option>
                    </select>
                    </div>
                </div>
                
                <div class="col-md-6 col-6">
                    <div class="form-group2">
                    <label for="basicInput">Units Under Management</label>
                    <input type="text" class="form-control" name="units" id="units">
                    </div>
                </div>
                
                <div class="col-md-12">
                    <div class="form-group2">
                    <label for="basicInput">Address</label>
                    <input type="text" class="form-control" name="address" id="address">
                    </div>
                </div>
                
                <div class="col-md-4 col-4">
                    <div class="form-group2">
                    <label for="basicInput">State</label>
                    <select id="state" name="state" onchange="getCities(this.value)" class="form-select">
                    <option value="">Select</option>
                    @foreach($states as $key => $state)
                    <option value="{{$state->id}}">{{$state->state}}</option>
                    @endforeach
                    </select>
                    </div>
                </div>
                
                <div class="col-md-4 col-4">
                    <div class="form-group2">
                    <label for="basicInput">City</label>
                    <select id="city" name="city" class="form-select">
                    <option value="">Select</option>
                    </select>
                    </div>
                </div>
                
                <div class="col-md-4 col-4">
                    <div class="form-group2">
                    <label for="basicInput">ZIP Code</label>
                    <input type="text" maxlength="5" class="form-control" name="zipcode" id="zipcode">
                    </div>
                </div>
                
                <?php /*?><div class="col-md-12">
                    <div class="form-group2">
                    <label for="basicInput">Password</label>
                    <input type="password" class="form-control" name="password" id="password">
                    </div>
                </div><?php */?>
                
                </div>
                    
                <hr />
                <div class="row">
                    <h5>Point of Contact</h5>
                    <div class="col-md-4">
                        <div class="form-group2">
                        <label for="basicInput">Name</label>
                        <input type="text" class="form-control" name="p_contact_name" id="p_contact_name">
                        </div>
                    </div>
                    <div class="col-md-4 col-6">
                        <div class="form-group2">
                        <label for="basicInput">Contact No.</label>
                        <input type="text" maxlength="10" class="form-control" name="p_contact_no" id="p_contact_no">
                        </div>
                    </div>
                    <div class="col-md-4 col-6">
                        <div class="form-group2">
                        <label for="basicInput">Email Address</label>
                        <input type="text" class="form-control" name="p_contact_email" id="p_contact_email">
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-primary btn-block btn-lg shadow-lg mt-4" id="loginSubmit">Submit</button>

				<p style="margin-top:22px;font-size:16px; text-align:center"><a href="{{route('admin.login')}}"> Login by Username and Password?</a></p>
                </div>
                </form>

            </div>

        </div>

    </div>

</div>



<script type="text/javascript">

let adminLoginURL = "{{route('admin.registerAgent')}}";

let dashboardURL = "{{url('/admin/dashboard')}}";

$(document).ready(function(){

	$('#loginSubmit').click(function(e){

            $('#loginSubmit').html('Processing...');
			$('#loginSubmit').prop('disabled',true);

			$.ajax({
				type: 'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
				url: adminLoginURL,
				data: $('#adminLoginForm').serialize(),
				beforeSend:function(){$('#loginSubmit').removeClass('login-btn').addClass('login-btn-processing').val('Processing...'); },
				success: function(msg){
					$('#loginSubmit').prop('disabled',false);
                    var obj = JSON.parse(msg);
                    $('#loginSubmit').html('Submit');
					if(obj['heading'] == "Success"){
                        $('#reg_form_div').remove();
						$('#success_msg_div').show();
					}else{
						Toastify({
						text: obj['msg'],
						duration: 3000,
						close: true,
						style: {background: "#f00"}
						}).showToast();
						return false;
					}

				},error: function(ts) {
					Toastify({
						text: "Some thing want to wrong, please try after sometime.",
						duration: 3000,
						close: true,
						style: {background: "#f00"}
						}).showToast();
						return false;
				}

			});

		

	});

});

function showMessage(msg){

Toastify({
text: msg,
duration: 3000,
close: true,
style: {background: "#f00"}
}).showToast();
return false;

}
function getCities(stateID){
	if(stateID > 0){
		$.ajax({
			type: 'POST',
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			url: "{{route('admin.get-cities')}}",
			data: {stateID:stateID},
			success: function(msg){
				$('#city').html(msg);
			},error: function(ts) {
				
			}
		});
	}
}
</script>

@endsection

