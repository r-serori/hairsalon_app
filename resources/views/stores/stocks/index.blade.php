<!-- resources/views/stocks/index.blade.php -->

<x-app-layout>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                在庫管理
            </h2>
            <a href="{{ route('stocks.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                新規作成
            </a>
            <a href="{{ route('stock_categories.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                カテゴリー画面
            </a>
        </div>
    </x-slot>


    <form action="{{ route('stocks.index') }}" method="GET" class="mb-4">
        <div class="flex">
            <div class="mr-2">
                <label for="category" class="block text-gray-700 text-sm font-bold mb-2">カテゴリー</label>
                <select name="category" id="category" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="">すべてのカテゴリー</option>
                    @foreach ($stock_categories as $category)
                    <option value="{{ $category->id }}">{{ $category->category }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mr-2">
                <label for="search" class="block text-gray-700 text-sm font-bold mb-2">商品名</label>
                <input type="text" name="search" id="search" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="商品名を入力してください">
            </div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">検索</button>
        </div>
    </form>
    <a href="{{ route('stocks.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
        全体へ戻る
    </a>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif


    <div class="container mx-auto py-6">
        <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    カテゴリー
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    商品名
                                </th>

                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    数量
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    仕入れ価格
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    仕入れ先
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    備考
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    アクション
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($stocks as $stock)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $stock->stock_category->category }}</td>

                                <td class="px-6 py-4 whitespace-nowrap">{{ $stock->product_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $stock->quantity }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $stock->product_price }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $stock->supplier }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $stock->remarks }}</td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('stocks.edit', $stock->id) }}" class="text-blue-600 hover:text-blue-900 mr-2">編集</a>
                                    <form action="{{ route('stocks.destroy', $stock->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">削除</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    </div>
</x-app-layout>