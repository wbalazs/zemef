<?php
namespace App\Models;

use App\Tag;
use App\Category;
use App\Models\ToolType;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class Tool extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'identity_number' , 'serial_number', 'description', 'failure_description', 'entry_date',
        'withdrawal_date', 'value', 'tool_type_id', 'tool_type_comment', 'maintainer_id', 'review_needed',
        'status', 'business_id', 'employee_id','from'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'options' => 'array',
    ];


    public $primaryKey = 'tool_id';


    public function tool_type(): HasOne
    {
        return $this->hasOne(ToolType::class, 'tool_type_id');
    }


    public function business(): HasOne
    {
        return $this->hasOne(Business::class, 'business_id');
    }


    public function maintainer(): HasOne
    {
        return $this->hasOne(Customer::class, 'maintainer_id');
    }


    public function documents()
    {
        return $this->hasMany('App\Models\Document', 'resource_id', 'tool_id')->where('document_module', '=', 'tool_doc')->orderBy('created_at', 'DESC');
    }


    public function properties(): BelongsToMany
    {
        return $this->belongsToMany(Property::class);
    }

    public function investigations(): HasMany
    {
        return $this->hasMany(ToolInvestigation::class, 'tool_id', 'tool_id');
    }
}
