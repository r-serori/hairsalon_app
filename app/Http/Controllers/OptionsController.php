<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\options;

class OptionsController extends Controller
{

    public function index()
    {
        $options = options::all();

        return response()->json(['options' => $options]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'option_name' => 'required',
            'price' => 'required',
        ]);

        options::create([
            'option_name' => $validatedData['option_name'],
            'price' => $validatedData['price'],

        ]);

        return response()->json([], 204);
    }

    public function show($id)
    {
        $option = options::find($id);

        return response()->json(['option' => $option]);
    }

    public function edit($id)
    {
        $option = options::find($id);
        if (!$option) {
            return response()->json(['message' =>
            'option not found'], 404);
        }

        return response()->json(['option' => $option]);
    }


    public function update(Request $request, $id)
    {

        $validatedData = $request->validate([
            'option_name' => 'required',
            'price' => 'required',
        ]);

        $option = options::find($id);

        $option->option_name = $validatedData['option_name'];
        $option->price = $validatedData['price'];

        $option->save();

        return response()->json(
            [],
            204
        );
    }

    public function destroy($id)
    {
        $option = options::find($id);
        if (!$option) {
            return response()->json(['message' =>
            'option not found'], 404);
        }

        try {
            $option->delete();
            return response()->json([], 204);
        } catch (\Exception $e) {
            return response()->json(['message' =>
            'option is used in another table'], 409);
        }
    }
}
