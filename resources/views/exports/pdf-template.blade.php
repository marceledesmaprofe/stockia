<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Generated on {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                @foreach($headers as $header)
                    <th>{{ ucfirst(str_replace('_', ' ', $header)) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($records as $record)
                <tr>
                    @foreach($headers as $header)
                        <td>
                            @if($header === 'category_name' && isset($record->category))
                                {{ $record->category->name ?? 'Uncategorized' }}
                            @elseif($header === 'product_count' && isset($record->products))
                                {{ $record->products->count() }}
                            @elseif($header === 'status')
                                {{ $record->{$header} ? 'Active' : 'Inactive' }}
                            @else
                                {{ $record->{$header} ?? '' }}
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>