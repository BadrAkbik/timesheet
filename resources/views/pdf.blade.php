<div>
    <style>
        * {
            font-family: DejaVu Sans !important;
        }

        body {
            font-size: 16px;
            font-family: 'DejaVu Sans', 'Roboto', 'Montserrat', 'Open Sans', sans-serif;
            padding: 10px;
            margin: 10px;
        }


        body {
            text-align: right;
        }


        @page {
            size: a4;
            margin: 0;
            padding: 0;
        }

        .table_component {
            overflow: auto;
            width: 100%;
        }

        .table_component table {
            border: 1px solid #dededf;
            height: 99%;
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            border-spacing: 1px;
            text-align: right;
            page-break-before: avoid;
            page-break-after: avoid;
        }

        .table_component caption {
            caption-side: top;
            text-align: right;
        }

        .table_component th {
            border: 1px solid #dededf;
            background-color: #eceff1;
            color: #000000;
            padding: 5px;
            text-align: center;
        }

        .table_component td {
            border: 1px solid #dededf;
            background-color: #ffffff;
            color: #000000;
            padding: 5px;
        }
    </style>
    @foreach ($guards as $guard)
        <div class="table_component">
            <table>
                <thead>
                    <tr>
                        <th colspan="3">{{ $guard->name }}</th>
                    </tr>
                    <tr>
                        <th>الفترة</th>
                        <th>الوقت</th>
                        <th>التاريخ</th>
                    </tr>
                    @foreach ($guard->workingTimes as $workingTime)
                        <tr>
                            <td>{{ $workingTime->period }}</td>
                            <td>{{ $workingTime->time }}</td>
                            <td>{{ $workingTime->date }}</td>
                        </tr>
                    @endforeach
                </thead>
                <tbody></tbody>
            </table>
        </div>
    @endforeach
</div>
