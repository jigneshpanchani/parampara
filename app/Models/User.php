<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\ActivityTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles;
    use SoftDeletes;
    use ActivityTrait;

    const STATUS_ACTIVE = '1';

    protected static $logName = 'User';

    public function getLogDescription(string $event): string
    {
        $roleName = $this->roles()->first()?->name ?? 'Unknown Role';

        return "<strong>{$this->first_name} {$this->last_name}</strong> has been {$event} by";
    }

    protected static $logAttributes = ['first_name', 'last_name', 'email', 'contact_no', 'address', 'role_id', 'dob', 'status'];

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'contact_no',
        'address',
        'dob',
        'role_id',
        'password',
        'status',
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
        'password' => 'hashed',
    ];

    // public function roles(){
    //     return $this->belongsTo(Role::class,'role_id','id');
    // }

    public function invoicecreate()
    {
        return $this->hasMany(Invoice::class,'created_by','id');
    }

    public function invoiceby()
    {
        return $this->hasMany(Invoice::class,'sales_user_id','id');
    }

    public function challancreate()
    {
        return $this->hasMany(Challan::class,'created_by','id');
    }

    public function challanby()
    {
        return $this->hasMany(Challan::class,'sales_user_id','id');
    }

    public function quotationcreate()
    {
        return $this->hasMany(Quotation::class,'created_by','id');
    }

    public function quotationby()
    {
        return $this->hasMany(Quotation::class,'sales_user_id','id');
    }

}
