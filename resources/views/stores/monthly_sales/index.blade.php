<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      {{ __('日次売り上げ一覧') }}
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
    <a href="{{ route('monthly_sales.create') }}" class="btn btn-primary">新規作成</a>
  </div>

  <!-- 年と月の入力フォーム -->
  <form action="{{ route('monthly_sales.updateYearlySales') }}" method="POST" class="mt-4 ml-auto mr-4">
    @csrf
    <div class="form-group row">
      <label for="year" class="col-sm-2 col-form-label">年：</label>
      <div class="col-sm-2">
        <input type="text" class="form-control" id="year" name="year" placeholder="年を入力">
      </div>
      <div class="col-sm-2">
        <button type="submit" class="btn btn-primary">年次売り上げを更新</button>
      </div>
      @error('year')
      <p class="text-red-500 text-xs italic">{{ $message }}</p>
      @enderror

    </div>
  </form>

  @if(session('success'))
  <div class="alert alert-success">
    {{ session('success') }}
  </div>
  @endif
  

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
                    <th>month</th>
                    <th>monthly_sales</th>
                    <th>アクション</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($monthly_sales as $monthly_sale)
                  <tr>
                    <td>{{ $monthly_sale->year }}</td>
                    <td>{{ $monthly_sale->month }}</td>
                    <td>{{ $monthly_sale->monthly_sales }}</td>
                    <td>
                      <a href="{{ route('monthly_sales.edit', $monthly_sale->id) }}" class="btn btn-primary">編集</a>
                      <form action="{{ route('monthly_sales.destroy', $monthly_sale->id) }}" method="POST">
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