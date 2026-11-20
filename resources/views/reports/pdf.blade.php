<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 26px 30px 34px;
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 9.5px;
            color: #102a43;
            line-height: 1.45;
        }
        .header { width: 100%; border-bottom: 2px solid #1689e8; padding-bottom: 12px; }
        .header table { width: 100%; border-collapse: collapse; }
        .header td { vertical-align: top; }
        .brand { font-size: 8px; font-weight: bold; letter-spacing: 1.4px; text-transform: uppercase; color: #1689e8; }
        h1 { margin: 4px 0 2px; font-size: 20px; letter-spacing: -0.3px; color: #082b4c; }
        .meta { font-size: 8.5px; color: #52677d; }
        .meta strong { color: #102a43; }
        .org { text-align: right; font-size: 9px; color: #52677d; }
        .org .name { font-size: 12px; font-weight: bold; color: #082b4c; }
        .summary { width: 100%; border-collapse: separate; border-spacing: 6px 0; margin: 14px -6px 0; }
        .summary td {
            width: 25%;
            background: #f4faff;
            border: 1px solid #d8e7f4;
            border-radius: 5px;
            padding: 8px 10px;
        }
        .summary .label { font-size: 7.5px; text-transform: uppercase; letter-spacing: 0.8px; color: #7d91a5; }
        .summary .value { font-size: 13px; font-weight: bold; color: #102a43; margin-top: 2px; }
        h2 { margin: 18px 0 6px; font-size: 11px; color: #082b4c; }
        table.data { width: 100%; border-collapse: collapse; margin-top: 4px; }
        table.data thead th {
            background: #eaf4fe;
            border: 1px solid #d8e7f4;
            padding: 6px 7px;
            text-align: left;
            font-size: 7.5px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #52677d;
        }
        table.data tbody td {
            border: 1px solid #e8f0f7;
            padding: 5px 7px;
            font-size: 8.5px;
            color: #33475b;
        }
        table.data tbody tr:nth-child(even) td { background: #fbfdff; }
        .empty { padding: 18px; text-align: center; color: #7d91a5; border: 1px solid #e8f0f7; }
        .notes { margin-top: 18px; border-top: 1px solid #e8f0f7; padding-top: 10px; }
        .notes h3 { margin: 0 0 5px; font-size: 8.5px; text-transform: uppercase; letter-spacing: 0.7px; color: #7d91a5; }
        .notes ul { margin: 0; padding: 0 0 0 14px; }
        .notes li { font-size: 7.8px; color: #52677d; margin-bottom: 3px; }
        .footer {
            position: fixed;
            bottom: 12px;
            left: 30px;
            right: 30px;
            border-top: 1px solid #e8f0f7;
            padding-top: 6px;
            font-size: 7px;
            color: #9aabb9;
        }
        .footer .right { float: right; }
    </style>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td>
                    <div class="brand">AC Management Portal</div>
                    <h1>{{ $title }}</h1>
                    <div class="meta">
                        Period: <strong>{{ $period }}</strong> &nbsp;·&nbsp; Scope: <strong>{{ $scope }}</strong> &nbsp;·&nbsp; Timezone: {{ $timezone }}
                    </div>
                </td>
                <td class="org" style="width: 34%;">
                    <div class="name">{{ $organization->name }}</div>
                    <div>Operational report</div>
                    <div>Generated {{ $generated_at }}</div>
                </td>
            </tr>
        </table>
    </div>

    @if (! empty($summary))
        <table class="summary">
            <tr>
                @foreach ($summary as $item)
                    <td>
                        <div class="label">{{ $item['label'] }}</div>
                        <div class="value">{{ $item['value'] }}</div>
                    </td>
                @endforeach
            </tr>
        </table>
    @endif

    <h2>Report data</h2>

    @if (empty($rows))
        <div class="empty">No records were found for this period and scope.</div>
    @else
        <table class="data">
            <thead>
                <tr>
                    @foreach ($columns as $key => $label)
                        <th>{{ $label }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $row)
                    <tr>
                        @foreach (array_keys($columns) as $key)
                            <td>{{ $row[$key] ?? '—' }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="notes">
        <h3>Important notes</h3>
        <ul>
            @foreach ($notes as $note)
                <li>{{ $note }}</li>
            @endforeach
        </ul>
    </div>

    <div class="footer">
        {{ $organization->name }} · {{ $title }} · Confidential operational report
        <span class="right">Generated {{ $generated_at }} ({{ $timezone }})</span>
    </div>
</body>
</html>
