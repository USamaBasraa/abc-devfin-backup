<?php

namespace app\models;

use illuminate\database\eloquent\model;

class Item extends model
{
    protected $table = 'SP_ITEMS';

    public $timestamps = false; // oracle timestamps handled manually

    protected $fillable = [
        'bh_cat_id',
        'item_title',
        'item_type',
        'item_category',
        'brand',
        'model',
        'budget_type',
        'status',
        'remarks',
        'entry_username',
        'created_at',
        'updated_at'
    ];
}
