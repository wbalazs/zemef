<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ItemList\ItemList;

class Document extends Model
{
    protected $table = 'documents';

    public $primaryKey = 'document_id';

    protected $fillable = [
        'resource_id',
        'document_path',
        'document_type_id',
        'document_comment',
        'document_module',
        'doc_issue_date',
        'doc_expire_date',
        'doc_reminder_days',
    ];
    
    public function doc_type()
    {
        return $this->hasOne('App\Models\DocType', 'doc_type_id', 'document_type_id');
    }

    public function itemList()
    {
        return $this->belongsTo(ItemList::class, 'resource_id', 'id')
            ->where('document_module', 'item_list_doc');
    }
}
