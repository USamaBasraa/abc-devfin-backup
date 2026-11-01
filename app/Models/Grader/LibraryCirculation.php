<?php

namespace App\Models\Grader;

use Illuminate\Database\Eloquent\Model;

class LibraryCirculation extends Model
{
    protected $table = 'LIBRARY_CIRCULATION';
     protected $primaryKey = 'id';
      /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ID',
        'SAI_ENROLLMENT_NO',
        'LIBRARY_BOOKS_ID',
        'LC_ACCESSION_NO',
        'LC_ISSUE_DATE',
        'LC_ISSUE_NO_OF_DAYS',
        'LC_RETURN_DATE',
        'LC_BOOK_STATUS',
        'LC_TOTAL_LATE_DAYS',
        'LC_FINE_PER_DAY',
        'LC_FINE_TOTAL',
        'ENTRY_USER_ID',
        'LC_DUE_DATE'
    ];
}
