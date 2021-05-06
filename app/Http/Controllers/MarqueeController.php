<?php

namespace App\Http\Controllers;

use App\Models\Marquee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MarqueeController extends Controller
{
    public function index()
    {
        $marquee = Marquee::simplePaginate(15);

        return response()->json([
            'status_code' => 200,
            'result' => $marquee
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'display' =>['required'],
            'forever' => ['required', 'in:1,2'],
            'title'       => ['required', 'max:20'],
            'description' => ['required', 'max:500'],
            'start_at'    => ['bail', 'required', 'date_format:Y-m-d H:i:s', 'after_or_equal:' . date("Y-m-d H:i:s", strtotime('-2 minute'))],
            'end_at'      => ['bail', 'required_if:forever,==,2', 'date_format:Y-m-d H:i:s', 'after:start_at']
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'status_code' => 422,
                'error' => $validator->errors()
            ]);
        }

        Marquee::create($request->all());

        return response()->json([
            'status_code' => 200,
            'result' => 'true'
        ]);
    }

    public function update(int $id, Request $request)
    {
        $request['id'] = $id;

        $validator = Validator::make($request->all(), [
            'id'          => ['required','exists:marquees,id'],
            'display' =>['required'],
            'forever' => ['required', 'in:1,2'],
            'title'       => ['required', 'max:20'],
            'description' => ['required', 'max:500'],
            'start_at'    => ['bail', 'required', 'date_format:Y-m-d H:i:s', 'after_or_equal:' . date("Y-m-d H:i:s", strtotime('-2 minute'))],
            'end_at'      => ['bail', 'required_if:forever,==,2', 'date_format:Y-m-d H:i:s', 'after:start_at']
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'status_code' => 422,
                'error' => $validator->errors()
            ]);
        }

        $params = $request->except('id');
        Marquee::find($id)->update($params);

        return response()->json([
            'status_code' => 200,
            'result' => 'true'
        ]);
    }

    public function destroy(int $id, Request $request)
    {
        $request['id'] = $id;
        $validator = Validator::make($request->all(), [
            'id'          => ['required','exists:marquees,id'],
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'status_code' => 422,
                'error' => $validator->errors()
            ]);
        }

        Marquee::find($id)->delete();

        return response()->json([
            'status_code' => 200,
            'result' => 'true'
        ]);
    }
}
