<x-app-layout>

  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      {{ __('経費詳細') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-8">
          <div class="card">
            <div class="card-header">{{ __('経費詳細') }}</div>

            <div class="card-body">
              <div class="mb-3">
                <strong>{{ __('経費名') }}:</strong> {{ $expense->expense_name }}
              </div>
              <div class="mb-3">
                <strong>{{ __('経費カテゴリー') }}:</strong> {{ $expense->expense_category }}
              </div>
              <div class="mb-3">
                <strong>{{ __('経費発生場所') }}:</strong> {{ $expense->expense_location }}
              </div>
              <div class="mb-3">
                <strong>{{ __('経費発生日') }}:</strong> {{ $expense->expense_date }}
              </div>
              <div class="mb-3">
                <strong>{{ __('備考') }}:</strong> {{ $expense->remarks }}
              </div>
              <div class="mb-3">
                <strong>{{ __('金額') }}:</strong> {{ $expense->price }}
              </div>
              <div class="mb-3">
                <strong>{{ __('消費税') }}:</strong> {{ $expense->tax }}
              </div>
              <div class="mb-3">
                <strong>{{ __('合計金額') }}:</strong> {{ $expense->total_amount }}
              </div>
              <div>
                <a href="{{ route('expense.edit', $expense->id) }}" class="btn btn-primary">{{ __('編集') }}</a>
                <form method="POST" action="{{ route('expense.destroy', $expense->id) }}" onsubmit="return confirm('本当にこの経費を削除しますか？')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-danger">{{ __('削除') }}</button>
                </form>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</x-app-layout>