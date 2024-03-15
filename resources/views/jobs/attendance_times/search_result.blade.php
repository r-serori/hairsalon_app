{{-- resources/views/jobs/attendance_times/search_result.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h1>Attendance Times</h1>
    </x-slot>

    <div class="container">
        <h2>Search Result</h2>

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
</x-app-layout>