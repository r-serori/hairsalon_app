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

                <!-- 顧客名選択フォーム -->
                <div class="mb-3">
                  <label for="customer_id" class="form-label">{{ __('顧客名選択') }}</label>
                  <select class="form-select" id="customer_id" name="customer_id" required>
                    <option value="">顧客名を選択してください</option>
                    @foreach($customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->customer_name }}</option>
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