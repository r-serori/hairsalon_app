<!-- resources/views/stocks/create.blade.php -->

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            新規在庫作成
        </h2>
    </x-slot>

    <div class="container mx-auto py-6">
        <div class="w-full max-w-md mx-auto">
            <form action="{{ route('stocks.store') }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        カテゴリー
                    </label>
                    @foreach ($stock_categories as $category)
                    <div class="flex items-center mb-2">
                        <input type="radio" id="stock_category_{{ $category->id }}" name="stock_category_id" value="{{ $category->id }}" class="mr-2">
                        <label for="stock_category_{{ $category->id }}" class="text-gray-700">{{ $category->category }}</label>
                    </div>
                    @endforeach
                    @error('stock_category_id')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>


                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="product_name">
                        商品名
                    </label>
                    <input type="text" name="product_name" id="product_name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="商品名を入力してください">
                    @error('product_name')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="quantity">
                    数量
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="quantity" name="quantity" type="number" placeholder="数量を入力してください">
                @error('quantity')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="product_price">
                        仕入れ価格
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="product_price" name="product_price" type="number" placeholder="仕入れ価格を入力してください">
                    @error('product_price')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="supplier">
                        仕入れ先
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="supplier" name="supplier" type="text" placeholder="仕入れ先を入力してください">

                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="remarks">
                        備考
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="remarks" name="remarks" type="text" placeholder="備考を入力してください">

                </div>
                <div class="flex items-center justify-between">
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                        作成
                    </button>

                </div>
            </form>
        </div>
    </div>
</x-app-layout>