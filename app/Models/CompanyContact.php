<?php
namespace App\Models;

use App\Tag;
use App\Category;
use Illuminate\Database\Eloquent\Model;

class CompanyContact extends Model
{
    protected $table = 'company_contacts';
    public $primaryKey = 'contact_id';


    public function companies(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
