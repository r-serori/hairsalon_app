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
                        <div class="card-body">
                            <h3 class="font-semibold text-lg">{{ $customer->name }} さんの詳細情報</h3>

                            <ul>
                                <li><strong>名前:</strong> {{ $customer->name }}</li>
                                <li><strong>電話番号:</strong> {{ $customer->phone_number }}</li>
                                <li><strong>特徴:</strong> {{ $customer->features }}</li>
                                <li><strong>髪型:</strong> {{ $customer->hairstyle ? $customer->hairstyle->hairstyle_name : '-' }}</li>
                                <li><strong>コース名:</strong> {{ $customer->course ? $customer->course->course_name : '-' }}</li>
                                <li><strong>オプション名:</strong> {{ $customer->option ? $customer->option->option_name : '-' }}</li>
                                <li><strong>物販:</strong> {{ $customer->merchandise ? $customer->merchandise->merchandise_name : '-' }}</li>
                            </ul>

                            <div class="mt-4">
                                <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-warning">{{ __('編集') }}</a>
                                <form method="POST" action="{{ route('customers.destroy', $customer->id) }}" class="inline-block">
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
</x-app-layout>
