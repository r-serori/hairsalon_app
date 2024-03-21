<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('スケジュール編集') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="card">
                        <div class="card-body">
                            <h3>スケジュール編集</h3>
                            <form action="{{ route('schedules.update', $schedule->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <!-- ここにスケジュールの編集フォームを追加してください -->
                                <div class="form-group">
                                    <label for="date">Date:</label>
                                    <input type="text" name="date" class="form-control" value="{{ $schedule->date }}">
                                    @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="start_time">Start Time:</label>
                                    <input type="text" name="start_time" class="form-control" value="{{ $schedule->start_time }}">
                                    @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="end_time">End Time:</label>
                                    <input type="text" name="end_time" class="form-control" value="{{ $schedule->end_time }}">
                                </div>
                                <!-- 他のフォーム項目も同様に追加してください -->

                                <div class="form-groups">
                                    <label for="customer_name">Customer Name:</label>
                                    <input type="text" name="customer_name" class="form-control" value="{{ $schedule->customer_name }}">
                                    @error('customer_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="courses">Courses:</label>
                                    @foreach($courses as $course)
                                    <input type="checkbox" id="courses_{{ $course->id }}" name="courses_id[]" value="{{ $course->id }}" {{ $schedule->courses->contains($course) ? 'checked' : '' }}>
                                    <label for="courses_{{ $course->id }}">{{ $course->course_name }}</label><br>
                                    @endforeach
                                </div>
                                <div class="form-group">
                                    <label for="options">Options:</label>
                                    @foreach($options as $option)
                                    <input type="checkbox" id="options_{{ $option->id }}" name="options_id[]" value="{{ $option->id }}" {{ $schedule->options->contains($option) ? 'checked' : '' }}>
                                    <label for="options_{{ $option->id }}">{{ $option->option_name }}</label><br>
                                    @endforeach
                                </div>
                                <div class="form-group">
                                    <label for="hairstyles">Hairstyles:</label>
                                    @foreach($hairstyles as $hairstyle)
                                    <input type="checkbox" id="hairstyles_{{ $hairstyle->id }}" name="hairstyles_id[]" value="{{ $hairstyle->id }}" {{ $schedule->hairstyles->contains($hairstyle) ? 'checked' : '' }}>
                                    <label for="hairstyles_{{ $hairstyle->id }}">{{ $hairstyle->hairstyle_name }}</label><br>
                                    @endforeach
                                </div>
                                <div class="form-group">
                                    <label for="merchandises">Merchandises:</label>
                                    @foreach($merchandises as $merchandise)
                                    <input type="checkbox" id="merchandises_{{ $merchandise->id }}" name="merchandises_id[]" value="{{ $merchandise->id }}" {{ $schedule->merchandises->contains($merchandise) ? 'checked' : '' }}>
                                    <label for="merchandises_{{ $merchandise->id }}">{{ $merchandise->merchandise_name }}</label><br>
                                    @endforeach



                                </div>


                                <button type="submit" class="btn btn-primary">更新</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>