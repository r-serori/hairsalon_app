<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('一覧画面') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("ログイン中") }}
                </div>
            </div>
        </div>
    </div>
    <!-- コンテナ -->
    <div class="py-8">
        <div class="flex flex-wrap justify-center gap-8">
            <!-- 10個の均一な四角いコンテナ -->
            @for ($i = 1; $i <= 9; $i++) <div class="relative">
                <div class="w-32 h-32 bg-gray-200 text-center flex items-center justify-center hover:bg-gray-300">

                    @switch($i)
                    @case(1)
                    <a href="{{ route('attendances.index') }}" class="block w-full h-full">
                        <img src="画像のURL" alt="コンテンツ{{ $i }}">
                    </a>
                    @break
                    @case(2)

                    <a href="{{ route('customers.index') }}" class="block w-full h-full">
                        <img src="画像のURL" alt="コンテンツ{{ $i }}">
                    </a>
                    @break
                    @case(3)
                    <a href="{{ route('daily_sales.index') }}" class="block w-full h-full">
                        <img src="画像のURL" alt="コンテンツ{{ $i }}">
                    </a>
                    @break
                    @case(4)
                    <a href="{{ route('schedules.index') }}" class="block w-full h-full">
                        <img src="画像のURL" alt="コンテンツ{{ $i }}">
                    </a>
                    @break
                    @case(5)
                    <a href="{{ route('stocks.index') }}" class="block w-full h-full">
                        <img src="画像のURL" alt="コンテンツ{{ $i }}">
                    </a>
                    @break
                    @case(6)
                    <a href="{{ route('courses.index') }}" class="block w-full h-full">
                        <img src="画像のURL" alt="コンテンツ{{ $i }}">
                    </a>
                    @break
                    @case(7)
                    <a href="{{ route('options.index') }}" class="block w-full h-full">
                        <img src="画像のURL" alt="コンテンツ{{ $i }}">
                    </a>
                    @break
                    @case(8)
                    <a href="{{ route('merchandises.index') }}" class="block w-full h-full">
                        <img src="画像のURL" alt="コンテンツ{{ $i }}">
                    </a>
                    @break
                    @case(9)
                    <a href="{{ route('hairstyles.index') }}" class="block w-full h-full">
                        <img src="画像のURL" alt="コンテンツ{{ $i }}">
                    </a>
                    @break
                    @default
                    <a href="#" class="block w-full h-full">
                        <img src="画像のURL" alt="秘密">
                    </a>
                    @endswitch

                </div>

                <!-- コンテンツの下にテキスト -->
                <div class="absolute inset-x-0 bottom-0 bg-gray-900 text-white py-2">
                    @switch($i)
                    @case(1)
                    勤怠管理
                    @break
                    @case(2)
                    顧客名簿管理
                    @break
                    @case(3)
                    売上管理
                    @break
                    @case(4)
                    予約表管理
                    @break
                    @case(5)
                    在庫管理
                    @break
                    @case(6)
                    コース管理
                    @break
                    @case(7)
                    オプション管理
                    @break
                    @case(8)
                    物販管理
                    @break
                    @case(9)
                    髪型管理
                    @break
                    @default
                    No Content
                    @endswitch
                </div>
        </div>
        @endfor
    </div>
    </div>

</x-app-layout>