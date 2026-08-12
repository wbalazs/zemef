<?php
/*

=========================================================
* Argon Dashboard PRO - v1.0.0
=========================================================

* Product Page: https://www.creative-tim.com/product/argon-dashboard-pro-laravel
* Copyright 2018 Creative Tim (https://www.creative-tim.com) & UPDIVISION (https://www.updivision.com)

* Coded by www.creative-tim.com & www.updivision.com

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

*/
namespace App\Models;

use App\Tag;
use App\Category;
use Illuminate\Database\Eloquent\Model;

class ToolType extends Model
{
    protected $table = 'master_tool_types';
    public $primaryKey = 'tool_type_id';


    public function tools(): BelongsToMany
    {
        return $this->belongsTo(Tool::class);
    }
}
