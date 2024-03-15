<x-app-layout>
    <x-slot name="header">
        <h1>Attendance Times</h1>
        <div>{{ $attendance->attendance_name }}</div>
    </x-slot>

    <div class="container">
        <div class="row mb-3">
            <div class="col">
                <a href="{{ route('attendance_times.create', ['attendance_id' => $attendanceId]) }}" class="btn btn-primary">新規作成</a>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <form action="{{ route('attendance_times.search', ['attendance_id' => $attendanceId]) }}" method="GET">
                    <div class="form-group">
                        <label for="search-date">年月：</label>
                        <input type="month" id="search-date" name="search_date" class="form-control" placeholder="YYYY-MM" pattern="[0-9]{4}-[0-9]{2}">
                    </div>
                    <button type="submit" class="btn btn-primary">検索</button>
                </form>
            </div>
        </div>


        <div class="container">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>日付</th>
                        <th>出勤時間</th>
                        <th>退勤時間</th>
                        <th>休憩時間（時間）</th>
                        <th>アクション</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($attendanceTimes as $attendanceTime)
                    <tr>
                        <td>{{ $attendanceTime->date }}</td>
                        <td>{{ $attendanceTime->start_time }}</td>
                        <td>{{ $attendanceTime->end_time }}</td>
                        <td>{{ $attendanceTime->break_time }}</td>
                        <td>
                            <a href="{{ route('attendance_times.edit', $attendanceTime->id) }}" class="btn btn-success">編集</a>
                            <form action="{{ route('attendance_times.destroy', $attendanceTime->id) }}" method="POST" style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">削除</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</x-app-layout>