@extends('layout.admin.login')
@section('content')
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
                <h1 class="auth-title" style="font-size:30px;text-align:center;">Welcome back!</h1>
                <p class="auth-subtitle mb-3" style="text-align:center;font-size:19px;">Log in to Your Account</p>

                <form action="#" method="post" id="adminLoginForm">
                	<input type="hidden" id="u_id" name="u_id" />
                    <div id="mobileDiv">
                    <div class="form-group position-relative has-icon-left mb-4">
                        <input type="text" class="form-control form-control-xl" name="mobile"  id="mobile" placeholder="Enter Mobile or Email">
                        <div class="form-control-icon">
                            <i class="bi bi-phone"></i>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary btn-block btn-lg shadow-lg mt-5" id="mobileSubmit">Log in</button>
                    <p style="margin-top:22px;font-size:16px; text-align:center; color:#03F"><a href="{{route('admin.login')}}" style="color:#03F"> Login by Username and Password?</a></p>
                    </div>

                    <div id="otpDiv" style="display:none;">
                    <div class="form-group position-relative has-icon-left mb-4">
                        <input type="text" name="otp" maxlength="4" id="otp" class="form-control form-control-xl" placeholder="OTP">
                        <div class="form-control-icon">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                    </div>                     
                    <button type="button" class="btn btn-primary btn-block btn-lg shadow-lg mt-4" id="otpSubmit">Log in</button>
                    <p style="margin-top:22px;font-size:19px;"><a href="#" target="_blank">Resend OTP?</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
let adminLoginURL = "{{url('/admin/admin-login')}}";
let adminOtpVerifyURL = "{{url('/admin/admin-otp-login')}}";
let dashboardURL = "{{url('/admin/dashboard')}}";

$(document).ready(function(){

    $('#otpSubmit').click(function(e){
        var flag = 0;
        let mobile = $.trim($("#mobile").val());
        if($.trim($("#otp").val()) == ''){
			flag = 1;
            showMessage('Please Enter OTP.');
			return false;
		}
        if(flag == 0){
            $('#otpSubmit').html('Processing...');
            $.ajax({
				type: 'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
				url: adminOtpVerifyURL,
				data: $('#adminLoginForm').serialize(),
				beforeSend:function(){$('#otpSubmit').removeClass('login-btn').addClass('login-btn-processing').val('Processing...'); },
				success: function(msg){
                    var obj = JSON.parse(msg);
                    $('#otpSubmit').html('Log In');
					if(obj['heading'] == "Success"){
                        window.location.assign(dashboardURL);
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
        }
    });

	$('#mobileSubmit').click(function(e){
		var flag = 0;
        let length = $.trim($("#mobile").val()).length;
		if($.trim($("#mobile").val()) == ''){
			flag = 1;
            showMessage('Please Enter Mobile or Email.');
			return false;
		}
		if(flag == 0){
            $('#mobileSubmit').html('Processing...');
			$.ajax({
				type: 'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
				url: adminLoginURL,
				data: $('#adminLoginForm').serialize(),
				beforeSend:function(){$('#mobileSubmit').removeClass('login-btn').addClass('login-btn-processing').val('Processing...'); },
				success: function(msg){
                    var obj = JSON.parse(msg);
                    $('#mobileSubmit').html('Log In');
					if(obj['heading'] == "Success"){
                        $('#otpDiv').show();
                        $('#mobileDiv').hide();
						$('#u_id').val(obj['msg']);
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
		}
	});
});
function showMessage(msg){
    swal("Error!", msg, "error");
}
</script>
@endsection