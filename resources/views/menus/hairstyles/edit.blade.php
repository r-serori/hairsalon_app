<!-- resources/views/menus/hairstyles/edit.blade.php -->

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            髪型を編集する
        </h2>
    </x-slot>

    <div class="container mx-auto py-6">
        <div class="max-w-2xl mx-auto">
            <form action="{{ route('hairstyles.update', $hairstyle->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label for="hairstyle_name" class="block text-gray-700 text-sm font-bold mb-2">髪型名</label>
                    <input type="text" name="hairstyle_name" id="hairstyle_name" value="{{ $hairstyle->hairstyle_name }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    @error('hairstyle_name')
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
