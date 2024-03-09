<!-- create.blade.php -->

<form action="{{ route('attendance_times.store',$attendance->id) }}" method="POST">
  @csrf

  <label for="date">日付:</label>
  <input type="date" id="date" name="date"><br>

  <label for="start_time">出勤時間:</label>
<input type="time" id="start_time" name="start_time">

<label for="end_time">退勤時間:</label>
<input type="time" id="end_time" name="end_time">

  <label for="break_time">休憩時間:</label>
  <input type="number" id="break_time" name="break_time"><br>

  <input type="hidden" name="attendance_id" value="{{ $attendance->id }}">

    <button type="submit">Submit</button>
</form>
