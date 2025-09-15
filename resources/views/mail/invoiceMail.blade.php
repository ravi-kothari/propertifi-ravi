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
    <strong>Date:</strong> {{date('F d, Y')}}<br>
    <strong>Invoice Number:</strong> {{$data['orderData']['invoice_id']}}
    </td>
  </tr>  
  <tr>
    <td colspan="2" style="border-bottom:6px solid #031470; padding-top:20px;"></td>
  </tr> 
  <tr>
    <td style="padding-top:20px; padding-bottom:26px;font-family:Verdana, Geneva, sans-serif; color:#031470; font-size:13px;font-weight:400;">Dear {{$data['userData']['company_name']}},</td>
    <td style="padding-top:20px; padding-bottom:26px;font-family:Verdana, Geneva, sans-serif; color:#031470; font-size:13px;font-weight:400; text-align:right;"><strong>Transaction ID:</strong> {{$data['orderData']['txn_id']}}	</td>
  </tr>
   <tr>
    <td colspan="2" style="padding-bottom:10px;font-family:Verdana, Geneva, sans-serif; color:#031470; font-size:13px; line-height:20px;font-weight:400;">Thank you for choosing Propertifi. We are excited to be working with you and helping you achieve your goals. </td>
  </tr>
     <tr>
    <td colspan="2" style="padding-bottom:20px;font-family:Verdana, Geneva, sans-serif; color:#031470; font-size:13px; line-height:20px;font-weight:400;">If you ever have any questions, please do not hesitate to reach out. Your receipt is attached.</td>
  </tr>   
   <td colspan="2" style="padding-bottom:20px;vertical-align:top;">
   		<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border:1px solid #d3d3d3; border-radius:6px 6px 0 0; overflow:hidden;">
        	<tr>
            	<td colspan="2" style="padding:7px 18px;font-family:Verdana, Geneva, sans-serif; color:#fff; font-size:16px;font-weight:400; background:#031470;">Description</td>
                <td width="40px" colspan="1" style="padding:7px 50px 7px 15px;font-family:Verdana, Geneva, sans-serif; color:#fff; font-size:16px;font-weight:400; background:#031470; text-align:right;">Amount</td>
            </tr>
            <tr>
            	<td style="vertical-align:top;padding: 14px 15px 3px;font-family: Verdana, Geneva, sans-serif;color: #031470;font-size: 15px;font-weight: bold;">{{$data['orderData']['credit']}} Credit</td>
                <td style="vertical-align:top;padding: 14px 0px 3px;font-family: Verdana, Geneva, sans-serif;color: #031470;font-size: 13px;font-weight: 400; text-align:right;">List Price</td>
                <td style="vertical-align:top;padding: 14px 50px 3px 6px;font-family: Verdana, Geneva, sans-serif;color: #031470;font-size: 13px;font-weight: 400; text-align:right;">${{$data['orderData']['amount']}}</td>
            </tr>
            
            
             
             <tr>
            	<td style="vertical-align:top;padding: 3px 15px 16px;font-family: Verdana, Geneva, sans-serif;color: #031470;font-size: 13px;font-weight: 400;"></td>
                <td style="vertical-align:top;padding: 3px 0px 16px;font-family: Verdana, Geneva, sans-serif;color: #031470;font-size: 14px;font-weight: 400; text-align:right;">Sub Total</td>
                <td style="vertical-align:top;padding: 3px 50px 16px 6px;font-family: Verdana, Geneva, sans-serif;color: #031470;font-size: 13px;font-weight: 400; text-align:right;">${{$data['orderData']['amount']}}</td>
            </tr>
            
             <tr>
                <td colspan="3" style="vertical-align:top;padding: 3px 50px 16px 6px;font-family: Verdana, Geneva, sans-serif;color: #031470;font-size: 21px;font-weight: 400; text-align:right;">Total: ${{$data['orderData']['amount']}}</td>
            </tr>
        </table>
   </td>
  </tr>  
    <tr>
    <td colspan="2" style="padding-bottom:26px;font-family:Verdana, Geneva, sans-serif; color:#031470; font-size:13px;font-weight:400;line-height:20px;">Please feel free to get in touch with our support team at <a href="mailto:info@propertifi.co" target="_blank" style="color:#287ebc; text-decoration:none;">info@propertifi.co</a>. Please note that your credits will never expire.</td>
  </tr>
     <tr>
    <td colspan="2" style="padding-bottom:26px;font-family:Verdana, Geneva, sans-serif; color:#031470; font-size:13px;font-weight:400;">Thank you,</td>
  </tr>
    <tr>
    <td colspan="2" style="padding-bottom:26px;font-family:Verdana, Geneva, sans-serif; color:#031470; font-size:13px;font-weight:400; line-height:20px;">The Propertifi Team <br>
    <a href="{{$siteUrl}}" target="_blank" style="color:#287ebc; text-decoration:none;">{{$siteUrl}}</a></td>
  </tr>
     <tr>
    <td colspan="2" style="padding-top:15px;padding-bottom:40px;font-family:Verdana, Geneva, sans-serif; color:#031470; font-size:13px; line-height:20px;font-weight:400;border-top:1px solid #d3d3d3;border-bottom:1px solid #d3d3d3;">Note: The information transmitted is intended only for the person or entity to which it is addressed and may contain confidential and/or privileged material. Any review, transmission, re-transmission, dissemination or other use of, or taking of any action in reliance upon this information, by persons or entities other than the intended recipient, is prohibited. If you received this email in error, please contact the sender and delete the material from any computer.</td>
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
