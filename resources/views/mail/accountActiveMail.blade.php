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
    <td colspan="2" style="padding-bottom:10px;font-family:Verdana, Geneva, sans-serif; color:#031470; font-size:13px; line-height:20px;font-weight:400;">Welcome to Propertifi! Your account has been verified and is now active. We're thrilled to have you join our network of top-rated property managers.<br><br>
    
    The final step before we can start sending you matched property owner leads is to complete your professional profile. A detailed profile ensures we connect you with the right clients who are looking for your specific expertise.<br><br>
    
<strong>Login into account:</strong> For access your account dashboard, Use your registered email id as username and temporary password <strong>{{$data['userData']['temp_pass']}}</strong><br><br>

Complete Your Profile Now<br><br>

<strong>Why a Complete Profile Matters</strong><br><br>
A strong profile is the key to your success on Propertifi. It allows you to:<br><br>

Get Better Matches: Receive leads from property owners looking for managers in your specific service areas.<br><br>

Showcase Your Expertise: Attract the right clients by highlighting the property types and unit sizes you specialize in.<br><br>

<strong>Stand Out:</strong> Differentiate your business with your unique value propositions and experience.<br><br>

<strong>Save Time:</strong> Receive only relevant opportunities that fit your business model.<br><br>

To make the best matches, please log in and provide the following details:<br><br>

Service Areas (the zip codes you cover)<br><br>

Property Types you manage (residential, commercial, etc.)<br><br>

Min/Max Unit Sizes you work with<br><br>

Your Management Fee Structure<br><br>

Years of Experience and any certifications<br><br>

Company Highlights and unique value propositions<br><br>

The Final Step: Activate Your Leads
After your profile is complete, the last step is to review our subscription plans. Choosing a plan will activate your lead flow and you'll officially be ready to receive matched opportunities.<br><br>

<strong>Review Subscription Plans</strong><br><br>

If you have any questions, please don't hesitate to contact our support team at <a href="mailto:help@propertifi.co">help@propertifi.co</a>.<br><br>

We look forward to helping you grow your business!<br>
    
    
    </td>
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
    <td colspan="2" style="padding:8px;font-family:Verdana, Geneva, sans-serif; color:#ffffff; font-size:13px;font-weight:400; background:#031470; text-align:center;">Copyright © {{date('Y')}} Propertifi - All Rights Reserved.</td>
  </tr>
 </table>
</body>
</html>
