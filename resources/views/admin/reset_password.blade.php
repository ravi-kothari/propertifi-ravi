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
                <h1 class="auth-title" style="font-size:30px;text-align:center;">Change Your Password!</h1>
                <p class="auth-subtitle mb-3" style="text-align:center;font-size:19px;">Enter a new password below to change your password.</p>



                <form action="#" method="post" id="adminLoginForm">
                
                <input type="hidden" name="token" value="{{$id}}">
                <input type="hidden" name="key" value="{{$time}}">

                    <div class="form-group position-relative has-icon-left mb-4">

                        <input type="password" class="form-control form-control-xl" name="password"  id="password" placeholder="New Password">

                        <div class="form-control-icon">

                            <i class="bi bi-shield-lock"></i>

                        </div>

                    </div>
                    
                    <div class="form-group position-relative has-icon-left mb-4">

                        <input type="password" class="form-control form-control-xl" name="c_password"  id="c_password" placeholder="Confirm Password">

                        <div class="form-control-icon">

                            <i class="bi bi-shield-lock"></i>

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

let adminLoginURL = "{{url('/admin/update-new-password')}}";

$(document).ready(function(){

	$('#loginSubmit').click(function(e){
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
					if(obj['heading'] == "Success"){
						$('#password').val('');
						$('#c_password').val('');
                        Toastify({
						text: obj['msg'],
						duration: 3000,
						close: true,
						style: {background: "#093"}
						}).showToast();
						
						setTimeout(() => {window.location.href="{{route('admin.login')}}"}, "4000");
						
						return false;
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

</script>

@endsection

