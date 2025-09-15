<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'name',
        'email',
        'password',
		'mobile',
		'gender',
		'dob',
		'address',
		'city',
		'zipcode',
        'photo',
        'address',
		'state',
		'country',
        'last_login',
        'is_delete',
		'company_name',
		'email_varification',
		'about',
		'p_contact_name',
		'p_contact_no',
		'p_contact_email',
		'position',
		'featured',
		'credits',
		'single_family',
		'multi_family',
		'association_property',
		'commercial_property',
		'website',
		'city_extra',
		'state_extra',
		'otp',
		'country_code',
		'role_id',
		'added_by',
		'portfolio_type',
		'units',
		'status',
		'temp_pass',
		'slug',
		'seo_title',
		'seo_description',
		'seo_keywords'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

	public function GetRecordById($id){
		return $this::where('id', $id)->first();
	}
	public function UpdateRecord($Details){
		$Record = $this::where('id', $Details['id'])->update($Details);
		return true;
	}
	public function CreateRecord($Details){
		$Record = $this::create($Details);
		return $Record;
	}

    public function ExistingRecord($email){
		return $this::where('email',$email)->where('status','!=', 3)->exists();
	}
	public function ExistingMobileRecord($phone){
		return $this::where('mobile',$phone)->where('status','!=', 3)->exists();
	}
	public function ExistingRecordUpdate($email, $id){
		return $this::where('email',$email)->where('id','!=', $id)->where('status','!=', 3)->exists();
	}

    public function getUsersNames($ids){
        $user_name = 'N/A';
		$userIDs = explode(',',$ids);
        $users = $this::whereIn('id',$userIDs)->get();
        if(count($users) > 0){
            $usersArr = [];
            foreach($users as $user){
                $usersArr[] = $user->name;
            }
            if(count($usersArr) > 0){
                $user_name = implode(', ',$usersArr);
            }
            if(count($usersArr) > 1){
                $user_name = substr_replace($user_name, ' and', strrpos($user_name, ','), 1);
            }
        }
        return $user_name;
    }
}
