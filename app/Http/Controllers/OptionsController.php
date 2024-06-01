<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\options;

class OptionsController extends Controller
{

    public function index()
    {
        try {
            $options = options::all();

            return response()->json([
                "resStatus" => "success",
                'options' => $options
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                'message' =>
                'オプションが見つかりません。       '
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {

            $validatedData = $request->validate([
                'option_name' => 'required',
                'price' => 'required',
            ]);
            $option =
                options::create([
                    'option_name' => $validatedData['option_name'],
                    'price' => $validatedData['price'],

                ]);

            return response()->json([
                "resStatus" => "success",
                "option" => $option
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                "message" => "オプションの作成に失敗しました。"
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $option = options::find($id);

            return response()->json([
                "resStatus" => "success",
                'option' => $option
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                'message' =>
                'オプションが見つかりません。'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'option_name' => 'required',
                'price' => 'required',
            ]);

            $option = options::find($id);

            $option->option_name = $validatedData['option_name'];
            $option->price = $validatedData['price'];

            $option->save();

            return response()->json(
                [
                    "resStatus" => "success",
                    "option" => $option
                ],
                200
            );
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                "message" => "オプションの更新に失敗しました。"
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $option = options::find($id);
            if (!$option) {
                return response()->json([
                    "resStatus" => "error",
                    'message' =>
                    'オプションが見つかりません。'
                ], 500);
            }

            $option->delete();
            return response()->json([
                "resStatus" => "success",
                'message' => 'オプションを削除しました。'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                'message' =>
                'オプションが見つかりません。'
            ], 500);
        }
    }
}
