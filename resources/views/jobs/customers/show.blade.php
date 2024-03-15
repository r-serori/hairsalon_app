<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('顧客詳細') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">{{ __('顧客詳細') }}</div>

                        <div class="card-body">
                            <div>
                                <p><strong>名前: </strong>{{ $customer->customer_name }}</p>
                                <p><strong>電話番号: </strong>{{ $customer->phone_number }}</p>
                                <p><strong>備考: </strong>{{ $customer->remarks }}</p>
                                <p><strong>新規or既存: </strong>{{ $customer->new_customer ? '新規' : '既存' }}</p>

                                <p><strong>髪型: </strong>
                                    @foreach ($customer->hairstyles as $hairstyle)
                                        {{ $hairstyle->hairstyle_name }},
                                    @endforeach
                                </p>

                                <p><strong>コース名: </strong>
                                    @foreach ($customer->courses as $course)
                                        {{ $course->course_name }},
                                    @endforeach
                                </p>

                                <p><strong>オプション名: </strong>
                                    @foreach ($customer->options as $option)
                                        {{ $option->option_name }},
                                    @endforeach
                                </p>

                                <p><strong>物販: </strong>
                                    @foreach ($customer->merchandises as $merchandise)
                                        {{ $merchandise->merchandise_name }},
                                    @endforeach
                                </p>

                                <p><strong>担当者: </strong>
                                    @foreach ($customer->attendances as $attendance)
                                        {{ $attendance->attendance_name }},
                                    @endforeach
                                </p>

                                <div class="mt-4">
                                    <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-warning">{{ __('編集') }}</a>
                                    <form method="POST" action="{{ route('customers.destroy', [$customer->id]) }}" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">{{ __('削除') }}</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
