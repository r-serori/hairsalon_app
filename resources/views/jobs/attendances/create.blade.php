<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      新規データ作成
    </h2>
  </x-slot>
  
  <div class="container mt-5">
    <div class="row">
      <div class="col-md-6 mx-auto">
        <form action="{{ route('attendance.store') }}" method="POST">
          @csrf
          
          <!-- 名前 -->
          <div class="mb-3">
            <label for="name" class="form-label">名前</label>
            <input type="text" class="form-control" id="name" name="name" required>
          </div>
          
          <!-- 住所 -->
          <div class="mb-3">
            <label for="address" class="form-label">住所</label>
            <input type="text" class="form-control" id="address" name="address" required>
          </div>
          
          <!-- 電話番号 -->
          <div class="mb-3">
            <label for="phone_number" class="form-label">電話番号</label>
            <input type="text" class="form-control" id="phone_number" name="phone_number" required>
          </div>
          
          <!-- 役職 -->
          <div class="mb-3">
            <label for="position" class="form-label">役職</label>
            <select class="form-control" id="position" name="position" required>
              <option value="オーナー">オーナー</option>
              <option value="社員">社員</option>
            </select>
          </div>
          
          <!-- 登録ボタン -->
          <button type="submit" class="btn btn-primary">登録</button>
        </form>
      </div>
    </div>
  </div>
</x-app-layout>
                    

