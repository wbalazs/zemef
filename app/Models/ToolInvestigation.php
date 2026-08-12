<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ToolInvestigation extends Model
{
    protected $fillable = [
        'tool_id', 'date', 'value', 'employee_id', 'employee_name'
    ];

    public function tool()
    {
        return $this->belongsTo(Tool::class, 'tool_id', 'tool_id');
    }
}
