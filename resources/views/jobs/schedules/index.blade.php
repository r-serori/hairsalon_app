<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      {{ __('予約管理') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              {{ __('予約表') }}
            </div>
            <div class="card-body">
              <table class="table">
                <thead>
                  <tr>
                    <th scope="col">時間</th>
                    <th scope="col">月曜日</th>
                    <th scope="col">火曜日</th>
                    <th scope="col">水曜日</th>
                    <th scope="col">木曜日</th>
                    <th scope="col">金曜日</th>
                    <th scope="col">土曜日</th>
                    <th scope="col">日曜日</th>
                  </tr>
                </thead>
                <tbody>
         
      
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>