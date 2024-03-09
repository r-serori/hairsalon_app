<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('経費編集') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">{{ __('経費編集') }}</div>

                        <div class="card-body">
                            <form method="POST" action="{{ route('expense.update', $expense->id) }}">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label for="expense_name" class="form-label">{{ __('経費名') }}</label>
                                    <input type="text" class="form-control" id="expense_name" name="expense_name" value="{{ $expense->expense_name }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="expense_category" class="form-label">{{ __('経費カテゴリー') }}</label>
                                    <input type="text" class="form-control" id="expense_category" name="expense_category" value="{{ $expense->expense_category }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="expense_location" class="form-label">{{ __('経費発生場所') }}</label>
                                    <input type="text" class="form-control" id="expense_location" name="expense_location" value="{{ $expense->expense_location }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="expense_date" class="form-label">{{ __('経費発生日') }}</label>
                                    <input type="date" class="form-control" id="expense_date" name="expense_date" value="{{ $expense->expense_date }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="remarks" class="form-label">{{ __('備考') }}</label>
                                    <textarea class="form-control" id="remarks" name="remarks" rows="3">{{ $expense->remarks }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="price" class="form-label">{{ __('金額') }}</label>
                                    <input type="number" step="0.01" class="form-control" id="price" name="price" value="{{ $expense->price }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="tax" class="form-label">{{ __('消費税') }}</label>
                                    <input type="number" step="0.01" class="form-control" id="tax" name="tax" value="{{ $expense->tax }}" required>
                                </div>

                                <button type="submit" class="btn btn-primary">{{ __('更新') }}</button>
                            </form>

                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
