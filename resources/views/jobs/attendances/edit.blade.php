<x-app-layout>
    <x-slot name="header">
        <!-- ヘッダーのコンテンツ -->
    </x-slot>

    <div class="container">
        <h1>Edit Attendance</h1>

        <!-- Attendee Information -->
        <h2>Attendee Information</h2>
        <form action="{{ route('attendances.update', $attendance->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div>
                <label for="attendance_name">attendance_name</label>
                <input type="text" id="attendance_name" name="attendance_name" value="{{ $attendance->attendance_name }}">
            </div>
            <div>
                <label for="phone_number">Phone Number</label>
                <input type="text" id="phone_number" name="phone_number" value="{{ $attendance->phone_number }}">
            </div>
             <!-- 役職 -->
             <div class="mb-3">
            <label for="position" class="form-label">役職</label>
            <select class="form-control" id="position" name="position" required>
              <option value="オーナー">オーナー</option>
              <option value="社員">社員</option>
            </select>
          </div>
            <div>
                <label for="address">Address</label>
                <input type="text" id="address" name="address" value="{{ $attendance->address }}">
            </div>

        

            <button type="submit">Update Attendee</button>
        </form>

    </div>
</x-app-layout>