
@extends('layouts.app')
@section('title')
    BNU CMS
@stop
@section('content')

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<!-- Bootstrap 4 JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>

<form>
    <input type="text" id="search-input" class="form-control" placeholder="Search...">
</form>
<div id="search-dropdown" class="dropdown-menu"></div>

@stop
@section('javascript')

<script>
// $(document).ready(function() {
//     $('#autocomplete-input').keyup(function() {
//         var query = $(this).val();

//         if(query != '') {
//             $.ajax({
//                 url: '{{url('autocomplete') }}',
//                 method: 'GET',
//                 data: {
//                     query: query,
//                     _token: '{{ csrf_token() }}'
//                 },
//                 success: function(data) {
//                     $('#autocomplete-results').html(data);
//                 }
//             });
//         }
//     });
// });

$(document).ready(function() {
    $('#search-input').keyup(function() {
        var query = $(this).val();
        if(query != '') {
            $.ajax({
                url: "{{ url('/autocomplete') }}",
                method: "GET",
                data: {query:query},
                dataType: "json",
                success:function(data) {
                    $('#search-dropdown').fadeIn();
                    $('#search-dropdown').html(data);
                }
            });
        }
    });
});


</script>

@stop

