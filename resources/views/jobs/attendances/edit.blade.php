<x-app-layout>
    <x-slot name="header">
        <!-- ヘッダーのコンテンツ -->
    </x-slot>

    <div class="container">
        <h1>Edit Attendance</h1>

        <!-- Attendee Information -->
        <h2>Attendee Information</h2>
        <form action="{{ route('attendance.update', $attendance->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div>
                <label for="name">Name</label>
                <input type="text" id="name" name="name" value="{{ $attendance->name }}">
            </div>
            <div>
                <label for="phone_number">Phone Number</label>
                <input type="text" id="phone_number" name="phone_number" value="{{ $attendance->phone_number }}">
            </div>
            <div>
                <label for="position">Position</label>
                <select name="position" id="position">
                    <option value="owner" {{ $attendance->position === 'owner' ? 'selected' : '' }}>オーナー</option>
                    <option value="employee" {{ $attendance->position === 'employee' ? 'selected' : '' }}>社員</option>
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
