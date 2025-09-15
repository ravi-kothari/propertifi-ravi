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
    <td style="padding-top:20px; padding-bottom:26px;font-family:Verdana, Geneva, sans-serif; color:#031470; font-size:13px;font-weight:400;">Dear {{$data['userData']['name']}},</td>
    <td style="padding-top:20px; padding-bottom:26px;font-family:Verdana, Geneva, sans-serif; color:#031470; font-size:13px;font-weight:400; text-align:right;"></td>
  </tr>
   <tr>
    <td colspan="2" style="padding-bottom:10px;font-family:Verdana, Geneva, sans-serif; color:#031470; font-size:13px; line-height:20px;font-weight:400;">Thank you for submitting your inquiry! We appreciate your interest in our service. Our team will review your submission and get back to you soon with more information.
<br><br>
    
    If you have any further questions or concerns, feel free to reach out to us. We're here to help.

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

