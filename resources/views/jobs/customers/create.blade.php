<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      {{ __('新規顧客作成') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-8">
          <div class="card">
            <div class="card-body">
              <form method="POST" action="{{ route('customers.store') }}">
                @csrf

                <!-- 名前 -->
                <div class="mb-3">
                  <label for="name" class="form-label">{{ __('名前') }}</label>
                  <input type="text" class="form-control" id="name" name="name" required>
                </div>

                <!-- 電話番号 -->
                <div class="mb-3">
                  <label for="phone_number" class="form-label">{{ __('電話番号') }}</label>
                  <input type="tel" class="form-control" id="phone_number" name="phone_number" required>
                </div>

                <!-- 特徴 -->
                <div class="mb-3">
                  <label for="features" class="form-label">{{ __('特徴') }}</label>
                  <textarea class="form-control" id="features" name="features" rows="3"></textarea>
                </div>

                <!-- 髪型 -->
                <div class="mb-3">
                  <label for="hairstyle_id" class="form-label">{{ __('髪型') }}</label>
                  <select class="form-control" id="hairstyle_id" name="hairstyle_id">
                    <option value="">選択しない</option> <!-- 選択しないオプションを追加 -->
                    @foreach($hairstyles as $hairstyle)
                    <option value="{{ $hairstyle->id }}">{{ $hairstyle->hairstyle_name }}</option>
                    @endforeach
                  </select>
                </div>

                <!-- コース名 -->
                <div class="mb-3">
                  <label for="course_id" class="form-label">{{ __('コース名') }}</label>
                  <select class="form-control" id="course_id" name="course_id">
                    <option value="">選択しない</option> <!-- 選択しないオプションを追加 -->
                    @foreach($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->course_name }}</option>
                    @endforeach
                  </select>
                </div>

                <!-- オプション名 -->
                <div class="mb-3">
                  <label for="option_id" class="form-label">{{ __('オプション名') }}</label>
                  <select class="form-control" id="option_id" name="option_id">
                    <option value="">選択しない</option> <!-- 選択しないオプションを追加 -->
                    @foreach($options as $option)
                    <option value="{{ $option->id }}">{{ $option->option_name }}</option>
                    @endforeach
                  </select>
                </div>

                <!-- 物販 -->
                <div class="mb-3">
                  <label for="merchandise_id" class="form-label">{{ __('物販') }}</label>
                  <select class="form-control" id="merchandise_id" name="merchandise_id">
                    <option value="">選択しない</option> <!-- 選択しないオプションを追加 -->
                    @foreach($merchandises as $merchandise)
                    <option value="{{ $merchandise->id }}">{{ $merchandise->merchandise_name }}</option>
                    @endforeach
                  </select>
                </div>

                <!-- 担当者 -->
                <div class="mb-3">
                  <label for="user_id" class="form-label">{{ __('担当者') }}</label>
                  <select class="form-control" id="user_id" name="user_id">
                    <option value="">選択しない</option> <!-- 選択しないオプションを追加 -->
                    @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                  </select>
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