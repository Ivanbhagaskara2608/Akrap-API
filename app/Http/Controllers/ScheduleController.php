<?php

namespace App\Http\Controllers;

use App\Models\Presence;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Blocktrail\CryptoJSAES\CryptoJSAES;
use Illuminate\Support\Facades\Auth;

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
        $createPresences->create($data['scheduleId'], $validation['users']);

        // Get the ID of the newly created schedule
        $scheduleId = $data->scheduleId;

        // Retrieve the newly created schedule from the database
        $newlyCreatedSchedule = Schedule::find($scheduleId);

        // return response success
        return response()->json([
            "message" => "Schedule added successfully",
            "data" => $newlyCreatedSchedule
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
        } elseif ($schedule->status == 'unavailable') {
            return response()->json([
                'message'=>'The schedule is over!'
            ], 400);
        } else {
            // get presences data 
            $presences = Presence::where('scheduleId', $request['scheduleId'])->get();
            // check if there is data that has empty status, then set status to "Alpha"
            foreach ($presences as $presence) {
                if (!$presence->status) {
                    $presence->status = 'Alpha';
                    $presence->save();
                }
            }

            $schedule->status = 'unavailable';
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
        $schedule = Schedule::where('status', 'available')->get();

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
        $schedule = Schedule::where('status', "unavailable")->get();

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
        } elseif ($schedule->status == 'unavailable') {
            return response()->json([
                'message'=> 'You can not update the past schedule'
            ], 400);
        } else {
            $text = $schedule['activity_name'] . $request['date'];
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

        foreach ($request['scheduleId'] as $schedule) {
            $data = Schedule::where('scheduleId', $schedule)->first();
            $data->forceDelete();
        }
        
        return response()->json([
            'message'=> 'Schedule has been successfully deleted.',
        ], 201);
    }

    public function todaySchedule()
    {
        // initiation auth user
        $user = Auth::user();
        $date = Carbon::today()->toDateString();

        $data = Schedule::where('date', $date)->get();
        
        // get all values 'scheduleId' from data
        $scheduleIds = $data->pluck('scheduleId');

        $presences = Presence::whereIn('scheduleId', $scheduleIds)
                    ->where('userId', $user->userId)
                    ->get();

        $scheduleIds = $presences->pluck('scheduleId');

        $schedules = Schedule::whereIn('scheduleId', $scheduleIds)->get();

        if ($schedules->count() > 0) {
            return response()->json([
                "message" => "Today schedule found",
                'data'=> $schedules
            ], 201);
        } else {
            return response()->json([
                "message" => "Schedules are empty",
                "data" => []
            ], 404);
        }

    }
}
