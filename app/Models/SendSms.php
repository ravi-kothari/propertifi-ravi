<?php
namespace App\Models;
class SendSms
{
	public static function SendSMS($templateID,$data){
		$curl = curl_init();
		curl_setopt_array($curl, [
		  CURLOPT_URL => "https://control.msg91.com/api/v5/flow/",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => json_encode([
			'template_id' => $templateID,
			'recipients' => [$data]
		  ]),
		  CURLOPT_HTTPHEADER => [
			"accept: application/json",
			"authkey: ".env('AUTH_KEY')."",
			"content-type: application/json"
		  ],
		]);
		
		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);
		return true;
	}
}
