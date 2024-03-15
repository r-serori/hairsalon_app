<!-- resources/views/jobs/schedules/index.blade.php -->

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Schedule List') }}
        </h2>
        <a href="{{ route('schedules.create') }}" class="btn btn-success float-end">Create Schedule</a>
    </x-slot>

    <div class="container py-12">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                    <th>Customer Name</th>
                                    <th>Phone Number</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($schedules as $schedule)
                                    <tr>
                                        <td>{{ $schedule->date }}</td>
                                        <td>{{ $schedule->start_time }}</td>
                                        <td>{{ $schedule->end_time }}</td>
                                        <td>{{ $schedule->customer_name }}</td>
                                        <td>{{ $schedule->phone_number }}</td>

                                        <td>
                                            <a href="{{ route('schedules.show', $schedule->id) }}" class="btn btn-info">Show</a>
                                            <a href="{{ route('schedules.edit', $schedule->id) }}" class="btn btn-primary">Edit</a>
                                            <form action="{{ route('schedules.destroy', $schedule->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this schedule?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
