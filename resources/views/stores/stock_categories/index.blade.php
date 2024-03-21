<x-app-layout>

  <x-slot name="header">
    <div class="flex justify-between items-center">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        在庫カテゴリー管理
      </h2>
      <a href="{{ route('stock_categories.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
        新規作成
      </a>

    </div>
  </x-slot>

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
                  操作
                </th>
              </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-200">
              @foreach ($stock_categories as $stock_category)
              <tr>
                <td class="px-6 py-4 whitespace-nowrap">{{ $stock_category->category }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <a href="{{ route('stock_categories.edit', $stock_category->id) }}" class="text-blue-600 hover:text-blue-900 mr-2">編集</a>
                  <form action="{{ route('stock_categories.destroy', $stock_category->id) }}" method="POST" class="inline">
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


</x-app-layout>