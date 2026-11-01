@extends('layouts.app')
@section('title')
    BNU CMS
@stop
@section('content')

<div class="container-fluid p-5 my-1">

<h1>University Leaving Requests</h1>

<form action="{{ url('/university-leaving-requests/filter') }}" method="POST">
    @csrf
    <label for="from_date">From Date:</label>
    <input type="date" id="from_date" name="from_date" >

    <label for="to_date">To Date:</label>
    <input type="date" id="to_date" name="to_date" >

    <button type="submit">Filter</button>
</form>
<a href="{{ route('university-leaving-requests.pdf', ['from_date' => request('from_date'), 'to_date' => request('to_date')]) }}" class="btn btn-primary">
    Download PDF
</a>
@if(isset($requests))
    <h2>Filtered Results:</h2>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Question</th>
                <th>Answer</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($requests as $request)
                <tr>
                    <td>{{ $request->ulr_enrollment_no }}</td>
                    <td>{{ $request->question }}</td>
                    <td>{{ $request->answer }}</td>
                    <td>{{ $request->created_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif


</div>
@endsection
