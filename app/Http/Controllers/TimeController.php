<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Time;

class TimeController extends Controller
{
    private $time;

    public function __construct(Time $time)
    {
        $this->time = $time;
    }

    public function index(Request $request)
    {
        $timeGet = $this->time->getResults($request->nome,$request->id);
        return response()->json($timeGet);
    }

    public function store(Request $request)
    {
        $time = $this->time->create($request->all());
        return response()->json($time, 201);
    }

    public function update(Request $request, $id)
    {
        $time = $this->time->find($id);
        if (!$time)
            return response()->json(['error' => 'Not Found'], 404);
        $time->update($request->all());
        return response()->json($time);
    }

    public function destroy($id)
    {
        if (!$time = $this->time->find($id))
            return response()->json(['error' => 'Not Found'], 404);

        $time->delete();
        return response()->json(['sucess' => true], 204);
    }
}
