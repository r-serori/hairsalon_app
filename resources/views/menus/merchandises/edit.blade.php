<!-- resources/views/menus/merchandises/edit.blade.php -->

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          物販を編集する
        </h2>
    </x-slot>

    <div class="container mx-auto py-6">
        <div class="max-w-2xl mx-auto">
            <form action="{{ route('merchandises.update', $merchandise->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label for="merchandise_name" class="block text-gray-700 text-sm font-bold mb-2">オプション名</label>
                    <input type="text" name="merchandise_name" id="merchandise_name" value="{{ $merchandise->merchandise_name }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    @error('merchandise_name')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="price" class="block text-gray-700 text-sm font-bold mb-2">料金</label>
                    <input type="text" name="price" id="price" value="{{ $merchandise->price }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    @error('price')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">更新</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
