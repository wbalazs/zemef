<?php

namespace App\Models;

use App\Tag;
use App\Category;
use App\Models\Bank;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BankAccount extends Model
{
    protected $table = 'master_bank_accounts';
    public $primaryKey = 'bank_account_id';


    public function bank()
    {
        return $this->belongsTo(Bank::class, 'bank_id', 'bank_id');
    }


    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class);
    }
}
