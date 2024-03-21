<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      {{ __('年次売り上げ一覧') }}
    </h2>
  </x-slot>

  <!-- 日次売り上げ画面へのリンク -->
  <a href="{{ route('daily_sales.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
    日次売り上げ画面へ
  </a>

  <!-- 月次売り上げ画面へのリンク -->
  <a href="{{ route('monthly_sales.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
    月次売り上げ画面へ
  </a>


  <!-- 年次売り上げ画面へのリンク -->
  <a href="{{ route('yearly_sales.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
    年次売り上げ画面へ
  </a>

  <!-- 新規作成リンク -->
  <div class="mt-4 text-right">
    <a href="{{ route('yearly_sales.create') }}" class="btn btn-primary">新規作成</a>
  </div>



  <!-- 日次売り上げ一覧の表示 -->
  <div class="py-12">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-10">
          <div class="card">
            <div class="card-body">
              <h3>月次売り上げ一覧</h3>
              <table class="table">
                <thead>
                  <tr>
                    <th>year</th>
                    <th>yearly_sales</th>
                    <th>アクション</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($yearly_sales as $yearly_sale)
                  <tr>
                    <td>{{ $yearly_sale->year }}</td>
                    <td>{{ $yearly_sale->yearly_sales }}</td>
                    <td>
                      <a href="{{ route('yearly_sales.edit', $yearly_sale->id) }}" class="btn btn-primary">編集</a>
                      <form action="{{ route('yearly_sales.destroy', $yearly_sale->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">削除</button>
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