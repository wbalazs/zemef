<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocType extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $primaryKey = 'doc_type_id';
    protected $fillable = ['name'];
}
