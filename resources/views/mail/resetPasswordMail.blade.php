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
    <td colspan="2" style="padding-bottom:10px;font-family:Verdana, Geneva, sans-serif; color:#031470; font-size:13px; line-height:20px;font-weight:400;">We received a request to reset your password for your account on [Your Website/Platform Name]. If you didn't request this, please ignore this email.<br><br>

To reset your password, click the link below:<br><br>

<strong>Reset Password Link:</strong> <a href="{{env('APP_URL')}}admin/reset-password/{{time()}}/{{$data['userData']['id']}}">{{env('APP_URL')}}admin/reset-password/{{time()}}/{{$data['userData']['id']}}</a><br><br>

This link will expire in 30 minutes for security reasons.<br><br>

If you're having trouble clicking the link, you can copy and paste the following URL into your browser: {{env('APP_URL')}}admin/reset-password/{{time()}}/{{$data['userData']['id']}}
    
    
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
