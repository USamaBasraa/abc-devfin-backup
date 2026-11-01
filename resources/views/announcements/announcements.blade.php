@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Create Announcement</h2>

    <!-- Show success or error messages -->
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Announcement Creation Form -->
    <form action="{{ route('announcements.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="a_title">Title</label>
            <input type="text" class="form-control" id="a_title" name="a_title" required>
        </div>

        <div class="form-group">
            <label for="a_description">Description</label>
            <textarea class="form-control" id="a_description" name="a_description" rows="3"></textarea>
        </div>

        <div class="form-group">
            <label for="a_endpoint">Endpoint</label>
            <input type="text" class="form-control" id="a_endpoint" name="a_endpoint">
        </div>

        <div class="form-group">
            <label for="a_priority">Priority</label>
            <select class="form-control" id="a_priority" name="a_priority" required>
                <option value="Compulsory">Compulsory</option>
                <option value="Non-Compulsory">Non-Compulsory</option>
            </select>
        </div>

        <div class="form-group">
            <label for="a_status">Status</label>
            <select class="form-control" id="a_status" name="a_status" required>
                <option value="Enable">Enable</option>
                <option value="Disable">Disable</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Create Announcement</button>
    </form>

    <hr>

    <h2>Announcements List</h2>

    <!-- Announcements List -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Description</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($announcements as $announcement)
                <tr>
                    <td>{{ $announcement->a_id }}</td>
                    <td>{{ $announcement->a_title }}</td>
                    <td>{{ $announcement->a_description }}</td>
                    <td>{{ $announcement->a_priority }}</td>
                    <td>{{ $announcement->a_status }}</td>
                    <td>
                        <!-- Edit Button -->
                        {{-- <a href="{{ route('announcements.edit', $announcement->a_id) }}" class="btn btn-warning btn-sm">Edit</a> --}}

                        <!-- Enable/Disable Button -->
                        @if($announcement->a_status === 'Enable')
                            <form action="{{ route('announcements.disable', $announcement->a_id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-danger btn-sm">Disable</button>
                            </form>
                        @else
                            <form action="{{ route('announcements.enable', $announcement->a_id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-success btn-sm">Enable</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
