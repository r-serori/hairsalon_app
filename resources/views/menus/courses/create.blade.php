<!-- resources/views/courses/create.blade.php -->

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            新しいコースを作成
        </h2>
    </x-slot>

    <div class="container mx-auto py-6">
        <div class="max-w-2xl mx-auto">
            <form action="{{ route('courses.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="course_name" class="block text-gray-700 text-sm font-bold mb-2">コース名</label>
                    <input type="text" name="course_name" id="course_name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"placeholder="コース名を入力してください">
                </div>
                <div class="mb-4">
                    <label for="price" class="block text-gray-700 text-sm font-bold mb-2">料金</label>
                    <input type="text" name="price" id="price" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">作成</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
