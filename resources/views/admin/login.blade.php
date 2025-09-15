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

                    <div class="form-group position-relative has-icon-left mb-4">

                        <input type="text" class="form-control form-control-xl" value="{{$cookieUsername}}" name="email"  id="email" placeholder="Username">

                        <div class="form-control-icon">

                            <i class="bi bi-person"></i>

                        </div>

                    </div>

                    <div class="form-group position-relative has-icon-left mb-4">

                        <input value="{{$cookiePassword}}" type="password" name="password" id="password" class="form-control form-control-xl" placeholder="Password">

                        <div class="form-control-icon">

                            <i class="bi bi-shield-lock"></i>

                        </div>

                    </div>

                    <div class="form-check form-check-lg d-flex align-items-end">

                        <input class="form-check-input me-2" type="checkbox" id="flexCheckDefault" checked value="1">

                        <label class="form-check-label text-gray-600" for="flexCheckDefault">

                            Keep me logged in

                        </label>

                    </div>

                    <button type="button" class="btn btn-primary btn-block btn-lg shadow-lg mt-4" id="loginSubmit">Log in</button>

<p style="margin-top:22px;font-size:16px; text-align:center"><a href="{{route('admin.forgotPassword')}}"> Forgot Your Password?</a></p>

<p style="margin-top:22px;font-size:16px; text-align:center"><a href="{{route('admin.login.user')}}"> Login by One Time Password?</a></p>

<!--<p style="margin-top:22px;font-size:16px; text-align:center"><a href="{{route('admin.create_agent_account')}}" style="color:#03F"> Don't have an Agent account yet? Request one here</a></p>-->
                </form>

                <div class="text-center mt-5 text-lg fs-4">

                    <p><a class="font-bold"  href="{{route('admin.create_agent_account')}}" style="color:#03F">Don't have an Agent account yet? Request one here</a>.</p>

                </div>

            </div>

        </div>

    </div>

</div>



<script type="text/javascript">

let adminLoginURL = "{{url('/admin/admin-login-by-username')}}";

let dashboardURL = "{{url('/admin/dashboard')}}";

$(document).ready(function(){

	$("#email, #password").on('keyup', function (e) {

		if (e.keyCode == 13) {

			$('#loginSubmit').trigger('click');

		}

	});
	$('#loginSubmit').click(function(e){

		var flag = 0;

		if($.trim($("#email").val()) == ''){

			flag = 1;

            showMessage('Please Enter Account Username.');

			return false;

		}

		if($.trim($("#password").val()) == ''){

			flag = 1;

            showMessage('Please Enter Account Password.');

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

                    var obj = JSON.parse(msg);

                    $('#loginSubmit').html('Log In');

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

});

function showMessage(msg){

    swal("Error!", msg, "error");

}

</script>

@endsection

