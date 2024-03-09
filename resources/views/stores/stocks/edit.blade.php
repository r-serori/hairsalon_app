<!-- resources/views/stocks/edit.blade.php -->

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            在庫編集
        </h2>
    </x-slot>

    <div class="container mx-auto py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('stocks.update', $stock->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label for="product_name" class="font-bold">商品名:</label>
                            <input type="text" name="product_name" id="product_name" value="{{ $stock->product_name }}" class="border-gray-300 focus:outline-none focus:ring focus:border-blue-300 rounded-md shadow-sm focus:ring-blue-200 focus:ring-opacity-50 w-full">
                        </div>
                        <div class="mb-4">
                            <label for="category" class="font-bold">カテゴリー:</label>
                            <input type="text" name="category" id="category" value="{{ $stock->category }}" class="border-gray-300 focus:outline-none focus:ring focus:border-blue-300 rounded-md shadow-sm focus:ring-blue-200 focus:ring-opacity-50 w-full">
                        </div>
                        <div class="mb-4">
                            <label for="quantity" class="font-bold">数量:</label>
                            <input type="number" name="quantity" id="quantity" value="{{ $stock->quantity }}" class="border-gray-300 focus:outline-none focus:ring focus:border-blue-300 rounded-md shadow-sm focus:ring-blue-200 focus:ring-opacity-50 w-full">
                        </div>
                        <div class="mb-4">
                            <label for="purchase_price" class="font-bold">仕入れ価格:</label>
                            <input type="number" name="purchase_price" id="purchase_price" value="{{ $stock->purchase_price }}" class="border-gray-300 focus:outline-none focus:ring focus:border-blue-300 rounded-md shadow-sm focus:ring-blue-200 focus:ring-opacity-50 w-full">
                        </div>
                        <div class="mb-4">
                            <label for="supplier" class="font-bold">仕入れ先:</label>
                            <input type="text" name="supplier" id="supplier" value="{{ $stock->supplier }}" class="border-gray-300 focus:outline-none focus:ring focus:border-blue-300 rounded-md shadow-sm focus:ring-blue-200 focus:ring-opacity-50 w-full">
                        </div>
                        <div class="mb-4">
                            <label for="remarks" class="font-bold">備考:</label>
                            <input type="text" name="remarks" id="remarks" value="{{ $stock->remarks }}" class="border-gray-300 focus:outline-none focus:ring focus:border-blue-300 rounded-md shadow-sm focus:ring-blue-200 focus:ring-opacity-50 w-full">
                        </div>
                        <div>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">更新</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
