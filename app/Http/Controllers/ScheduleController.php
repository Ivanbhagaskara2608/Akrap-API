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
            'date' => 'required|after:yesterday',
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
        $request->validate([
            'scheduleId' => 'required'
        ]);

        $schedule = Schedule::where('scheduleId', $request['scheduleId'])->first();

        if (!$schedule) {
            return response()->json([
                'message'=>'There is no schedule!'
            ], 400);
        } elseif ($schedule->status == '0') {
            return response()->json([
                'message'=>'The schedule is over!'
            ], 400);
        } else {
            $schedule->status = '0';
            $schedule->save();

            // return response success
            return response()->json([
                'message'=> 'The Schedule is set to over',
                'data' => $schedule
            ], 201);   
        }
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        // get and show recent schedule data
        $schedule = Schedule::where('status', '1')->get();

        if($schedule->isEmpty()) {
            return response()->json([
                "message" => "Schedule is empty",
                "data" => []
            ], 404);
        } else {
            return response()->json([
                "message" => "Recent schedules found",
                'data' => $schedule
            ], 200);
        }
    }

    public function showPast()
    {
        // get and show past schedule data
        $schedule = Schedule::where('status', '0')->get();

        if($schedule->isEmpty()) {
            return response()->json([
                "message" => "Schedule is empty",
                "data" => []
            ], 404);
        } else {
            return response()->json([
                "message" => "Past schedules found",
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
    public function update(Request $request)
    {
        $request->validate([
            'scheduleId' => 'required',
            'date' => 'required|after:yesterday',
            'location' => 'required|min:4|max:16',
            'start_time' => 'required',
            'end_time' => 'required',
         ]);

         // get data schedule from db
        $schedule = Schedule::where('scheduleId', $request['scheduleId'])->first();

        if (!$schedule) {
            return response()->json([
                'message'=>'There is no schedule!'
            ], 400);
        } elseif ($schedule->status == '0') {
            return response()->json([
                'message'=> 'You can not update the past schedule'
            ], 400);
        } else {
            $text = $schedule['activity_name'] . $request['date'] . $request['location'];
            $key = 'akrap hor';
            $attendanceCode = CryptoJSAES::encrypt($text, $key);
            
            $schedule->update([
                'date' => $request['date'],
                'location' => $request['location'],
                'start_time' => $request['start_time'],
                'end_time' => $request['end_time'],
                'attendance_code' => $attendanceCode
            ]);

            // return response success
            return response()->json([
                'message'=> 'Schedule has been successfully edit.',
                'data' => $schedule
            ], 201);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'scheduleId' => 'required'
        ]);

        $schedule = Schedule::where('scheduleId', $request['scheduleId'])->first();
        
        if (!$schedule) {
            return response()->json([
                'message'=>'There is no schedule!'
            ], 400);
        } else {
            $schedule->forceDelete();

            return response()->json([
                'message'=> 'Schedule has been successfully deleted.',
            ], 201);
        }
    }
}
