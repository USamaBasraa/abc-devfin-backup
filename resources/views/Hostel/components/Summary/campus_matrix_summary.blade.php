<style>
    #campus_matrix_table_wrapper .dt-buttons {
        margin-bottom: 12px;
    }

    #campus_matrix_table thead th {
        background-color: #4454c3;
        color: #fff;
        text-align: center;
        vertical-align: middle;
        font-size: 12px;
        white-space: normal;
    }

    #campus_matrix_table tbody td {
        text-align: center;
        vertical-align: middle;
    }
</style>

<div class="filter-section mb-4" id="campus_matrix_section">
    <h5 class="mb-3"><i class="fa fa-table mr-2"></i>Hostel Campus Summary</h5>
    <div class="row">
        <div class="form-group col-12 col-lg-3">
            <label class="font-weight-bold" for="matrix_from_date">From Date</label>
            <input type="date" class="form-control" id="matrix_from_date" max="{{ date('Y-m-d') }}">
        </div>
        <div class="form-group col-12 col-lg-3">
            <label class="font-weight-bold" for="matrix_to_date">To Date</label>
            <input type="date" class="form-control" id="matrix_to_date" max="{{ date('Y-m-d') }}">
        </div>
        <div class="form-group col-12 col-lg-3">
            <label class="font-weight-bold" for="matrix_session_id">Session</label>
            <select class="form-control" id="matrix_session_id">
                <option value="">Select Session</option>
                @foreach($sessions as $session)
                    <option value="{{ $session->sess_id ?? $session->SESS_ID }}">
                        {{ $session->sess_name ?? $session->SESS_NAME }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-12 col-lg-3">
            <label class="font-weight-bold" for="matrix_hostel_type">Hostel Type</label>
            <select class="form-control" id="matrix_hostel_type">
                <option value="all">All Campuses</option>
                <option value="on_campus">On Campus</option>
                <option value="bedian">Bedian Campus</option>
                <option value="safari">Safari Campus</option>
            </select>
        </div>
        <div class="form-group col-12">
            <button type="button" class="btn btn-primary px-4" id="matrixLoadBtn">
                <i class="fa fa-filter mr-2"></i>Apply Filter
            </button>
            <button type="button" class="btn btn-secondary px-4 ml-2" id="matrixResetBtn">
                Reset
            </button>
        </div>
    </div>

    <div id="campus_matrix_table_wrapper" class="table-responsive mt-3">
        <table id="campus_matrix_table" class="table table-bordered mb-0" style="width:100%;">
            <thead>
                <tr>
                    <th>Label</th>
                    <th>On Campus - Issued</th>
                    <th>On Campus - Resident</th>
                    <th>Bedian Campus - Issued</th>
                    <th>Bedian Campus - Resident</th>
                    <th>Safari Campus - Issued</th>
                    <th>Safari Campus - Resident</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="font-weight-bold">BH</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                </tr>
                <tr>
                    <td class="font-weight-bold">GH</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
