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
    <td style="padding-top:20px; padding-bottom:26px;font-family:Verdana, Geneva, sans-serif; color:#031470; font-size:13px;font-weight:400;">Hi {{$data['userData']['company_name']}},</td>
    <td style="padding-top:20px; padding-bottom:26px;font-family:Verdana, Geneva, sans-serif; color:#031470; font-size:13px;font-weight:400; text-align:right;"></td>
  </tr>
   <tr>
    <td colspan="2" style="padding-bottom:10px;font-family:Verdana, Geneva, sans-serif; color:#031470; font-size:13px; line-height:20px;font-weight:400;">Great news! A new property owner whose needs match your expertise has just joined Propertifi, and we've connected them with you.<br><br>This is a high-quality lead, and we strongly recommend you contact them within the next 24 hours to introduce yourself and discuss how you can help.<br><br>
    <hr>
    
    <strong>New Lead Details:</strong><br><br>
    
   <strong> Owner Information:</strong><br><br>

    Name: {{$data['leadData']['name']}}<br>
    Contact Email: {{$data['leadData']['email']}}<br>
    Contact Phone: {{$data['leadData']['phone']}}<br>
    Owner's Note: N/A<br><br>
    
    <strong>Property Information:</strong><br><br>
    @if($data['leadData']['category'] == 1)
    Property Type: Single Family<br>
    @endif
    @if($data['leadData']['category'] == 2)
    Property Type: Multi Family<br>
    @endif
    @if($data['leadData']['category'] == 3)
    Property Type: Association<br>
    @endif
    @if($data['leadData']['category'] == 8)
    Property Type: Commercial<br>
    @endif
    Location: {{$data['leadData']['address']}},{{$data['leadData']['city']}},{{$data['leadData']['zipcode']}}<br>
    Number of Units: 1<br>
    Services Required: Check on your Dashboard<br><br><hr>
    
    <strong>Your Next Steps:</strong><br><br>
    1. Reach Out: Contact the property owner directly to make a great first impression.<br>
    2. Update Status: Log in to your Propertifi dashboard to view the full lead details and update its status once you've made contact. This helps us know you're actively working on it.<br>
    
    </td>
  </tr>
     
 
    <tr>
    <td colspan="2" style="padding-bottom:26px;font-family:Verdana, Geneva, sans-serif; color:#031470; font-size:13px;font-weight:400;line-height:20px;">This lead was matched to you based on your profile's focus on Your Category and Zipcodes.<br>Acting quickly is the key to winning new business. Good luck!</td>
  </tr>
     <tr>
    <td colspan="2" style="padding-bottom:26px;font-family:Verdana, Geneva, sans-serif; color:#031470; font-size:13px;font-weight:400;">Best regards,</td>
  </tr>
    <tr>
    <td colspan="2" style="padding-bottom:26px;font-family:Verdana, Geneva, sans-serif; color:#031470; font-size:13px;font-weight:400; line-height:20px;">The Propertifi Team <br>
    <a href="{{$siteUrl}}" target="_blank" style="color:#287ebc; text-decoration:none;">{{$siteUrl}}</a></td>
  </tr>
     
    <tr>
    <td colspan="2" style="padding-top:18px;"></td>
  </tr> 
   <tr>
    <td colspan="2" style="padding:8px;font-family:Verdana, Geneva, sans-serif; color:#ffffff; font-size:13px;font-weight:400; background:#031470; text-align:center;">Copyright © 2025 Propertifi - All Rights Reserved.</td>
  </tr>
 </table>
</body>
</html>
