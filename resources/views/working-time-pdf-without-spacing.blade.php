<div>
    <style>
        * {
            font-family: DejaVu Sans !important;
        }

        body {
            font-size: 14px;
            font-family: 'DejaVu Sans', 'Roboto', 'Montserrat', 'Open Sans', sans-serif;
            padding: 10px;
            margin: 10px;
        }


        body {
            text-align: right;
        }

        .separator {
            font-size: 1px;
            height: 1px;
            border-top: 2px solid rgb(126, 126, 126);
            border-bottom: 1px solid rgba(255, 255, 255, 0);
        }


        @page {
            size: a4;
            margin: 0;
            padding: 0;
        }

        .table_component {
            overflow: auto;
            width: 100%;
            margin-bottom: 10px;
            page-break-inside: avoid;
        }

        .table_component table {
            border: 1px solid #dededf;
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            border-spacing: 1px;
            text-align: right;
            margin: 0;
            padding: 0;
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
            border: 1px solid #fdfdfd;
            background-color: #ffffff;
            color: #000000;
            padding: 5px;
        }
    </style>
    @php
        $previousDate = null;
    @endphp
    @foreach ($guards as $guard)
        <div class="table_component">
            <table>
                <thead>
                    <tr>
                        <th colspan="3">{{ $guard->name }} ({{ $guard->guard_number }})</th>
                    </tr>
                    <tr>
                        <th>الفترة</th>
                        <th>الوقت</th>
                        <th>التاريخ</th>
                    </tr>
                    @foreach ($guard->workingTimes as $key => $workingTime)
                        @if ($workingTime->date !== $previousDate && $previousDate !== null)
                            <tr class="separator">
                                <td colspan="3"></td>
                            </tr>
                        @endif
                        <tr>
                            <td>{{ $workingTime->period }}</td>
                            <td>{{ $workingTime->time }}</td>

                            @if ($workingTime->date !== $previousDate)
                                <td>{{ $workingTime->date }}</td>
                                @php
                                    $previousDate = $workingTime->date; // Update the previous date
                                @endphp
                            @else
                                <td></td>
                            @endif
                        </tr>
                    @endforeach
                </thead>
                <tbody></tbody>
            </table>
        </div>
    @endforeach
</div>