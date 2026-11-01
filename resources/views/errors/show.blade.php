@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Error Log Details</h5>
                    <div>
                        <a href="{{ route('error.logs') }}" class="btn btn-secondary btn-sm">
                            Back to Logs
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if (!$errorLog)
                        <div class="alert alert-danger">
                            Error log not found.
                        </div>
                    @else
                        <div class="alert alert-info">
                            <strong>Log ID:</strong> {{ $errorLog->id }}
                        </div>

                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">Error Details</h6>
                            </div>
                            <div class="card-body">
                                <pre class="bg-light p-3" style="white-space: pre-wrap; word-break: break-word;">{{ $errorLog->description }}</pre>
                            </div>
                        </div>

                        @php
                            // Extract parts from the error log
                            $lines = explode("\n", $errorLog->description);
                            $errorParts = [];
                            
                            foreach ($lines as $line) {
                                $parts = explode(":", $line, 2);
                                if (count($parts) == 2) {
                                    $key = trim($parts[0]);
                                    $value = trim($parts[1]);
                                    $errorParts[$key] = $value;
                                }
                            }
                        @endphp

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Basic Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th>Error Type</th>
                                                <td>{{ $errorParts['ERROR TYPE'] ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Date</th>
                                                <td>{{ $errorParts['DATE'] ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>User</th>
                                                <td>{{ $errorParts['USER'] ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>IP Address</th>
                                                <td>{{ $errorParts['IP'] ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>URL</th>
                                                <td>{{ $errorParts['URL'] ?? 'N/A' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Technical Details</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th>Error Message</th>
                                                <td>{{ $errorParts['Error Message'] ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Error Code</th>
                                                <td>{{ $errorParts['Error Code'] ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>File</th>
                                                <td>{{ $errorParts['File'] ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Line</th>
                                                <td>{{ $errorParts['Line'] ?? 'N/A' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 