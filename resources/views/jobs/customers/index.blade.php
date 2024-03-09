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
                    <th>特徴</th>
                    <th>髪型</th>
                    <th>コース名</th>
                    <th>オプション名</th>
                    <th>物販</th>
                    <th>アクション</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($customers as $customer)
                  <tr>
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->phone_number }}</td>
                    <td>{{ $customer->features }}</td>
                    <td>{{ $customer->hairstyle ? $customer->hairstyle->hairstyle_name : '-' }}</td>
                    <td>{{ $customer->course ? $customer->course->course_name : '-' }}</td>
                    <td>{{ $customer->option ? $customer->option->option_name : '-' }}</td>
                    <td>{{ $customer->merchandise ? $customer->merchandise->merchandise_name : '-' }}</td>

                    <td>
                      <a href="{{ route('customers.show', $customer->id) }}" class="btn btn-info btn-sm">{{ __('詳細') }}</a>
                      <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-warning btn-sm">{{ __('編集') }}</a>
                      <form method="POST" action="{{ route('customers.destroy', $customer->id) }}" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">{{ __('削除') }}</button>
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