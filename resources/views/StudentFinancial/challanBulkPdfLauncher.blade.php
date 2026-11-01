<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Bulk challan PDF</title>
</head>
<body style="font-family: sans-serif; padding: 1.5rem;">
    <p>Opening bulk challan preview in a new tab…</p>
    <form id="bulk-pdf-form" action="{{ $action }}" method="post" target="_self" accept-charset="UTF-8">
        {{-- Textarea avoids size/quoting limits of a single hidden input value attribute --}}
        <textarea name="bulk_json" style="display:none" aria-hidden="true">{{ e($bulkJson) }}</textarea>
        <noscript>
            <p><button type="submit">Open PDF preview</button></p>
        </noscript>
    </form>
    <script>
        document.getElementById('bulk-pdf-form').submit();
    </script>
</body>
</html>
