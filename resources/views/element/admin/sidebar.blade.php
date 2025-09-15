@php
$action =  Route::getCurrentRoute()->getName();
$adminType = Session::get('admin_type');
$adminId = Session::get('admin_id');
@endphp
<div id="sidebar" class="active">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header">
            <div class="d-flex justify-content-between">
                <div class="logo">
                    <a href="{{ url('/admin/dashboard'); }}"><img src="{{ asset('public/admin/images/logo/p-logo.jpg') }}"  /></a>
                </div>
                <div class="toggler">
                    <a href="#" class="sidebar-hide d-xl-none d-block"><i style="color:#fff" class="bi bi-x bi-middle"></i></a>
                </div>
            </div>
        </div>
        <div class="sidebar-menu">
            <ul class="menu">
                <li class="sidebar-item {{$action =='admin.dashboard' ?'active':''}}">
                    <a href="{{ url('/admin/dashboard'); }}" class='sidebar-link'>
                        <i class="bi bi-grid-fill"></i>
                        <span>{{Session::get('admin_type')}} Dashboard</span>
                    </a>
                </li>
                @if($adminType == 'Admin' || $adminType == 'AccountManager')
                <li class="sidebar-item  has-sub {{$action =='admin.update-profile' || $action =='admin.change-password'  ?'active':''}}">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-person-fill"></i>
                        <span>My Profile</span>
                    </a>
                    <ul class="submenu {{$action =='admin.update-profile' || $action =='admin.change-password'  ?'active':''}}">
                    	@if($adminType == 'Admin' || $adminType == 'AccountManager')
                        <li class="submenu-item {{$action =='admin.update-profile' ?'active':''}}">
                            <a href="{{ url('/admin/update-profile'); }}">Update Profile</a>
                        </li>
                        <li class="submenu-item {{$action =='admin.change-password' ?'active':''}} ">
                            <a href="{{ url('/admin/change-password'); }}">Change Password</a>
                        </li>
                        @endif
                        @if($adminType == 'Admin')
                        @php
                        $settingsPermit = Helper::checkPermission($adminId,1,'view');
                        @endphp
                        @if($settingsPermit)
                        <li class="submenu-item {{$action =='admin.settings' ?'active':''}} ">
                            <a href="{{ url('/admin/settings'); }}">Settings</a>
                        </li>
                        @endif
                        @endif
                    </ul>
                </li>
                @endif
                <!--@if($adminType == 'Admin')
                @php
                $settingsPermit = Helper::checkPermission($adminId,1,'view');
                @endphp
                @if($settingsPermit)
                <li class="sidebar-item {{$action =='admin.settings'  ?'active':''}}">
                    <a href="{{ url('/admin/settings'); }}" class='sidebar-link'>
                        <i class="bi bi-gear-fill"></i>
                        <span>Settings</span>
                    </a>
                    
                </li>
                @endif
                @endif-->
                
               <!-- @if($adminType == 'Admin' || $adminType == 'AccountManager')
                <li class="sidebar-item {{$action =='admin.update-profile' || $action =='admin.change-password'  ?'active':''}}">
                    <a href="{{ url('/admin/update-profile'); }}" class='sidebar-link'>
                        <i class="bi bi-person-fill"></i>
                        <span>My Profile</span>
                    </a>
                    
                </li>
                @endif-->
                
                @if($adminType == 'Admin')
                <li class="sidebar-item {{$action =='admin.account-managers' || $action =='admin.add-account-managers' || $action =='admin.edit-account-managers'  ?'active':''}}">
                    <a href="{{ url('/admin/account-managers'); }}" class='sidebar-link'>
                        <i class="bi bi-person-fill"></i>
                        <span>Account Managers</span>
                    </a>
                    
                </li>
                @endif
                
                @if($adminType == 'Agent')
                <li class="sidebar-item {{$action =='admin.users' || $action =='admin.add-user' || $action =='admin.edit-user'  ?'active':''}}">
                    <a href="{{ url('/admin/users'); }}/0" class='sidebar-link'>
                        <i class="bi bi-person-fill"></i>
                        <span>Users</span>
                    </a>
                    
                </li>
                @endif
                
                @if($adminType == 'Agent')
                <li class="sidebar-item {{$action =='admin.update-profile' || $action =='admin.edit-agent-profile'  ?'active':''}}">
                    <a href="{{ url('/admin/agent-profile'); }}" class='sidebar-link'>
                        <i class="bi bi-person-fill"></i>
                        <span>My Profile</span>
                    </a>
                    
                </li>
                @endif
                @php
                $leadPermit = Helper::checkPermission($adminId,2,'view');
                @endphp
                @if($leadPermit)
                <li class="sidebar-item {{$action =='admin.leads' || $action =='admin.leads_paginate' || $action =='admin.view-lead-history'  ?'active':''}}">
                    <a href="{{ url('/admin/leads'); }}" class='sidebar-link'>
                        <i class="bi bi-people-fill"></i>
                        <span>Lead History</span>
                    </a>
                    
                </li>
                @endif
                
                @if($adminType == 'Admin' || $adminType == 'AccountManager')
                @php
                $propertyManagerPermit = Helper::checkPermission($adminId,3,'view');
                @endphp
                @if($propertyManagerPermit)
                 <li class="sidebar-item {{$action =='admin.property-managers' || $action =='admin.add-property-managers' || $action =='admin.edit-property-managers'  ?'active':''}}">
                    <a href="{{ url('/admin/property-managers'); }}" class='sidebar-link'>
                        <i class="bi bi-person-fill"></i>
                        <span>Property Managers</span>
                    </a>
                    
                </li>
                @endif
                @endif
                
                @if($adminType == 'Agent')
                <li class="sidebar-item  has-sub {{$action =='admin.coverage' || $action =='admin.zipcodes'  ?'active':''}}">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-gear-fill"></i>
                        <span>Preferences</span>
                    </a>
                    <ul class="submenu {{$action =='admin.coverage' || $action =='admin.zipcodes'  ?'active':''}}">
                        <li class="submenu-item {{$action =='admin.coverage' ?'active':''}}">
                            <a class="{{$action =='admin.coverage' ?'active_item':''}}" href="{{ url('/admin/coverage'); }}/0">Coverage & Pricing</a>
                        </li>
                        <li class="submenu-item {{$action =='admin.zipcodes' ?'active':''}}">
                            <a class="{{$action =='admin.zipcodes' ?'active_item':''}}" href="{{ url('/admin/zipcodes'); }}/0">Zipcodes</a>
                        </li>
                    </ul>
                </li>
                @endif
                
                @if($adminType == 'Agent')
                <li class="sidebar-item {{$action =='admin.faqs' || $action =='admin.add-faq' || $action =='admin.edit-faq'  ?'active':''}}">
                    <a href="{{ url('/admin/faqs'); }}" class='sidebar-link'>
                        <i class="bi bi-square-half"></i>
                        <span>FAQ</span>
                    </a>
                    
                </li>
                @endif
                
                @php
                $paymentsPermit = Helper::checkPermission($adminId,4,'view');
                @endphp
                @if($paymentsPermit)
                <li class="sidebar-item {{$action =='admin.payments'  ?'active':''}}">
                    <a href="{{ url('/admin/payments'); }}" class='sidebar-link'>
                        <i class="bi bi-credit-card-fill"></i>
                        <span>Payments</span>
                    </a>
                    
                </li>
                @endif
                @if($adminType == 'Admin' || $adminType == 'AccountManager')
                @php
                $coveragePermit = Helper::checkPermission($adminId,5,'view');
                @endphp
                @if($coveragePermit)
                <li class="sidebar-item {{$action =='admin.pricings'  ?'active':''}}">
                    <a href="{{ url('/admin/pricings'); }}" class='sidebar-link'>
                        <i class="bi bi-square-half"></i>
                        <span>Coverage & Pricing</span>
                    </a>
                    
                </li>
                @endif
                @endif
                
                @if($adminType == 'Admin' || $adminType == 'AccountManager')
                <li class="sidebar-item  has-sub {{$action =='admin.blogs' || $action =='admin.add-blog' || $action =='admin.edit-blog' || $action =='admin.testimonials' || $action =='admin.add-testimonial' || $action =='admin.edit-testimonial' || $action =='admin.faqs' || $action =='admin.add-faq' || $action =='admin.edit-faq' || $action =='admin.inner-pages' || $action =='admin.edit-inner-page' || $action =='admin.tiers' || $action =='admin.edit-tier' || $action =='admin.add-tier' || $action =='admin.questions' || $action =='admin.edit-question' || $action =='admin.add-question' ?'active':''}}">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-file-earmark-fill"></i>
                        <span>CMS</span>
                    </a>
                    <ul class="submenu {{$action =='admin.blogs' || $action =='admin.add-blog' || $action =='admin.edit-blog' || $action =='admin.testimonials' || $action =='admin.add-testimonial' || $action =='admin.edit-testimonial' || $action =='admin.faqs' || $action =='admin.add-faq' || $action =='admin.edit-faq' || $action =='admin.inner-pages' || $action =='admin.edit-inner-page' || $action =='admin.tiers' || $action =='admin.edit-tier' || $action =='admin.add-tier' || $action =='admin.questions' || $action =='admin.edit-question' || $action =='admin.add-question' || $action =='admin.cities' || $action =='admin.edit-city' || $action =='admin.add-city'  ?'active':''}}">
                    	@php
                        $cityPermit = Helper::checkPermission($adminId,15,'view');
                        @endphp
                        @if($cityPermit)
                        <li class="submenu-item">
                            <a class="{{$action =='admin.cities' || $action =='admin.add-city' || $action =='admin.edit-city' ?'active_item':''}}" href="{{ url('/admin/cities'); }}">Cities</a>
                        </li>
                        @endif
                        @php
                        $blogPermit = Helper::checkPermission($adminId,6,'view');
                        @endphp
                        @if($blogPermit)
                        <li class="submenu-item">
                            <a class="{{$action =='admin.blog.categories' || $action =='admin.add-blog-category' || $action =='admin.edit-blog-category' ?'active_item':''}}" href="{{ url('/admin/blog-categories'); }}">Blog Category</a>
                        </li>
                        @endif
                        @php
                        $blogPermit = Helper::checkPermission($adminId,6,'view');
                        @endphp
                        @if($blogPermit)
                        <li class="submenu-item">
                            <a class="{{$action =='admin.blogs' || $action =='admin.add-blog' || $action =='admin.edit-blog' ?'active_item':''}}" href="{{ url('/admin/blogs'); }}">Blogs</a>
                        </li>
                        @endif
                        @php
                        $testimonialPermit = Helper::checkPermission($adminId,13,'view');
                        @endphp
                        @if($testimonialPermit)
                        <li class="submenu-item">
                            <a class="{{$action =='admin.testimonials' || $action =='admin.add-testimonial' || $action =='admin.edit-testimonial' ?'active_item':''}}" href="{{ url('/admin/testimonials'); }}">Testimonials</a>
                        </li>
                        @endif
                        @php
                        $faqPermit = Helper::checkPermission($adminId,7,'view');
                        @endphp
                        @if($faqPermit)
                        <li class="submenu-item">
                            <a class="{{$action =='admin.faqs' || $action =='admin.add-faq' || $action =='admin.edit-faq' ?'active_item':''}}" href="{{ url('/admin/faqs'); }}">Faqs</a>
                        </li>
                        @endif
                        @php
                        $innerPagePermit = Helper::checkPermission($adminId,8,'view');
                        @endphp
                        @if($innerPagePermit)
                        <li class="submenu-item">
                            <a class="{{$action =='admin.inner-pages' || $action =='admin.edit-inner-page' ?'active_item':''}}" href="{{ url('/admin/inner-pages'); }}">Inner Pages</a>
                        </li>
                        @endif
                        @php
                        $tierPermit = Helper::checkPermission($adminId,9,'view');
                        @endphp
                        @if($tierPermit)
                        <li class="submenu-item">
                            <a class="{{$action =='admin.tiers' || $action =='admin.edit-tier' || $action =='admin.add-tier' ?'active_item':''}}" href="{{ url('/admin/tiers'); }}">Tiers</a>
                        </li>
                        @endif
                        @php
                        $questionPermit = Helper::checkPermission($adminId,10,'view');
                        @endphp
                        @if($questionPermit)
                        <li class="submenu-item">
                            <a class="{{$action =='admin.questions' || $action =='admin.edit-question' || $action =='admin.add-question' ?'active_item':''}}" href="{{ url('/admin/questions'); }}">Questions</a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif
                
               @php
                $contactusPermit = Helper::checkPermission($adminId,11,'view');
                @endphp
                @if($contactusPermit)
                <li class="sidebar-item {{$action =='admin.contacts' || $action =='admin.contacts_paginate'  ?'active':''}}">
                    <a href="{{ url('/admin/contacts'); }}" class='sidebar-link'>
                        <i class="bi bi-square-half"></i>
                        <span>Inquiries</span>
                    </a>
                    
                </li>
                @endif
                
                @if($adminType == 'Admin' || $adminType == 'AccountManager')
                @php
                $newsletterPermit = Helper::checkPermission($adminId,12,'view');
                @endphp
                @if($newsletterPermit)
                <li class="sidebar-item {{$action =='admin.newsletters' || $action =='admin.newsletters_paginate'  ?'active':''}}">
                    <a href="{{ url('/admin/newsletters'); }}" class='sidebar-link'>
                        <i class="bi bi-square-half"></i>
                        <span>Newsletters</span>
                    </a>
                </li>
                @endif
                @endif
                
                @if($adminType == 'Admin')
                <li class="sidebar-item {{$action =='admin.roles' || $action =='admin.roles_paginate'  ?'active':''}}">
                    <a href="{{ url('/admin/roles'); }}" class='sidebar-link'>
                        <i class="bi bi-square-half"></i>
                        <span>User Roles</span>
                    </a>
                    
                </li>
                @endif
                
                @if($adminType == 'Agent')
                <li class="sidebar-item {{$action =='admin.agent_roles' || $action =='admin.agent_roles_paginate' || $action =='admin.add-agent-role' || $action =='admin.edit-agent-role'  ?'active':''}}">
                    <a href="{{ url('/admin/agent-roles'); }}" class='sidebar-link'>
                        <i class="bi bi-square-half"></i>
                        <span>User Roles</span>
                    </a>
                    
                </li>
                @endif
               
                
                <li class="sidebar-item">
                    <a href="{{ url('/admin/logout'); }}" class='sidebar-link'>
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Log Out</span>
                    </a>
                </li>
            </ul>
        </div>
        <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
    </div>
</div>
