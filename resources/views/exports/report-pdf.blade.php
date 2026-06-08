<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 12px; color: #111; margin: 24px; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        .meta { color: #555; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background: #f1f5f9; }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <p class="meta">Generated: {{ $generatedAt }}</p>
    @if(count($headers))
    <table>
        <thead><tr>@foreach($headers as $h)<th>{{ $h }}</th>@endforeach</tr></thead>
        <tbody>
            @foreach($rows as $row)
            <tr>@foreach((array)$row as $cell)<td>{{ $cell }}</td>@endforeach</tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p>No data available.</p>
    @endif
</body>
</html>
