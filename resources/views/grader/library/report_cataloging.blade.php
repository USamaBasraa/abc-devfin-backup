@extends('layouts.app',['pageTitle' => 'Cataloging Reports'])

@section('title')
    BNU CMS
@stop

@section('content')

<?php
$bookStatus = array("Active","Pending","Update");
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500;600&display=swap');

    :root {
        --ink:        #1a1a2e;
        --ink-light:  #4a4a6a;
        --paper:      #f7f5f0;
        --paper-dark: #edeae2;
        --accent:     #c0392b;
        --accent-2:   #e8a87c;
        --gold:       #b8972a;
        --white:      #ffffff;
        --border:     #d6d0c4;
        --shadow-sm:  0 2px 8px rgba(26,26,46,0.07);
        --shadow-md:  0 6px 24px rgba(26,26,46,0.11);
        --radius:     10px;
    }

    body, .card, .card-body, .card-header {
        font-family: 'DM Sans', sans-serif;
        background: var(--paper);
    }

    /* ── Page wrapper ── */
    .catalog-page {
        padding: 28px 20px 48px;
        max-width: 1400px;
        margin: 0 auto;
    }

    /* ── Page heading ── */
    .page-heading {
        display: flex;
        align-items: baseline;
        gap: 14px;
        margin-bottom: 24px;
    }
    .page-heading h1 {
        font-family: 'DM Serif Display', serif;
        font-size: 2rem;
        color: var(--ink);
        margin: 0;
        letter-spacing: -0.5px;
    }
    .page-heading span {
        font-size: 0.78rem;
        font-weight: 500;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: var(--ink-light);
        padding: 3px 10px;
        border: 1px solid var(--border);
        border-radius: 20px;
    }

    /* ── Filter card ── */
    .filter-card {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        padding: 22px 24px;
        margin-bottom: 22px;
    }
    .filter-card .filter-row {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        gap: 14px;
    }
    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    .filter-group label {
        font-size: 0.72rem;
        font-weight: 600;
        letter-spacing: 1px;
        text-transform: uppercase;
        color: var(--ink-light);
    }
    .filter-group .form-control,
    .filter-group select {
        border: 1px solid var(--border);
        border-radius: 7px;
        background: var(--paper);
        color: var(--ink);
        font-family: 'DM Sans', sans-serif;
        font-size: 0.875rem;
        padding: 8px 12px;
        height: 38px;
        transition: border-color .2s, box-shadow .2s;
        appearance: none;
        -webkit-appearance: none;
    }
    .filter-group select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%234a4a6a' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 10px center;
        padding-right: 30px;
    }
    .filter-group .form-control:focus,
    .filter-group select:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(192,57,43,0.10);
        background: var(--white);
    }
    .filter-search-input {
        width: 340px !important;
    }
    .filter-date {
        width: 150px !important;
    }
    .date-range-group {
        display: flex;
        align-items: flex-end;
        gap: 8px;
    }
    .date-sep {
        font-size: 0.8rem;
        color: var(--ink-light);
        padding-bottom: 9px;
    }
    .filter-actions {
        display: flex;
        gap: 10px;
        align-items: flex-end;
    }
    .btn-search {
        background: var(--ink);
        color: var(--white);
        border: none;
        border-radius: 7px;
        padding: 8px 20px;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        height: 38px;
        letter-spacing: 0.3px;
        transition: background .2s, transform .1s;
    }
    .btn-search:hover { background: var(--accent); transform: translateY(-1px); }
    .btn-search:active { transform: translateY(0); }
    .btn-excel {
        background: transparent;
        color: var(--gold);
        border: 1.5px solid var(--gold);
        border-radius: 7px;
        padding: 8px 18px;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        height: 38px;
        letter-spacing: 0.3px;
        transition: background .2s, color .2s;
    }
    .btn-excel:hover { background: var(--gold); color: var(--white); }

    /* ── Column header bar ── */
    .column-header {
        display: grid;
        grid-template-columns: 4fr 2fr 2fr 2fr 1fr 1fr;
        background: var(--ink);
        color: var(--white);
        border-radius: var(--radius);
        padding: 10px 20px;
        margin-bottom: 10px;
    }
    .column-header a {
        color: var(--paper-dark);
        text-decoration: none;
        font-size: 0.72rem;
        font-weight: 600;
        letter-spacing: 1px;
        text-transform: uppercase;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: color .2s;
    }
    .column-header a:hover { color: var(--accent-2); }
    .col-center { text-align: center; justify-content: center; }
    .col-plain {
        color: var(--paper-dark);
        font-size: 0.72rem;
        font-weight: 600;
        letter-spacing: 1px;
        text-transform: uppercase;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* ── Results area ── */
    #hideView, #result-body { margin-top: 2px; }

    /* ── Book card (injected via JS) ── */
    .book-card {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        margin-bottom: 8px;
        transition: box-shadow .2s, transform .15s;
        overflow: hidden;
    }
    .book-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-1px);
    }
    .book-card-inner {
        display: grid;
        grid-template-columns: 4fr 2fr 2fr 2fr 1fr 1fr;
        align-items: center;
        padding: 14px 20px;
        gap: 8px;
    }
    .book-cover {
        width: 52px;
        height: 52px;
        object-fit: cover;
        border-radius: 6px;
        flex-shrink: 0;
        background: var(--paper-dark);
        border: 1px solid var(--border);
    }
    .book-meta { padding-left: 14px; }
    .book-title-link {
        font-family: 'DM Serif Display', serif;
        font-size: 1rem;
        color: var(--ink);
        text-decoration: none;
        line-height: 1.3;
        display: block;
    }
    .book-title-link:hover { color: var(--accent); }
    .book-author-link {
        font-size: 0.8rem;
        color: var(--ink-light);
        text-decoration: none;
        display: block;
        margin-top: 2px;
    }
    .book-author-link:hover { color: var(--accent); }
    .book-type-badge {
        display: inline-block;
        font-size: 0.65rem;
        font-weight: 600;
        letter-spacing: 1px;
        text-transform: uppercase;
        background: var(--paper-dark);
        color: var(--ink-light);
        border-radius: 4px;
        padding: 2px 7px;
        margin-top: 5px;
    }
    .cell-center {
        text-align: center;
        font-size: 0.875rem;
        color: var(--ink-light);
        font-weight: 500;
    }
    .status-badge {
        display: inline-block;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        border-radius: 20px;
        padding: 3px 10px;
    }
    .status-available { background: #e8f5e9; color: #2e7d32; }
    .status-issued    { background: #fce4ec; color: #c62828; }

    /* ── Results count bar ── */
    .results-count {
        background: var(--paper-dark);
        border: 1px solid var(--border);
        border-radius: 7px;
        padding: 8px 16px;
        margin-bottom: 12px;
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--ink-light);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .results-count .count-num {
        font-family: 'DM Serif Display', serif;
        font-size: 1.1rem;
        color: var(--accent);
    }

    /* ── No results ── */
    .no-results {
        text-align: center;
        padding: 60px 24px;
        color: var(--ink-light);
    }
    .no-results .nr-icon { font-size: 2.5rem; margin-bottom: 12px; }
    .no-results p { font-size: 0.95rem; }
</style>

<div class="catalog-page">

    {{-- Page Heading --}}
    <div class="page-heading">
        <h1>Cataloging Reports</h1>
        <span>BNU Library</span>
    </div>

    {{-- Filter Card --}}
    <div class="filter-card">
        <form enctype="multipart/form-data" method="get" action="/library/cataloging-reports-excel"
              name="search_form" id="search_form">
            {{ csrf_field() }}
            <div class="filter-row">

                {{-- School --}}
                <div class="filter-group">
                    <label>School</label>
                    <select name="school" id="school">
                        <option value="">— All Schools —</option>
                        @foreach ($schools as $row)
                            <option <?=((isset($view_library[0]->library_purchased_by)?$view_library[0]->library_purchased_by:'') == $row->school_id )? 'selected="selected"' : '' ?>
                                    value="{{ $row->school_id }}">{{ $row->school_name }}</option>
                        @endforeach
                        <option <?=((isset($view_library[0]->library_purchased_by)?$view_library[0]->library_purchased_by:'') == 13 )? 'selected="selected"' : '' ?>
                                value="13">General Collection</option>
                    </select>
                </div>

                {{-- Search Input --}}
                <div class="filter-group" style="flex:1;min-width:220px">
                    <label>Search Catalog</label>
                    <input class="form-control filter-search-input" type="text"
                           id="search-input" name="search-input" placeholder="Title, author, ISBN…">
                </div>

                {{-- Search By --}}
                <div class="filter-group">
                    <label>Search By</label>
                    <select name="search_by">
                        <option value="">All Fields</option>
                        <option value="title">Title</option>
                        <option value="author">Author</option>
                        <option value="subject">Subject</option>
                        <option value="school">School</option>
                        <option value="ddc">DDC</option>
                        <option value="isbn">ISBN</option>
                        <option value="acc_no">Acc. No</option>
                        <option value="keyword">Keyword</option>
                    </select>
                </div>

                {{-- Date Range --}}
                <div class="filter-group">
                    <label>Date Range</label>
                    <div class="date-range-group">
                        <input class="form-control filter-date" type="date" id="date_from" name="date_from">
                        <span class="date-sep">→</span>
                        <input class="form-control filter-date" type="date" id="date_to" name="date_to">
                    </div>
                </div>

                {{-- Type --}}
                <div class="filter-group">
                    <label>Type</label>
                    <select name="catalog_type">
                        <option value="">All</option>
                        <option value="1">Unique</option>
                    </select>
                </div>

                {{-- Actions --}}
                <div class="filter-actions">
                    <button type="button" class="btn-search" onclick="searchByTitle()">
                        Search
                    </button>
                    <input type="submit" formtarget="_blank" class="btn-excel" value="↓ Excel"/>
                </div>

            </div>
        </form>
    </div>

    {{-- Column Headers --}}
    <div class="column-header">
        <?php
            $title_link = url('/library/catalog?title_order=asc');
            $title_icon = 'fa-sort-alpha-asc';
            if( request()->get('title_order') == 'asc' ){
                $title_link = url('/library/catalog?title_order=desc');
                $title_icon = 'fa-sort-alpha-desc';
            }elseif( request()->get('title_order') == 'desc' ){
                $title_link = url('/library/catalog?title_order=asc');
                $title_icon = 'fa-sort-alpha-asc';
            }

            $access_link = url('/library/catalog?access_order=asc');
            $access_icon = 'fa-sort-numeric-asc';
            if( request()->get('access_order') == 'asc' ){
                $access_link = url('/library/catalog?access_order=desc');
                $access_icon = 'fa-sort-numeric-desc';
            }elseif( request()->get('access_order') == 'desc' ){
                $access_link = url('/library/catalog?access_order=asc');
                $access_icon = 'fa-sort-numeric-asc';
            }

            $ddc_link = url('/library/catalog?ddc_order=asc');
            $ddc_icon = 'fa-sort-numeric-asc';
            if( request()->get('ddc_order') == 'asc' ){
                $ddc_link = url('/library/catalog?ddc_order=desc');
                $ddc_icon = 'fa-sort-numeric-desc';
            }elseif( request()->get('ddc_order') == 'desc' ){
                $ddc_link = url('/library/catalog?ddc_order=asc');
                $ddc_icon = 'fa-sort-numeric-asc';
            }

            $author_mark_link = url('/library/catalog?author_mark_order=asc');
            $author_mark_icon  = 'fa-sort-alpha-asc';
            if( request()->get('author_mark_order') == 'asc' ){
                $author_mark_link = url('/library/catalog?author_mark_order=desc');
                $author_mark_icon = 'fa-sort-alpha-desc';
            }elseif( request()->get('author_mark_order') == 'desc' ){
                $author_mark_link = url('/library/catalog?author_mark_order=asc');
                $author_mark_icon = 'fa-sort-alpha-asc';
            }
        ?>
        <a href="<?=$title_link?>">
            Title / Author <i class="fa <?=$title_icon?>"></i>
        </a>
        <a href="<?=$access_link?>" class="col-center">
            Accession No <i class="fa <?=$access_icon?>"></i>
        </a>
        <a href="<?=$ddc_link?>" class="col-center">
            DDC <i class="fa <?=$ddc_icon?>"></i>
        </a>
        <a href="<?=$author_mark_link?>" class="col-center">
            Author Mark <i class="fa <?=$author_mark_icon?>"></i>
        </a>
        <div class="col-plain">Edition</div>
        <div class="col-plain">Status</div>
    </div>

    {{-- Dynamic Results --}}
    <div id="hideView"></div>
    <div id="result-body"></div>

</div>

@stop

@section('javascript')
<script type="text/javascript">

document.getElementById("search_form").onkeypress = function(e) {
    var key = e.charCode || e.keyCode || 0;
    if (key == 13) { e.preventDefault(); }
};

function getval(sel, id) {
    if (sel.value == 'Update') {
        window.location.href = "{{URL::to('library/add-book-item')}}/?id=" + id;
    } else {
        var bookstatus = sel.value;
        var url = "{{URL::to('library/insert-book-status')}}/" + bookstatus + '/' + id;
        $.ajax({
            type: 'get', url: url,
            processData: false, contentType: false,
            success: function(data) {
                if (data == 1) {
                    $("#statusmg" + id).show();
                    $("#statusmghtml" + id).show();
                    location.reload();
                }
            }
        });
    }
}

function searchByAll() {
    var url = "{{URL::to('library/catalog')}}";
    location.reload(url);
}

var ROUTE_ADD_BOOK = "{{ route('add-book-item') }}";
var ROUTE_AUTHOR   = "{{ route('author_books_details') }}";

function searchByTitle(){
    var bi_form = document.querySelector('#search_form');
    var fd = new FormData(bi_form);
    var url = '<?=url("library/insert-library-items-catlog")?>';

    $.ajax({
        type: 'POST', url: url,
        data: fd, processData: false, contentType: false,
        success: function(data) {
            if (data.length > 0) {
                $('#hideView').hide();
                var html = '';

                html += '<div class="results-count">';
                html += '<span>Results</span><span class="count-num">' + data.length + '</span><span>books found</span>';
                html += '</div>';

                $.each(data, function(key, value) {
                    var library_title = value.library_title;
                    var title = (library_title == null) ? 'No Title Entered' : value.library_title;
                    var item_type = value.library_item_type;
                    var imgValue = value.library_attachment;
                    var imgsrc = '<?=url("/library_images")?>' + '/' + imgValue;
                    var edition = (value.library_edition != null) ? value.library_edition : 'Not Entered';
                    var isIssued = (value.lc_book_status == 'Issued');
                    var statusClass = isIssued ? 'status-issued' : 'status-available';
                    var statusLabel = isIssued ? 'Issued' : 'Available';

                    html += '<div class="book-card">';
                    html += '<div class="book-card-inner">';

                    // Col 1: Cover + Meta
                    html += '<div style="display:flex;align-items:center">';
                    html += '<img class="book-cover" src="' + imgsrc + '" onerror="this.style.visibility=\'hidden\'">';
                    html += '<div class="book-meta">';
                    html += '<a class="book-title-link" href="' + ROUTE_ADD_BOOK + '/?id=' + value.id + '">' + title + '</a>';
                    html += '<a class="book-author-link" href="' + ROUTE_AUTHOR + '/?author=' + value.library_author_name + '">' + value.library_author_name + '</a>';
                    if (item_type) html += '<span class="book-type-badge">' + item_type + '</span>';
                    html += '</div></div>';

                    // Col 2-6
                    html += '<div class="cell-center">' + value.accession_no + '</div>';
                    html += '<div class="cell-center">' + value.library_ddc_number + '</div>';
                    html += '<div class="cell-center">' + value.library_author_mark + '</div>';
                    html += '<div class="cell-center">' + edition + '</div>';
                    html += '<div class="cell-center"><span class="status-badge ' + statusClass + '">' + statusLabel + '</span></div>';

                    html += '</div>';
                    html += '<div id="statusmghtml' + value.id + '" style="text-align:center;color:#2e7d32;font-size:12px;font-weight:700;display:none;padding:4px 0 8px">✓ Status Updated</div>';
                    html += '</div>';
                });

                $('#result-body').html(html);
            } else {
                $('#hideView').hide();
                $('#result-body').html(
                    '<div class="no-results">' +
                    '<div class="nr-icon">📚</div>' +
                    '<p>No results found. Try adjusting your search filters.</p>' +
                    '</div>'
                );
            }
        },
        error: function(jqXHR, exception) {}
    });
}

</script>
@stop