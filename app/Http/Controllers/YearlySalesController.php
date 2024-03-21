<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\yearly_sales;

class YearlySalesController extends Controller
{
    public function index()
    {
        $yearly_sales = yearly_sales::all()-> sortBy('year');
        

        return view('stores.yearly_sales.index', compact('yearly_sales'));
    }   

    public function create()
    {
        return view('stores.yearly_sales.create');
    }

    public function store(Request $request)
    {
        
        $validatedData = $request->validate([
            'year' => 'required',
            'yearly_sales' => 'required',
        ]);

        yearly_sales::create([
            'year' => $validatedData['year'],
            'yearly_sales' => $validatedData['yearly_sales'],
        ]);

        return redirect()->route('yearly_sales.index');
    }

    public function show($id)
    {
        $yearlySales = yearly_sales::find($id);
        return view('stores.yearly_sales.show', compact('yearlySales'));
    }

    public function edit($id)
    {
        
        $yearlySales = yearly_sales::find($id);
        return view('stores.yearly_sales.edit', compact('yearlySales'));
    }

    public function update(Request $request, $id)
    {

        $validatedData = $request->validate([
            'year' => 'required',
            'yearly_sales' => 'required',
        ]);

        $yearlySales = yearly_sales::find($id);

        $yearlySales->year = $validatedData['year'];
        $yearlySales->yearly_sales = $validatedData['yearly_sales'];

        $yearlySales->yearly_sales = $request->yearly_sales;
        $yearlySales->save();
        return redirect()->route('yearly_sales.index');
    }

    public function destroy($id)
    {
        $yearlySales = yearly_sales::find($id);
        $yearlySales->delete();
        return redirect()->route('yearly_sales.index');
    }


}


//

