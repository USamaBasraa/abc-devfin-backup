<!DOCTYPE html>
<html>
<head>
    <title>University Leaving Requests</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <h1>University Leaving Requests</h1>
    <p>From: {{ $from_date }} To: {{ $to_date }}</p>

    <table>
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
</body>
</html>
