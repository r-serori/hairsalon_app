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

       <!-- 日次売り上げ画面へのリンク -->
       <a href="{{ route('daily_sales.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
        日次売り上げ画面へ
    </a>


    <form action="{{ route('schedules.updateDailySales') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="date" class="form-label">日付を選択してください：</label>
            <input type="date" id="date" name="date" class="form-control">
            @error('date')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
        </div>
        <button type="submit" class="btn btn-primary">日次売り上げを更新</button>
    </form>


    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif



    <div class="py-12">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="card">
                        <div class="card-body">
                            <h3>スケジュール一覧</h3>

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
                                        @foreach ($schedule->hairstyles as $hairstyle)
                                        {{ $hairstyle->hairstyle_name }},
                                        @endforeach

                                        <td>{{ $schedule->price }}</td>
                                        <td>
                                            <a href="{{ route('schedules.edit', $schedule->id) }}" class="btn btn-primary">編集</a>
                                            <form action="{{ route('schedules.destroy', $schedule->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">削除</button>
                                            </form>
                                        </td>

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