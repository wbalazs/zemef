<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\BankAccount;
use App\Models\Employee;
use App\Models\Permission;
use App\Models\PurchaseTicketItem;
use Illuminate\Support\Facades\Log;
use App\Models\BillingoBill;

class User extends Authenticatable
{
    use Notifiable, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'picture','signpicture', 'role_id', 'employee_id', 'bank_contact', 'is_goldsmith','sign_code'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Get the role of the user
     *
     * @return \App\Role
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the path to the profile picture
     *
     * @return string
     */
    public function profilePicture()
    {
        if ($this->picture) {
            return "/storage/{$this->picture}";
        }

        return 'http://i.pravatar.cc/200';
    }

    public function signPicture()
    {
        if ($this->signpicture) {
            return "/storage/{$this->signpicture}";
        }

        return '';
    }

    /**
     * Check if the user has admin role
     *
     * @return boolean
     */
    public function isAdmin()
    {
        return $this->role_id == 1;
    }

    /**
     * Check if the user has creator role
     *
     * @return boolean
     */
    public function isCreator()
    {
        return $this->role_id == 2;
    }

    /**
     * Check if the user has user role
     *
     * @return boolean
     */
    public function isMember()
    {
        return $this->role_id == 3;
    }


    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }


    public function bank_accounts(): BelongsToMany
    {
        return $this->belongsToMany(BankAccount::class);
    }

    public function properties(): BelongsToMany
    {
        return $this->belongsToMany(Property::class);
    }

    public function own_items(): BelongsToMany
    {
        return $this->belongsToMany(PurchaseTicketItem::class)->whereNull('back_to_storage_at')->withPivot('delivered_at');
    }
    
    public function permissions()
    {
        // If a user has a role, return that role's permissions
        return $this->role ? $this->role->permissions() : $this->newQuery();
    }
    public function checkPermissions()
{
    return $this->role ? $this->role->permissions : collect();
}
    /**
     * A userhez van permission rendelve.
     * Ezek az admin jogok, amivel mindent is meg tud csinálni, ha van neki olyanja
     */
    public function hasPermission(string $ability, string $class): bool
    {
        //return true;
        
        return $this->checkPermissions()->contains(function (Permission $permission) use ($ability, $class) {
            $hasPermission = $permission->ability === $ability && $permission->model === $class;
            //Log::info("Checking permission for user {$this->id}: ability={$ability}, class={$class}, hasPermission=" . ($hasPermission ? 'true' : 'false'));
            return $hasPermission;
        });
    }
}