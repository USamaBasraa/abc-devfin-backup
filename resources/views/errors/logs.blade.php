@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">System Error Logs</h5>
                    <div>
                        <a href="{{ route('error.logs.clear') }}" class="btn btn-danger btn-sm" 
                           onclick="return confirm('Are you sure you want to clear all error logs? This action cannot be undone.')">
                            Clear All Logs
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($errorLogs->isEmpty())
                        <div class="alert alert-info">
                            No error logs found.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width: 5%;">ID</th>
                                        <th>Error Log Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($errorLogs as $log)
                                        <tr>
                                            <td>{{ $log->id }}</td>
                                            <td>
                                                <pre style="white-space: pre-wrap; word-break: break-word; font-size: 12px; max-height: 300px; overflow-y: auto;">{{ $log->description }}</pre>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            {{ $errorLogs->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 