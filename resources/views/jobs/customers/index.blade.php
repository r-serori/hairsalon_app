<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      {{ __('顧客管理') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="container">
      <div class="row justify-content-between mb-4">
        <div class="col-md-6">
          <h3 class="font-semibold text-lg">{{ __('顧客一覧') }}</h3>
        </div>


        <div class="col-md-6 text-right">
        <form action="{{ route('customers.index') }}" method="GET">
      <div class="input-group">
        <input type="text" name="search" class="form-control" placeholder="{{ __('名前で検索') }}">
        <div class="input-group-append">
          <button class="btn btn-primary" type="submit">{{ __('検索') }}</button>
        </div>
      </div>
    </form>
          <a href="{{ route('customers.create') }}" class="btn btn-primary">{{ __('新規作成') }}</a>
        </div>
      </div>
      <div class="row justify-content-center">
        <div class="col-md-10">
          <div class="card">
            <div class="card-body">
              <table class="table">
                <thead>
                  <tr>
                    <th>名前</th>
                    <th>電話番号</th>
                    <th>備考</th>
                    <th>髪型</th>
                    <th>コース名</th>
                    <th>オプション名</th>
                    <th>物販</th>
                    <th>担当者</th>
                    <th>新規or既存</th>
                    <th>アクション</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($customers as $customer)
                  <tr>
                    <td>{{ $customer->customer_name }}</td>
                    <td>{{ $customer->phone_number }}</td>
                    <td>{{ $customer->remarks }}</td>

                    <td>
                      @foreach ($customer->hairstyles as $hairstyle)
                      {{ $hairstyle->hairstyle_name }},
                      @endforeach
                    </td>
                  
                    <td>
                      @foreach ($customer->courses as $course)
                      {{ $course->course_name }},
                      @endforeach
                    </td>

                    <td>
                      @foreach ($customer->options as $option)
                      {{ $option->option_name }},
                      @endforeach
                    </td>


                    <td>
                      @foreach ($customer->merchandises as $merchandise)
                      {{ $merchandise->merchandise_name }},
                      @endforeach

                    </td>
                    <td>
                      @foreach ($customer->attendances as $attendance)
                      {{ $attendance->attendance_name }},
                      @endforeach

                    </td>
                 
                   


                    <td>{{ $customer->new_customer ? '新規' : '既存' }}</td>
                    <td>
                      <a href="{{ route('customers.show', $customer->id) }}" class="btn btn-info btn-sm">{{ __('詳細') }}</a>
                      <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-warning btn-sm">{{ __('編集') }}</a>
                      <form method="POST" action="{{ route('customers.destroy', [$customer->id]) }}" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">{{ __('削除') }}</button>
                      </form>
                      <a href="{{ route('customers.scheduleCreate', [$customer->id]) }}" class="btn btn-warning btn-sm">{{ __('予約') }}</a>
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

  <!-- ページネーションを表示 -->
{{ $customers->links() }}

</x-app-layout>