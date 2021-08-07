<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use \Illuminate\Database\QueryException;
use App\Models\Diary;

class DiaryController extends Controller
{
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2',
        ]);
        if ($validator->fails()) {
            return response([
                'success' => false,
                'message' => $validator->errors()
            ], 400);
        }
        try {
            Diary::create([
                'user_id' => auth()->user()->id,
                'name' => $request['name']
            ]);

            return response([
                'success' => true,
                'message' => 'Diary is Created Successfully'
            ], 201);
        } catch (QueryException $exception) {
            return response([
                'success' => false,
                'message' => $exception->errorInfo
            ], 500);
        }
    }
    public function show()
    {
        try {
            $diaries = Diary::where('user_id', auth()->user()->id)->orderBy('id')->get();
            return response([
                'success' => true,
                'diaries' => $diaries,
                'message' => count($diaries) . ' Diary Found.'
            ], 201);
        } catch (QueryException $exception) {
            return response([
                'success' => false,
                'message' => $exception->errorInfo
            ], 500);
        }
    }
}
