<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use DB;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
	
	
	public function builtSlug($input_lines){
        preg_match_all("/[0-9A-Za-z\s]/", trim($input_lines), $output_array);
        $slug = strtolower(preg_replace("/[\s]/", "-", join($output_array[0])));
        return preg_replace("/-{2,}/", "-", $slug);
    }
	public function checkPermission($userID,$mid,$colum){
		$userData = DB::table('users')->select('role_id','type')->where(array('id' => $userID))->first();
		if($userData->type == 'AccountManager'){
			$roleData = DB::table('roles')->where(array('id' => $userData->role_id))->whereRaw("FIND_IN_SET('".$mid."', $colum)")->first();
			if(isset($roleData->id)){
				return true;
			}
			return false;
		}
       	return true;
	}
	
}
