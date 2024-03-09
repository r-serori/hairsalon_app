<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                勤怠管理画面
            </h2>
           
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
             
                        </tr>
                    </thead>
                    <tbody>
                        
                        <!-- ここにデータを動的に挿入 -->

                        <tr>
                    
                            <td>{{ $attendance->name }}</td>
                            <td>{{ $attendance->phone_number }}</td>
                            <td>{{ $attendance->position }}</td>
                            <td>{{ $attendance->address }}</td>

                            <form action="{{ route('attendance.destroy', $attendance->id) }}" method="POST" >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">削除</button>
                                </form>
                        

                        </tr>
                  
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>