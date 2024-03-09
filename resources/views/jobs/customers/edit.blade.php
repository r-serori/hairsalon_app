<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      {{ __('顧客編集') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-8">
          <div class="card">
            <div class="card-body">
              <form method="POST" action="{{ route('customers.update', $customer->id) }}">
                @csrf
                @method('PUT')

                <!-- 名前 -->
                <div class="mb-3">
                  <label for="name" class="form-label">{{ __('名前') }}</label>
                  <input type="text" class="form-control" id="name" name="name" value="{{ $customer->name }}" required>
                </div>

                <!-- 電話番号 -->
                <div class="mb-3">
                  <label for="phone_number" class="form-label">{{ __('電話番号') }}</label>
                  <input type="tel" class="form-control" id="phone_number" name="phone_number" value="{{ $customer->phone_number }}" required>
                </div>

                <!-- 特徴 -->
                <div class="mb-3">
                  <label for="features" class="form-label">{{ __('特徴') }}</label>
                  <textarea class="form-control" id="features" name="features" rows="3">{{ $customer->features }}</textarea>
                </div>

                <div class="mb-3">
                  <label for="hairstyle_id" class="form-label">{{ __('髪型') }}</label>
                  <select class="form-control" id="hairstyle_id" name="hairstyle_id">
                    <option value="">選択しない</option>
                    @foreach($hairstyles as $hairstyle)
                    <option value="{{ $hairstyle->id }}" {{ $customer->hairstyle_id == $hairstyle->id ? 'selected' : '' }}>{{ $hairstyle->hairstyle_name }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="mb-3">
                  <label for="course_id" class="form-label">{{ __('コース名') }}</label>
                  <select class="form-control" id="course_id" name="course_id">
                    <option value="">選択しない</option>
                    @foreach($courses as $course)
                    <option value="{{ $course->id }}" {{ $customer->course_id == $course->id ? 'selected' : '' }}>{{ $course->course_name }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="mb-3">
                  <label for="option_id" class="form-label">{{ __('オプション名') }}</label>
                  <select class="form-control" id="option_id" name="option_id">
                    <option value="">選択しない</option>
                    @foreach($options as $option)
                    <option value="{{ $option->id }}" {{ $customer->option_id == $option->id ? 'selected' : '' }}>{{ $option->option_name }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="mb-3">
                  <label for="merchandise_id" class="form-label">{{ __('物販') }}</label>
                  <select class="form-control" id="merchandise_id" name="merchandise_id">
                    <option value="">選択しない</option>
                    @foreach($merchandises as $merchandise)
                    <option value="{{ $merchandise->id }}" {{ $customer->merchandise_id == $merchandise->id ? 'selected' : '' }}>{{ $merchandise->merchandise_name }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="mb-3">
                  <label for="user_id" class="form-label">{{ __('担当者') }}</label>
                  <select class="form-control" id="user_id" name="user_id">
                    <option value="">選択しない</option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ $customer->user_id == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                  </select>
                </div>



                <button type="submit" class="btn btn-primary">{{ __('更新') }}</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>