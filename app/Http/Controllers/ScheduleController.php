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
    public function store($scheduleId)
    {
        $schedule = Schedule::where('scheduleId', $scheduleId)->first();

        $schedule->status = '0';
        $schedule->save();

        // return response success
        return response()->json([
            'message'=> 'The Schedule is set to finished',
            'data' => $schedule
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        // get and show active schedule data
        $schedule = Schedule::where('status', '1')->get();

        if($schedule->isEmpty()) {
            return response()->json([
                "message" => "Schedule is empty",
                "data" => []
            ], 404);
        } else {
            return response()->json([
                'data' => $schedule
            ], 200);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $scheduleId)
    {
        $request->validate([
            'date' => 'required',
            'location' => 'required|min:4|max:16',
            'start_time' => 'required',
            'end_time' => 'required'
         ]);

         // get data schedule from db
        $schedule = Schedule::where('scheduleId', $scheduleId)->first();
        
        $schedule->update([
            'date' => $request['date'],
            'location' => $request['location'],
            'start_time' => $request['start_time'],
            'end_time' => $request['end_time']
        ]);

        // return response success
        return response()->json([
            'message'=> 'Schedule has been successfully edit.',
            'data' => $schedule
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($scheduleId)
    {
        $schedule = Schedule::where('scheduleId', $scheduleId)->first();
        
        $schedule->forceDelete();

        return response()->json([
            'message'=> 'Schedule has been successfully deleted.',
        ], 201);
    }
}
