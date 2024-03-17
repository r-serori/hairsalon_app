<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      {{ __('スケジュール作成') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-8">
          <div class="card">
            <div class="card-body">
              <form method="POST" action="{{ route('schedules.store') }}">
                @csrf

                <!-- 日付 -->
                <div class="mb-3">
                  <label for="date" class="form-label">{{ __('日付') }}</label>
                  <input type="date" class="form-control" id="date" name="date" required>
                </div>

                <!-- 開始時間 -->
                <div class="mb-3">
                  <label for="start_time" class="form-label">{{ __('開始時間') }}</label>
                  <input type="time" class="form-control" id="start_time" name="start_time" required>
                </div>

                <!-- 終了時間 -->
                <div class="mb-3">
                  <label for="end_time" class="form-label">{{ __('終了時間') }}</label>
                  <input type="time" class="form-control" id="end_time" name="end_time" required>
                </div>
  <!-- 顧客 -->
  <div class="mb-3">
    <label for="customer_name" class="form-label">{{ __('名前') }}</label>
    <input type="text" class="form-control" id="customer_name" name="customer_name" value=新規 required>
  </div>

  <!-- コース -->
  <div class="mb-3">
    <label>{{ __('コース') }}</label><br>
    @foreach($courses as $course)
    <input type="checkbox" id="courses_{{ $course->id }}" name="courses_id[]" value="{{ $course->id }}" >
    <label for="courses_{{ $course->id }}">{{ $course->course_name }}</label><br>
    @endforeach
  </div>

  <!-- オプション -->
  <div class="mb-3">
    <label>{{ __('オプション') }}</label><br>
    @foreach($options as $option)
    <input type="checkbox" id="options_{{ $option->id }}" name="options_id[]" value="{{ $option->id }}" >
    <label for="options_{{ $option->id }}">{{ $option->option_name }}</label><br>
    @endforeach
  </div>

  <!-- ヘアスタイル -->
  <div class="mb-3">
    <label>{{ __('ヘアスタイル') }}</label><br>
    @foreach($hairstyles as $hairstyle)
    <input type="checkbox" id="hairstyles_{{ $hairstyle->id }}" name="hairstyles_id[]" value="{{ $hairstyle->id }}">
    <label for="hairstyles_{{ $hairstyle->id }}">{{ $hairstyle->hairstyle_name }}</label><br>
    @endforeach
  </div>

  <!-- 商品 -->
  <div class="mb-3">
    <label>{{ __('商品') }}</label><br>
    @foreach($merchandises as $merchandise)
    <input type="checkbox" id="merchandises_{{ $merchandise->id }}" name="merchandises_id[]" value="{{ $merchandise->id }}" >
    <label for="merchandises_{{ $merchandise->id }}">{{ $merchandise->merchandise_name }}</label><br>
    @endforeach
  </div>
         

                <button type="submit" class="btn btn-primary">{{ __('作成') }}</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>


</x-app-layout>