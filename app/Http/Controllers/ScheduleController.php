<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Blocktrail\CryptoJSAES\CryptoJSAES;


class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // get and show all schedule data
        $schedule = Schedule::all();
        return response()->json([
            'data' => $schedule
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // validate the request from user
        $validation = $request->validate([
            'activity_name' => 'required',
            'date' => 'required',
            'location' => 'required|min:4|max:16',
            'start_time' => 'required',
            'end_time' => 'required',
            'users' => 'required'
        ]);

        // do encryption for attendance code
        $text = $validation['activity_name'] . $validation['date'] . $validation['location'];
        $key = 'akrap hor';
        $attendanceCode = CryptoJSAES::encrypt($text, $key);
        
        // save schedule to database
        $data = Schedule::create([
            'activity_name' => $validation['activity_name'],
            'date' => $validation['date'],
            'location' => $validation['location'],
            'start_time' => $validation['start_time'],
            'end_time' => $validation['end_time'],
            'attendance_code' => $attendanceCode
        ]);

        
        $createPresences = new PresenceController;
        $createPresences->create($data['scheduleId'], $data['activity_name'], $data['date'], $data['location'], $validation['users']);

        // return response success
        return response()->json([
            "message" => "Schedule added successfully",
            "data" => $data
        ], 201);
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
