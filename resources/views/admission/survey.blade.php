@extends('layouts.app')

@section('title')
    BNU CMS
@stop

@section('content')

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

<div class="container mt-4">
    <div class="card shadow-sm">

        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Admission Survey</h5>

         
        </div>

        <div class="card-body">

            <!-- Session Filter -->
            <form method="get" action="{{ route('admission.survey') }}">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Select Session</label>

                        <select class="form-select" name="sess_id" onchange="this.form.submit()">
                            <option value="">Select Session</option>

                            @foreach($sessions as $session)
                                <option value="{{ $session->sess_id }}"
                                    {{ request('sess_id') == $session->sess_id ? 'selected' : '' }}>
                                    {{ $session->sess_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>

            <!-- Success Message -->
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Table -->
            @if(count($surveys) > 0)
                <div class="table-responsive">
                    <table id="surveyTable" class="table table-bordered table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Applicant ID</th>
                                <th>Q#1 BNU Information Source</th>
                                <th>Q#2 Admission Source</th>
                                <th>Entry Date</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($surveys as $survey)
                                <tr>
                                    <td>{{ $survey->applicant_id }}</td>
                                    <td>{{ $survey->information_source }}</td>
                                    <td>{{ $survey->admission_source }}</td>
                                    <td>{{ $survey->entry_date_time }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    No survey record found for selected session.
                </div>
            @endif

        </div>
    </div>
</div>



@stop




<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

<!-- Buttons -->
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>

<!-- Excel Export Dependency -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script>
    $(document).ready(function () {
        $('#surveyTable').DataTable({
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excel',
                    title: 'Admission Survey'
                },
                {
                    extend: 'csv',
                    title: 'Admission Survey'
                },
                {
                    extend: 'copy'
                }
            ],
            pageLength: 10
        });
    });
</script>

