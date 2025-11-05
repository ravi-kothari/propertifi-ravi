<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * User role constants
     */
    const TYPE_OWNER = 'owner';
    const TYPE_PM = 'pm';
    const TYPE_ADMIN = 'admin';
    const TYPE_USER = 'User'; // Default type

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
		'seo_keywords',
		'is_verified',
		'verification_documents',
		'verified_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_verified' => 'boolean',
        'verification_documents' => 'array',
        'verified_at' => 'datetime',
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

    /**
     * Get the user's preferences.
     */
    public function preferences()
    {
        return $this->hasOne(\App\Models\UserPreferences::class);
    }

    /**
     * Get the leads assigned to this user.
     */
    public function assignedLeads()
    {
        return $this->hasMany(\App\Models\UserLead::class);
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

    /**
     * Check if user is a property manager.
     */
    public function isPropertyManager()
    {
        return $this->type === self::TYPE_PM;
    }

    /**
     * Check if user is an owner.
     */
    public function isOwner()
    {
        return $this->type === self::TYPE_OWNER;
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin()
    {
        return $this->type === self::TYPE_ADMIN;
    }

    /**
     * Check if user has a specific type.
     */
    public function hasType($type)
    {
        return $this->type === $type;
    }

    /**
     * Get the role associated with the user.
     */
    public function role()
    {
        return $this->belongsTo(Roles::class, 'role_id');
    }

    /**
     * Check if user has a specific permission.
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermission($permission)
    {
        // Admin and AccountManager have all permissions
        if ($this->isAdmin() || $this->type === 'AccountManager') {
            return true;
        }

        if (!$this->role) {
            return false;
        }

        return $this->role->hasPermission($permission);
    }

    /**
     * Check if user has any of the given permissions.
     *
     * @param array $permissions
     * @return bool
     */
    public function hasAnyPermission(array $permissions)
    {
        // Admin and AccountManager have all permissions
        if ($this->isAdmin() || $this->type === 'AccountManager') {
            return true;
        }

        if (!$this->role) {
            return false;
        }

        return $this->role->hasAnyPermission($permissions);
    }

    /**
     * Check if user has all of the given permissions.
     *
     * @param array $permissions
     * @return bool
     */
    public function hasAllPermissions(array $permissions)
    {
        // Admin and AccountManager have all permissions
        if ($this->isAdmin() || $this->type === 'AccountManager') {
            return true;
        }

        if (!$this->role) {
            return false;
        }

        return $this->role->hasAllPermissions($permissions);
    }

    /**
     * Check if user can manage users.
     *
     * @return bool
     */
    public function canManageUsers()
    {
        return $this->hasPermission('manage_users');
    }

    /**
     * Check if user can manage roles.
     *
     * @return bool
     */
    public function canManageRoles()
    {
        return $this->hasPermission('manage_roles');
    }
}
