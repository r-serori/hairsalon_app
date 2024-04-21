<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\merchandises;

class MerchandisesController extends Controller
{

    public function index()
    {
        $merchandises = merchandises::all();
        return response()->json(['merchandises' => $merchandises]);
    }


    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'merchandise_name' => 'required',
            'price' => 'required',
        ]);

        merchandises::create([
            'merchandise_name' => $validatedData['merchandise_name'],
            'price' => $validatedData['price'],

        ]);

        return response()->json([], 204);
    }

    public function show($id)
    {
        $merchandise = merchandises::find($id);

        return response()->json(['merchandise' => $merchandise]);
    }


    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'merchandise_name' => 'required',
            'price' => 'required',
        ]);

        $merchandise = merchandises::find($id);


        $merchandise->merchandise_name = $validatedData['merchandise_name'];
        $merchandise->price = $validatedData['price'];

        $merchandise->save();


        return response()->json(
            [],
            204
        );
    }

    public function destroy($id)
    {
        $merchandise = merchandises::find($id);
        if (!$merchandise) {
            return response()->json(['message' =>
            'merchandise not found'], 404);
        }

        try {
            $merchandise->delete();
            return response()->json([], 204);
        } catch (\Exception $e) {
            return response()->json(['message' =>
            'merchandise cannot be deleted'], 500);
        }
    }
}
