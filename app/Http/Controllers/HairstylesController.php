<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\hairstyles;

class HairstylesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $hairstyles = hairstyles::all();
        return response()->json(['hairstyles' => $hairstyles]);
    }


    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'hairstyle_name' => 'required',
        ]);

        hairstyles::create([
            'hairstyle_name' => $validatedData['hairstyle_name'],
        ]);

        return response()->json([], 204);
    }


    public function show($id)
    {
        $hairstyle = \App\Models\hairstyles::find($id);
        return response()->json(['hairstyle' => $hairstyle]);
    }


    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'hairstyle_name' => 'required',
        ]);

        $hairstyle = hairstyles::find($id);
        $hairstyle->hairstyle_name = $validatedData['hairstyle_name'];

        $hairstyle->save();

        return response()->json(
            [],
            204
        );
    }

    public function destroy($id)
    {
        $hairstyle = hairstyles::find($id);
        if (!$hairstyle) {
            return response()->json(['message' => 'hairstyle not found'], 404);
        }

        try {
            $hairstyle->delete();
            return response()->json([], 204);
        } catch (\Exception $e) {
            return response()->json(['message' => 'hairstyle cannot be deleted'], 400);
        }
    }
}
