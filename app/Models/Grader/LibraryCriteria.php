<?php

namespace App\Models\Grader;

use Illuminate\Database\Eloquent\Model;

class LibraryCriteria extends Model
{
    protected $table = 'LIBRARY_BOOK_CRITERIA';
    protected $primaryKey = 'ID';
     /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
   protected $fillable = [
       'ID',
       'LBC_CATEGORIES',
       'LBC_LIMIT',
       'LBC_FINE_PER_DAY',
       'LBC_BOOK_LOST',
       
       'LBC_DURATION_ISSUANCE',
       'LBC_REISSUANCE_POLICY',     
       'ENTRY_USER_ID'
       
   ];
}
