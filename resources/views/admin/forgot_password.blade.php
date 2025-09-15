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
                <h1 class="auth-title" style="font-size:30px;text-align:center;">Password Reset!</h1>
                <p class="auth-subtitle mb-3" style="text-align:center;font-size:19px;">You will receive instructions for reseting your password.</p>



                <form action="#" method="post" id="adminLoginForm">

                    <div class="form-group position-relative has-icon-left mb-4">

                        <input type="text" class="form-control form-control-xl" name="email"  id="email" placeholder="Email Address">

                        <div class="form-control-icon">

                            <i class="bi bi-person"></i>

                        </div>

                    </div>

                    <button type="button" class="btn btn-primary btn-block btn-lg shadow-lg mt-4" id="loginSubmit">Submit</button>

<p style="margin-top:22px;font-size:16px; text-align:center"><a href="{{route('admin.login')}}"> Login through username and password?</a></p>
                </form>

                

            </div>

        </div>

    </div>

</div>



<script type="text/javascript">

let adminLoginURL = "{{url('/admin/forgot-password')}}";

$(document).ready(function(){

	$('#loginSubmit').click(function(e){

		var flag = 0;

		if($.trim($("#email").val()) == ''){

			flag = 1;

            showMessage('Please Enter Email.');

			return false;

		}

		if(flag == 0){

            $('#loginSubmit').html('Processing...');

			$.ajax({

				type: 'POST',

                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},

				url: adminLoginURL,

				data: $('#adminLoginForm').serialize(),

				beforeSend:function(){$('#loginSubmit').removeClass('login-btn').addClass('login-btn-processing').val('Processing...'); },

				success: function(msg){
					$('#loginSubmit').html('Submit');
					var obj = JSON.parse(msg);
                    $('#otpSubmit').html('Log In');
					if(obj['heading'] == "Success"){
                        
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

Toastify({
text: msg,
duration: 3000,
close: true,
style: {background: "#f00"}
}).showToast();
return false;

}

</script>

@endsection

