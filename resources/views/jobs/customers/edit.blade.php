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
                                    <label for="customer_name" class="form-label">{{ __('名前') }}</label>
                                    <input type="text" class="form-control" id="customer_name" name="customer_name" value="{{ $customer->customer_name }}" required>
                                </div>

                                <!-- 電話番号 -->
                                <div class="mb-3">
                                    <label for="phone_number" class="form-label">{{ __('電話番号') }}</label>
                                    <input type="tel" class="form-control" id="phone_number" name="phone_number" value="{{ $customer->phone_number }}" required>
                                </div>

                                <!-- 特徴 -->
                                <div class="mb-3">
                                    <label for="remarks" class="form-label">{{ __('特徴') }}</label>
                                    <textarea class="form-control" id="remarks" name="remarks" rows="3" required>{{ $customer->remarks }}</textarea>
                                </div>

                                <!-- 髪型 -->
                                <div class="mb-3">
                                    <label for="hairstyles" class="form-label">{{ __('髪型名') }}</label>
                                    @foreach($hairstyles as $hairstyle)
                                    <div>
                                        <input type="checkbox" id="hairstyle_{{ $hairstyle->id }}" name="hairstyles_id[]" value="{{ $hairstyle->id }}">
                                        <label for="hairstyle_{{ $hairstyle->id }}">{{ $hairstyle->hairstyle_name }}</label>
                                    </div>
                                    @endforeach
                                </div>

                                <!-- コース -->
                                <div class="mb-3">
                                    <label for="courses" class="form-label">{{ __('コース名') }}</label>
                                    @foreach($courses as $course)
                                    <div>
                                        <input type="checkbox" id="course_{{ $course->id }}" name="courses_id[]" value="{{ $course->id }}">
                                        <label for="course_{{ $course->id }}">{{ $course->course_name }}</label>
                                    </div>
                                    @endforeach
                                </div>

                                <!-- オプション名 -->
                                <div class="mb-3">
                                    <label for="options" class="form-label">{{ __('オプション名') }}</label>
                                    @foreach($options as $option)
                                    <div>
                                        <input type="checkbox" id="option_{{ $option->id }}" name="options_id[]" value="{{ $option->id }}">
                                        <label for="option_{{ $option->id }}">{{ $option->option_name }}</label>
                                    </div>
                                    @endforeach
                                </div>

                                <!-- 物販 -->
                                <div class="mb-3">
                                    <label for="merchandises" class="form-label">{{ __('物販名') }}</label>
                                    @foreach($merchandises as $merchandise)
                                    <div>
                                        <input type="checkbox" id="merchandise_{{ $merchandise->id }}" name="merchandises_id[]" value="{{ $merchandise->id }}">
                                        <label for="merchandise_{{ $merchandise->id }}">{{ $merchandise->merchandise_name }}</label>
                                    </div>
                                    @endforeach
                                </div>

                                <!-- 担当者 -->
                                <div class="mb-3">
                                    <label for="attendance" class="form-label">{{ __('担当者') }}</label>
                                    @foreach($attendances as $attendance)
                                    <div>
                                        <input type="radio" id="attendance_{{ $attendance->id }}" name="attendances_id" value="{{ $attendance->id }}">
                                        <label for="attendance_{{ $attendance->id }}">{{ $attendance->attendance_name }}</label>
                                    </div>
                                    @endforeach
                                </div>

                                <!-- 新規or既存 -->
                                <div class="mb-3">
                                    <label for="new_customer" class="form-label">{{ __('新規or既存') }}</label>
                                    <div>
                                        <input type="radio" id="new_customer" name="new_customer" value="1">
                                        <label for="new_customer">{{ __('新規') }}</label>
                                    </div>
                                    <div>
                                        <input type="radio" id="old_customer" name="new_customer" value="0">
                                        <label for="old_customer">{{ __('既存') }}</label>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">{{ __('更新') }}</button>
                                <a href="{{ route('customers.show', $customer->id) }}" class="btn btn-secondary">{{ __('キャンセル') }}</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
