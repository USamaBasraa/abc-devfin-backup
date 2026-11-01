<?php
date_default_timezone_set("Asia/Karachi");
$filename ="excelreport_".date("h:i:sa")."_.xls";
header('Content-type: application/ms-excel');
header('Content-Disposition: attachment; filename='.$filename);
?>
<style>
table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
}
</style>
<table class="table">
    <thead class="thead-dark">
      <tr>
        <th scope="col">Author Name</th>
        <th scope="col">Title</th>
        <th scope="col">Accession No</th>
        <th scope="col">DDC</th>
        <th scope="col">Author Mark</th>
        <th scope="col">Publishing Year</th>
        <th scope="col">Purchase Date</th>
        <th scope="col">School</th>
        {{-- <th scope="col">Subject</th> --}}
      </tr>
    </thead>
    <tbody>
        @foreach ($viewBooks as $Book)

        <tr>
        <td scope="row">{{$Book->library_author_name}}</td>
        <td scope="row">{{ $Book->library_title}}</td>
        <td scope="row">{{ $Book->accession_no}}</td>
        <td scope="row">{{ $Book->library_ddc_number}}</td>
        <td scope="row">{{ $Book->library_author_mark}}</td>
        <td scope="row">{{ $Book->date_publication}}</td>
        <td scope="row">{{ \Carbon\Carbon::parse($Book->date_of_purchase)->format('d F, Y') }}</td>
        <td scope="row">{{ $Book->school_name}}</td>
        {{-- <td scope="row">{{ $Book['library_subject']}}</td> --}}

    </tr>
@endforeach
    </tbody>
  </table>
