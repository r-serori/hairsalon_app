<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      日次売り上げ編集
    </h2>
  </x-slot>

  <a href="{{ route('daily_sales.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
    売り上げ画面へ戻る
  </a>

  <div class="container mx-auto py-6">
    <div class="w-full max-w-md mx-auto">
      <form action="{{ route('daily_sales.store') }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        @csrf
        <div class="mb-4">
          <label class="block text-gray-700 text-sm font-bold mb-2" for="date">
            日にち
          </label>
          <input type="date" name="date" id="date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="日にちを入力してください">

          @error('date')
          <p class="text-red-500 text-xs italic">{{ $message }}</p>
          @enderror
        </div>
        <div class="mb-4">
          <label class="block text-gray-700 text-sm font-bold mb-2" for="daily_sales">
            売り上げ
          </label>
          <input type="daily_sales" name="daily_sales" id="daily_sales" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="売り上げを入力してください">
          @error('daily_sales')
          <p class="text-red-500 text-xs italic">{{ $message }}</p>
          @enderror
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