<x-app-layout>

  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      {{ __('経費管理') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-10">
          <div class="card">
            <div class="card-header">{{ __('経費一覧') }}</div>

            <!-- New Expense Button -->
            <div class="card-header d-flex justify-content-end">
              <a href="{{ route('expense.create') }}" class="btn btn-primary">{{ __('新規経費作成') }}</a>
            </div>

            <div class="card-body table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th scope="col">{{ __('経費名') }}</th>
                    <th scope="col">{{ __('経費カテゴリー') }}</th>
                    <th scope="col">{{ __('経費発生場所') }}</th>
                    <th scope="col">{{ __('経費発生日') }}</th>
                    <th scope="col">{{ __('備考') }}</th>
                    <th scope="col">{{ __('金額') }}</th>
                    <th scope="col">{{ __('消費税') }}</th>
                    <th scope="col">{{ __('合計金額') }}</th>
                    <th scope="col"></th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($expenses as $expense)
                  <tr>
                    <td>{{ $expense->expense_name }}</td>
                    <td>{{ $expense->expense_category }}</td>
                    <td>{{ $expense->expense_location }}</td>
                    <td>{{ $expense->expense_date }}</td>
                    <td>{{ $expense->remarks }}</td>
                    <td>{{ $expense->price }}</td>
                    <td>{{ $expense->tax }}</td>
                    <td>{{ $expense->total_amount }}</td>
                    <td>
                      <a href="{{ route('expense.show', $expense->id) }}" class="btn btn-sm btn-primary">{{ __('詳細') }}</a>
                      <a href="{{ route('expense.edit', $expense->id) }}" class="btn btn-sm btn-secondary">{{ __('編集') }}</a>
                      <form method="POST" action="{{ route('expense.destroy', $expense->id) }}" onsubmit="return confirm('本当にこの経費を削除しますか？')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">{{ __('削除') }}</button>
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
  </div>

</x-app-layout>