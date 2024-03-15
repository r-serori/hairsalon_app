<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                勤怠管理画面
            </h2>
            <a href="{{ route('attendances.create') }}" class="btn btn-primary">新規作成</a>
           
        </div>
    </x-slot>

    <div class="container mt-5">
        <div class="row">
            <div class="col mx-auto">
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">名前</th>
                            <th scope="col">電話番号</th>
                            <th scope="col">役職</th>
                            <th scope="col">住所</th>
                            <th scope="col" colspan="3">アクション</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        <!-- ここにデータを動的に挿入 -->
                        @foreach($attendances as $data)

                        <tr>
                    
                            <td>{{ $data->attendance_name }}</td>
                            <td>{{ $data->phone_number }}</td>
                            <td>{{ $data->position }}</td>
                            <td>{{ $data->address }}</td>

                            
                            <td><a href="{{ route('attendances.edit', $data->id) }}" class="btn btn-success">編集</a></td>
                            <td><a href="{{ route('attendances.show', $data->id) }}" class="btn btn-success">詳細</a></td>
                          



                            

                        </tr>
                        @endforeach
                        <!-- データの追加 -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>