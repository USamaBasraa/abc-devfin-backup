<?php

namespace App\Models\Grader;

use Illuminate\Database\Eloquent\Model;
use DB;

class BookItems extends Model
{
    protected $table = 'LIBRARY_BOOKS';
     protected $primaryKey = 'id';
      /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ID',
        'LIBRARY_PURCHASED_BY',
        'LIBRARY_ITEM_TYPE',
        'LIBRARY_NAME',
        'LIBRARY_ACCESSION_NUMBER',
        'LIBRARY_NUMBER_ISSN',
        'LIBRARY_DDC_NUMBER',
        'LIBRARY_AUTHOR_MARK',
        'LIBRARY_AUTHOR_NAME',
        'LIBRARY_SUB_AUTHOR_NAME',
        'LIBRARY_TITLE',
        'LIBRARY_SUBTITLE',
        'LIBRARY_EDITION',
        'LIBRARY_VOLUME',
        'LIBRARY_PRICE',
        'LIBRARY_PUBLISHER',
        'LIBRARY_PLACE_PUBLICATION',
        'LIBRARY_DATE_PUBLICATION',
        'LIBRARY_PAGES',
        'LIBRARY_SOURCES',
        'LIBRARY_SERIES_STATEMENT',
        'LIBRARY_KEYWORD',
        'LIBRARY_SUBJECT',
        'LIBRARY_BINDING',
        'LIBRARY_ATTACHMENT',
        'LIBRARY_LANGUAGE',
        'LIBRAY_EBOOK',
        'LIBRARY_ENTRY_USER',
        'BOOK_STATUS'
        
    ];
}
