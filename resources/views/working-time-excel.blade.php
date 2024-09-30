<div>
    @php
        $previousDate = null
    @endphp
    @foreach ($guards as $guard)
        <div class="table_component">
            <table>
                <thead>
                    <tr>
                        <th colspan="3">{{ $guard->name }} ({{ $guard->guard_number }})</th>
                    </tr>
                    <tr>
                        <th>التاريخ</th>
                        <th>الوقت</th>
                        <th>الفترة</th>
                    </tr>
                    @foreach ($guard->workingTimes as $key => $workingTime)
                        @if ($workingTime->date !== $previousDate && $previousDate !== null)
                            <tr>
                                <td colspan="3"></td>
                            </tr>
                        @endif
                        <tr>
                            @if ($workingTime->date !== $previousDate)
                                <td>{{ $workingTime->date }}</td>
                                @php
                                    $previousDate = $workingTime->date; // Update the previous date
                                @endphp
                            @else
                                <td></td>
                            @endif
                            <td>{{ $workingTime->time }}</td>
                            <td>{{ $workingTime->period }}</td>
                        </tr>
                    @endforeach
                </thead>
                <tbody></tbody>
            </table>
        </div>
    @endforeach
</div>
