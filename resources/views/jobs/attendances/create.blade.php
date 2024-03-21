<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      新規データ作成
    </h2>
  </x-slot>
  
  <div class="container mt-5">
    <div class="row">
      <div class="col-md-6 mx-auto">
        <form action="{{ route('attendances.store') }}" method="POST">
          @csrf
          
          <!-- 名前 -->
          <div class="mb-3">
            <label for="attendance_name" class="form-label">名前</label>
            <input type="text" class="form-control @error('attendance_name') is-invalid @enderror" id="attendance_name" name="attendance_name" required placeholder="田中太郎">
            @error('attendance_name')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <!-- 住所 -->
          <div class="mb-3">
            <label for="address" class="form-label">住所</label>
            <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" required>
            @error('address')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <!-- 電話番号 -->
          <div class="mb-3">
            <label for="phone_number" class="form-label">電話番号</label>
            <input type="text" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" required>
            @error('phone_number')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <!-- 役職 -->
          <div class="mb-3">
            <label for="position" class="form-label">役職</label>
            <select class="form-control @error('position') is-invalid @enderror" id="position" name="position" required>
              <option value="">選択してください</option>
              <option value="オーナー">オーナー</option>
              <option value="社員">社員</option>
            </select>
            @error('position')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <!-- 登録ボタン -->
          <button type="submit" class="btn btn-primary">登録</button>
        </form>
      </div>
    </div>
  </div>
</x-app-layout>
