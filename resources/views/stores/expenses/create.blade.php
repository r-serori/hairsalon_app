<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('経費の新規作成') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                <div class="p-6 sm:px-20 bg-white border-b border-gray-200">

                    

                

                    <form method="POST" action="{{ route('expense.store') }}">
                        @csrf

                        <!-- Expense Name -->
                        <div>
                            <label for="expense_name" class="block font-medium text-sm text-gray-700">{{ __('経費名') }}</label>

                            <input id="expense_name" class="block mt-1 w-full" type="text" name="expense_name" value="{{ old('expense_name') }}" required autofocus />
                        </div>

                        <!-- Expense Category -->
                        <div class="mt-4">
                            <label for="expense_category" class="block font-medium text-sm text-gray-700">{{ __('経費カテゴリー') }}</label>

                            <input id="expense_category" class="block mt-1 w-full" type="text" name="expense_category" value="{{ old('expense_category') }}" required />
                        </div>

                        <!-- Expense Location -->
                        <div class="mt-4">
                            <label for="expense_location" class="block font-medium text-sm text-gray-700">{{ __('経費発生場所') }}</label>

                            <input id="expense_location" class="block mt-1 w-full" type="text" name="expense_location" value="{{ old('expense_location') }}" required />
                        </div>

                        <!-- Expense Date -->
                        <div class="mt-4">
                            <label for="expense_date" class="block font-medium text-sm text-gray-700">{{ __('経費発生日') }}</label>

                            <input id="expense_date" class="block mt-1 w-full" type="date" name="expense_date" value="{{ old('expense_date') }}" required />
                        </div>

                        <!-- Remarks -->
                        <div class="mt-4">
                            <label for="remarks" class="block font-medium text-sm text-gray-700">{{ __('備考') }}</label>

                            <textarea id="remarks" name="remarks" class="block mt-1 w-full" rows="3">{{ old('remarks') }}</textarea>
                        </div>

                        <!-- Price -->
                        <div class="mt-4">
                            <label for="price" class="block font-medium text-sm text-gray-700">{{ __('金額') }}</label>

                            <input id="price" class="block mt-1 w-full" type="number" step="0.01" name="price" value="{{ old('price') }}" required />
                        </div>

                        <!-- Tax -->
                        <div class="mt-4">
                            <label for="tax" class="block font-medium text-sm text-gray-700">{{ __('消費税') }}</label>

                            <input id="tax" class="block mt-1 w-full" type="number" step="0.01" name="tax" value="{{ old('tax', 1.10) }}" required />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" class="ml-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                {{ __('経費を登録する') }}
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

</x-app-layout>
