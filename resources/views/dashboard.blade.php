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
        @for ($i = 1; $i <= 10; $i++)
        <div class="relative">
            <div class="w-32 h-32 bg-gray-200 text-center flex items-center justify-center hover:bg-gray-300">
                <a href="#" class="block w-full h-full">
                    <img src="画像のURL" alt="コンテンツ{{ $i }}">
                </a>
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
                        経費管理
                        @break
                    @case(7)
                        コース管理
                        @break
                    @case(8)
                        オプション管理
                        @break
                    @case(9)
                        物販管理
                        @break
                    @case(10)
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

