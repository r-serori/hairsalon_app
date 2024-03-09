<x-app-layout>
    <x-slot name="header">
        <h1>Edit Attendance Time</h1>
    </x-slot>

    <div class="container">
        <form action="{{ route('attendance_times.update', $attendanceTime->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="date">日付</label>
                <input type="date" class="form-control" id="date" name="date" value="{{ $attendanceTime->date }}">
            </div>
            <div class="form-group">
                <label for="start_time">出勤時間</label>
                <input type="time" class="form-control" id="start_time" name="start_time" value="{{ $attendanceTime->start_time }}">
            </div>
            <div class="form-group">
                <label for="end_time">退勤時間</label>
                <input type="time" class="form-control" id="end_time" name="end_time" value="{{ $attendanceTime->end_time }}">
            </div>
            <div class="form-group">
                <label for="break_time">休憩時間（時間）</label>
                <input type="text" class="form-control" id="break_time" name="break_time" value="{{ $attendanceTime->break_time }}">
            </div>
            <button type="submit" class="btn btn-primary">更新</button>
        </form>
    </div>
</x-app-layout>
