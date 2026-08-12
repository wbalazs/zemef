<?php
namespace App\Models;

use App\Tag;
use App\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class CompanyLoan extends Model
{
    protected $table = 'company_loans';
    public $primaryKey = 'loan_id';

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class);
    }


    public function bank()
    {
        return $this->hasOne('App\Models\Bank', 'bank_id', 'bank_id');
    }
}
