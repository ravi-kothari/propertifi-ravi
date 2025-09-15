<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\RouteHelper;
use App\Models\TokenHelper;
use App\Models\Responses;
use ReallySimpleJWT\Token;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Session;
use Validator;
use Mail;
use URL;
use Cookie;
use Illuminate\Validation\Rule;

use App\Models\State;
use App\Models\User;
use App\Models\Credits;
use Illuminate\Support\Facades\Config;
use App\Mail\InvoiceMail;

class StripePaymentController extends Controller{

	private static $User;
	private static $Credits;
	
	public function __construct(){
		self::$User = new User();
		self::$Credits = new Credits();
	}

    public function stripeCheckout(Request $request){
		
		$orderData = self::$Credits->where('id',$request->o_id)->first();
		$userData = self::$User->where('id',$orderData->user_id)->first();
		
        $stripe = new \Stripe\StripeClient(Config::get('stripe.stripe_secret_key'));

        $redirectUrl = route('stripe.checkout.success') . '?session_id={CHECKOUT_SESSION_ID}&oid='.$request->o_id;
        $response =  $stripe->checkout->sessions->create([
            'success_url' => $redirectUrl,
            'payment_method_types' => ['link', 'card'],
            'line_items' => [
                [
                    'price_data'  => [
                        'product_data' => [
                            'name' => $userData->company_name,
                        ],
                        'unit_amount'  => 100 * $orderData->amount,
                        'currency'     => 'USD',
                    ],
                    'quantity'    => 1
                ],
            ],
            'mode' => 'payment',
            'allow_promotion_codes' => false
        ]);

        return redirect($response['url']);
    }

    public function stripeCheckoutSuccess(Request $request)
    {
        $stripe = new \Stripe\StripeClient(Config::get('stripe.stripe_secret_key'));
        $session = $stripe->checkout->sessions->retrieve($request->session_id);
		
		if($session->payment_status == 'paid'){
			$orderData = self::$Credits->where('id',$request->oid)->first();
			$userData = self::$User->where('id',$orderData->user_id)->first();
			self::$Credits->where('id',$request->oid)->update(['stripe_session_id' => $session->id,'status' => 1,'payment_status' => 'Success']);
			
			$plus = self::$Credits->where('type','Credit')->where('status',1)->where('user_id',$orderData->user_id)->sum('credit');
			$minus = self::$Credits->where('type','Debit')->where('status',1)->where('user_id',$orderData->user_id)->sum('credit');
			
			$balance = $plus-$minus;
			
			$setData2['id'] = $orderData->user_id;
			$setData2['credits'] = $balance;
			self::$User->UpdateRecord($setData2);
			
			$orderData = self::$Credits->where(array('id' => $request->oid))->first()->toArray();
			$userData = self::$User->where(array('id' => $orderData['user_id']))->first()->toArray();
			if($userData['email'] != ''){
				$data = [
				'orderData' => $orderData,
				'userData' => $userData,
				'subject' => 'Propertifi Invoice '.$orderData['invoice_id'],
				];
				Mail::to([strtolower(trim($userData['email']))])->bcc([strtolower(env('BCC'))])->send(new InvoiceMail($data));
			}
		}
		return redirect('/admin/payments');
    }
}
