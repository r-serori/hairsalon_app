<!-- resources/views/stocks/show.blade.php -->

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            在庫詳細
        </h2>
    </x-slot>

    <div class="container mx-auto py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-4">
                        <span class="font-bold">商品名:</span> {{ $stock->product_name }}
                    </div>
                    <div class="mb-4">
                        <span class="font-bold">カテゴリー:</span> {{ $stock->category }}
                    </div>
                    <div class="mb-4">
                        <span class="font-bold">数量:</span> {{ $stock->quantity }}
                    </div>
                    <div class="mb-4">
                        <span class="font-bold">仕入れ価格:</span> {{ $stock->purchase_price }}
                    </div>
                    <div class="mb-4">
                        <span class="font-bold">仕入れ先:</span> {{ $stock->supplier }}
                    </div>
                    <div class="mb-4">
                        <span class="font-bold">備考:</span> {{ $stock->remarks }}
                    </div>
                    <div>
                        <a href="{{ route('stocks.edit', $stock->id) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2">編集</a>
                        <form action="{{ route('stocks.destroy', $stock->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">削除</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
