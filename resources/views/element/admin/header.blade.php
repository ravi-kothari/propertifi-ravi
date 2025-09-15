<style>
.header-sidebar{background: #fff;
position: fixed;
width: 100%;
top: 0px;
left: 303px;
padding: 23px 28px;
display: flex;
gap: 20px;
z-index: 99; transition: left 0.5s ease-out; }
.header-sidebar.active{
left:0px;
}
a.active_item {
    color: #fff !important;
}
@media(max-width:767px){
	.header-sidebar{
		left:0px;
		z-index:0;
	}
}
.wallet{position: fixed;
    right: 16px;
    top: 26px;
    font-size: 18px;
    font-weight: bold;
}
</style>
@php
$adminId = Session::get('admin_id');
$adminType = Session::get('admin_type');
$userData = Helper::GetUserData($adminId);
@endphp
<div style="height:88px"></div>
<header id="top_header" class="mb-3 header-sidebar">
    <a href="#" class="burger-btn d-block">
        <i class="bi bi-justify fs-3"></i>
    </a>
    <h3 id="page_main_title">Loading...</h3>
    @if($adminType == 'Agent')
    <span class="wallet"><i class="bi bi-wallet"></i> {{$userData->credits}} Credits</span>
    @endif
</header>
