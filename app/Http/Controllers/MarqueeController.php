<?php

namespace App\Http\Controllers;

use App\Http\Requests\MarqueeDestoryRequest;
use App\Http\Requests\MarqueeRequest;
use App\Http\Requests\MarqueeStoreRequest;
use App\Http\Requests\MarqueeUpdateRequest;
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

    public function store(MarqueeRequest $request)
    {
        Marquee::create($request->all());

        return response()->json([
            'status_code' => 200,
            'result' => 'true'
        ]);
    }

    public function update(MarqueeRequest $request)
    {
        $params = $request->except('id');
        Marquee::find($request->input('id'))->update($params);

        return response()->json([
            'status_code' => 200,
            'result' => 'true'
        ]);
    }

    public function destroy(MarqueeRequest $request)
    {
        Marquee::find($request->input('id'))->delete();

        return response()->json([
            'status_code' => 200,
            'result' => 'true'
        ]);
    }
}
