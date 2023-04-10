<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use Carbon\Carbon;
class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $validation = $request->validate([
            'activity_name' => 'required',
            'date' => 'required',
            'location' => 'required|min:4|max:16',
            'start_time' => 'required',
            'end_time' => 'required'
        ]);

        $data = Schedule::create([
            'activity_name' => $validation['activity_name'],
            'date' => $validation['date'],
            'location' => $validation['location'],
            'start_time' => $validation['start_time'],
            'end_time' => $validation['end_time']
        ]);

        return response()->json([
            "message" => "Schedule added successfully",
            "data" => $data
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        $schedule = Schedule::all();
        return response()->json([
            'data' => $schedule
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
