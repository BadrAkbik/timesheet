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
            size: a4 landscape;
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

        td {
            padding: 10px;
            margin: 10px;
        }
    </style>

    <div class="table_component">
        <table>
            <thead>
                <tr>
                    <th>الراتب</th>
                    <th>البنك</th>
                    <th>آيبان</th>
                    <th>تاريخ المباشرة</th>
                    <th>الهاتف</th>
                    <th>المسمى الوظيفي</th>
                    <th>اسم الموقع</th>
                    <th>رقم الهوية</th>
                    <th>رقم الحارس</th>
                    <th>الاسم</th>
                </tr>
                @foreach ($guards as $guard)
                    <tr>
                        <td>{{ $guard->salary }}</td>
                        <td>{{ $guard->iban }}</td>
                        <td>{{ $guard->bank }}</td>
                        <td>{{ $guard->start_date }}</td>
                        <td>{{ $guard->phone }}</td>
                        <td>{{ $guard->jobTitle->name }}</td>
                        <td>{{ $guard->site->name }}</td>
                        <td>{{ $guard->id_number }}</td>
                        <td>{{ $guard->guard_number }}</td>
                        <td>{{ $guard->name }}</td>
                    </tr>
                @endforeach
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
