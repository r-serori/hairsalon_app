<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('スケジュール一覧') }}
        </h2>
    </x-slot>

                                <!-- 新規作成リンク -->
                                <div class="mt-4 text-right">
                                <a href="{{ route('schedules.create') }}" class="btn btn-primary">新規作成</a>
                            </div>


    

    <div class="py-12">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="card">
                        <div class="card-body">
                            <h3>スケジュール一覧</h3>
                            検索フォーム
                            @foreach($paginatedSchedules as $date => $schedules)
                                <h4>{{ $date }}</h4>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Start Time</th>
                                            <th>End Time</th>
                                            <th>Customer Name</th>
                                            <th>Courses</th>
                                            <th>Options</th>
                                            <th>Merchandises</th>
                                            <th>Hairstyles</th>
                                            <th>Price</th>
                                            <th>アクション</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($schedules as $schedule)
                                            <tr>
                                                <td>{{ $schedule->start_time }}</td>
                                                <td>{{ $schedule->end_time }}</td>
                                                <td>{{ $schedule->customer_name }}</td>
                                                <td>
                                                    @foreach ($schedule->courses as $course)
                                                        {{ $course->course_name }},
                                                    @endforeach
                                                </td>
                                                <td>
                                                    @foreach ($schedule->options as $option)
                                                        {{ $option->option_name }},
                                                    @endforeach
                                                </td>
                                                <td>
                                                    @foreach ($schedule->merchandises as $merchandise)
                                                        {{ $merchandise->merchandise_name }},
                                                    @endforeach
                                                </td>
                                                <td>
                                                    @foreach ($schedule->hairstyles as $hairstyle)
                                                        {{ $hairstyle->hairstyle_name }},
                                                    @endforeach
                                                </td>
                                                <td>{{ $schedule->price }}</td>
                                                <td>
                                                    <a href="{{ route('schedules.edit', $schedule->id) }}" class="btn btn-primary">編集</a>
                                                    <form action="{{ route('schedules.destroy', $schedule->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">削除</button>
                                                    </form>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endforeach
                            {{ $paginatedSchedules->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
