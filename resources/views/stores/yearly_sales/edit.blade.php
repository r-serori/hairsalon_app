<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      年次売り上げ編集
    </h2>
  </x-slot>

  <a href="{{ route('yearly_sales.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
    売り上げ画面へ戻る
  </a>

  <div class="container mx-auto py-6">
    <div class="w-full max-w-md mx-auto">
      <form action="{{ route('yearly_sales.update', $yearlySales->id) }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        @csrf
        @method('PUT')
        <div class="mb-4">
          <label class="block text-gray-700 text-sm font-bold mb-2" for="year">
            年
          </label>
          <input type="year" name="year" id="year" value="{{ $yearlySales->year }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="年を入力してください">
          @error('year')
          <p class="text-red-500 text-xs italic">{{ $message }}</p>
          @enderror
        </div>
        <div class="mb-4">
          <label class="block text-gray-700 text-sm font-bold mb-2" for="yearly_sales">
            売り上げ
          </label>
          <input type="yearly_sales" name="yearly_sales" id="yearly_sales" value="{{ $yearlySales->yearly_sales }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="売り上げを入力してください">
          @error('yearly_sales')
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