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
        try {
            $hairstyles = hairstyles::all();

            if ($hairstyles->isEmpty()) {
                return response()->json([
                    "resStatus" => "success",
                    "message" => "初めまして！新規作成ボタンから使用するヘアスタイルを作成しましょう！",
                    'hairstyles' => $hairstyles
                ], 200);
            } else {
                return response()->json([
                    "resStatus" => "success",
                    'hairstyles' => $hairstyles
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                'message' => "ヘアスタイルが見つかりませんでした。"
            ], 500);
        }
    }


    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'hairstyle_name' => 'required',
            ]);

            $hairstyle = hairstyles::create([
                'hairstyle_name' => $validatedData['hairstyle_name'],
            ]);

            return response()->json([
                "resStatus" => "success",
                "hairstyle" => $hairstyle
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                "message" => "ヘアスタイルの作成に失敗しました。"
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $hairstyle = hairstyles::find($id);
            if (!$hairstyle) {
                return response()->json([
                    'resStatus' => 'error',
                    'message' => 'ヘアスタイルが見つかりません。'
                ], 404);
            }

            return response()->json([
                "resStatus" => "success",
                'hairstyle' => $hairstyle
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                'message' => 'ヘアスタイルが見つかりません。'
            ], 500);
        }
    }



    public function update(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'hairstyle_name' => 'required',
            ]);

            $hairstyle = hairstyles::find($id);
            $hairstyle->hairstyle_name = $validatedData['hairstyle_name'];

            $hairstyle->save();

            return response()->json(
                [
                    "resStatus" => "success",
                    "hairstyle" => $hairstyle
                ],
                200
            );
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                "message" => "ヘアスタイルの更新に失敗しました。"
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $hairstyle = hairstyles::find($id);
            if (!$hairstyle) {
                return response()->json([
                    "resStatus" => "error",
                    'message' => 'ヘアスタイルが見つかりません。'
                ], 500);
            }
            $hairstyle->delete();
            return response()->json([
                "resStatus" => "success",
                "deleteId" => $id
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                'message' => 'ヘアスタイルの削除に失敗しました。'
            ], 500);
        }
    }
}
