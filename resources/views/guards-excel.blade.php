<div>
    <div class="table_component">
        <table style="float: right;">
            <thead>
                <tr>
                    <th colspan="9">{{ $guards->first()->site->name }}</th>
                </tr>
                <tr>
                    <th>الاسم</th>
                    <th>رقم الحارس</th>
                    <th>رقم الهوية</th>
                    <th>المسمى الوظيفي</th>
                    <th>الهاتف</th>
                    <th>تاريخ المباشرة</th>
                    <th>آيبان</th>
                    <th>البنك</th>
                    <th>الراتب</th>
                </tr>
                @foreach ($guards as $guard)
                    <tr>
                        <td>{{ $guard->name }}</td>
                        <td>{{ $guard->guard_number }}</td>
                        <td>{{ $guard->id_number }}</td>
                        <td>{{ $guard->jobTitle->name }}</td>
                        <td>{{ $guard->phone }}</td>
                        <td>{{ $guard->start_date }}</td>
                        <td>{{ $guard->bank }}</td>
                        <td>{{ $guard->iban }}</td>
                        <td>{{ $guard->salary }}</td>
                    </tr>
                @endforeach
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
