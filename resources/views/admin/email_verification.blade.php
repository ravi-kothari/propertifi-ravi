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
                <p class="auth-subtitle mb-3" style="text-align:center;font-size:19px;">Email verification</p>

                <form action="#" method="post" id="adminLoginForm">
                <div class="row" id="success_msg_div">
                <div class="col-md-12">
                <div class="success-msg">Your email verification successfully done. Our team is reviewing your submitted details. You will receive another email as soon as your account is fully active.</div>
                </div>
                <div class="col-md-12">
                <a href="https://www.propertifi.co/" class="btn btn-primary btn-block btn-lg shadow-lg mt-4" id="">Go To Home</a>
                </div>
                </div>
                
                </form>

            </div>

        </div>

    </div>

</div>

@endsection

