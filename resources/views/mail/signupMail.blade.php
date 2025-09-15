@php
$siteUrl = env('SITE_URL');
@endphp
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Propertifi</title>
<meta name="viewport" content="width=600">
</head>

<body>
<table style="margin:0 auto; width:600px; border:1px solid #d3d3d3; padding:30px 15px 15px; box-sizing:border-box;">
  <tr>
    <td style="vertical-align:top;"><a href="https://propertifi.co/"><img src="https://propertifi.co/agent-panel/public/admin/images/logo/p-logo.jpg" alt=""  width="151"/></a></td>
    <td style="font-family:Verdana, Geneva, sans-serif; color:#031470; font-size:13px; text-align:right;font-weight:400;line-height: 20px;vertical-align:top;">
    <strong>Date:</strong> {{date('F d, Y')}}
    </td>
  </tr>  
  <tr>
    <td colspan="2" style="border-bottom:6px solid #031470; padding-top:20px;"></td>
  </tr> 
  <tr>
    <td style="padding-top:20px; padding-bottom:26px;font-family:Verdana, Geneva, sans-serif; color:#031470; font-size:13px;font-weight:400;">Dear {{$data['userData']['company_name']}},</td>
    <td style="padding-top:20px; padding-bottom:26px;font-family:Verdana, Geneva, sans-serif; color:#031470; font-size:13px;font-weight:400; text-align:right;"></td>
  </tr>
   <tr>
    <td colspan="2" style="padding-bottom:10px;font-family:Verdana, Geneva, sans-serif; color:#031470; font-size:13px; line-height:20px;font-weight:400;">Welcome to Propertifi! We're excited to have you join our network of professional property managers. To start receiving matched property owner leads, please review the plans and complete your profile with a few essential details.<br><br>
    
    <strong>Complete Your Profile</strong><br><br>
    
    Please provide the following information:<br><br>
    
    Service Areas (zip codes you cover)<br>
    Types of properties you manage (residential, commercial, multi-family, etc.)<br>
    Minimum and maximum unit sizes you work with<br>
    Your management fee structure<br>
    Years of experience and certifications<br>
    Company highlights and unique value propositions<br>
    Photos of your managed properties (optional)<br><br><br>
    <strong> Why Complete Your Profile?</strong><br><br>
    
    Get matched with property owners looking for managers in your area<br>
    Receive leads that match your expertise and preferences<br>
    Stand out with your unique management approach<br>
    Save time by receiving only relevant property opportunities<br><br>
    <strong>Next Steps</strong><br><br>
    
    Log in to your account at [Login Link]<br>
    Navigate to "Profile Settings"<br>
    Fill in all required information<br>
    Click "Save and Activate Profile"<br>
    Review the subscription plans and make a selection in your profile - URL to the details about the plans. <br>
    </td>
  </tr>
     
 
    <tr>
    <td colspan="2" style="padding-bottom:26px;font-family:Verdana, Geneva, sans-serif; color:#031470; font-size:13px;font-weight:400;line-height:20px;">Once your profile is complete, you'll start receiving property owner matches based on your specifications. <br> Questions? Contact us at <a href="mailto:help@propertifi.co" target="_blank" style="color:#287ebc; text-decoration:none;">help@propertifi.co</a>.</td>
  </tr>
     <tr>
    <td colspan="2" style="padding-bottom:26px;font-family:Verdana, Geneva, sans-serif; color:#031470; font-size:13px;font-weight:400;">Thank you,</td>
  </tr>
    <tr>
    <td colspan="2" style="padding-bottom:26px;font-family:Verdana, Geneva, sans-serif; color:#031470; font-size:13px;font-weight:400; line-height:20px;">The Propertifi Team <br>
    <a href="{{$siteUrl}}" target="_blank" style="color:#287ebc; text-decoration:none;">{{$siteUrl}}</a></td>
  </tr>
     
    <tr>
    <td colspan="2" style="padding-top:18px;"></td>
  </tr> 
   <tr>
    <td colspan="2" style="padding:8px;font-family:Verdana, Geneva, sans-serif; color:#ffffff; font-size:13px;font-weight:400; background:#031470; text-align:center;">Copyright © 2024 Propertifi - All Rights Reserved.</td>
  </tr>
 </table>
</body>
</html>
