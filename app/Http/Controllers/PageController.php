<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use \Illuminate\Database\QueryException;
use App\Models\Diary;
use App\Models\Page;

class PageController extends Controller
{
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'diary_id' => 'required|numeric',
            'content' => 'required|string|min:10',
        ]);
        if ($validator->fails()) {
            return response([
                'success' => false,
                'message' => $validator->errors()
            ], 400);
        }
        try {
            $diary = Diary::find($request['diary_id']);
            if (!empty($diary)) {
                if (auth()->user()->id === $diary->user_id) {
                    Page::create([
                        'diary_id' => $request['diary_id'],
                        'content' => $request['content']
                    ]);

                    return response([
                        'success' => true,
                        'message' => 'Page is Saved Successfully.'
                    ], 201);
                } else {
                    return response([
                        'success' => false,
                        'message' => 'The diary is not belongs to the user.'
                    ], 400);
                }
            } else {
                return response([
                    'success' => false,
                    'message' => 'No Diary Found.'
                ], 400);
            }
        } catch (QueryException $exception) {
            return response([
                'success' => false,
                'message' => $exception->errorInfo
            ], 500);
        }
    }
    public function show(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'diary_id' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return response([
                'success' => false,
                'message' => $validator->errors()
            ], 400);
        }
        try {
            /* SELECT * FROM `pages` as a 
                INNER JOIN diaries as b ON a.diary_id = b.id
                WHERE a.diary_id = 1 AND b.user_id = 4 */

            $pages = Page::where('diary_id', $request['diary_id'])
                ->where('diaries.user_id', auth()->user()->id)
                ->join('diaries', 'diaries.id', '=', 'pages.diary_id')
                ->select('pages.*')
                ->orderBy('pages.id')
                ->get();
            return response([
                'success' => true,
                'pages' => $pages,
                'message' => count($pages) . ' Pages Found.'
            ], 200);
        } catch (QueryException $exception) {
            return response([
                'success' => false,
                'message' => $exception->errorInfo
            ], 500);
        }
    }
}
