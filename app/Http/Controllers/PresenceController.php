<?php

namespace App\Http\Controllers;

use App\Models\Presence;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PresenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {   
        // initiation auth user
        $user = Auth::user();
        // get all presences data from database by userId
        $presencesUser = Presence::where('userId', $user->userId)->get();

        // return response if there's no record in database
        if($presencesUser->isEmpty()) {
            return response()->json([
                "message" => "History presences this user is empty",
                "data" => []
            ], 404);
        } else {
            // return response success get data presences
            return response()->json([
                "message" => "History presences user found",
                "data" => $presencesUser
            ], 200);
        }

    }

    /**
     * Show the form for creating a new resource.
     */
    public function presence(Request $request)
    {
        // validate user request
        $validation = $request->validate([
            'attendance_code' => 'required' 
        ]);
        
        // initiation auth user
        $user = Auth::user();
        // get data schedule by attendace code
        $schedule = Schedule::where('attendance_code', $validation['attendance_code'])->first();
        // check attendance code valid or not
        if (!$schedule) {
            return response()->json([
                'message'=>'invalid Attendance code!'
            ], 400);
        } 
        // get data presence 
        $presence = Presence::where('scheduleId', $schedule->scheduleId)
                            ->where('userId', $user->userId)
                            ->first();

        // return response if user already presence
        if($presence && $presence->status == 'Hadir') {
            return response()->json([
                'message' => 'You have already taken attendance'
            ], 400);
        }

        // get time using Carbon
        $timeNow = Carbon::now();
        // parsing schedule date to date string
        $date = Carbon::parse($schedule->date)->toDateString();
        // merge date and time so that it can be used
        $startTime = $date . " " . $schedule->start_time;
        $endTime = $date . " " . $schedule->end_time;

        // return response if user make attendance not in the set time
        if(!$timeNow->between($startTime, $endTime)) {
            return response()->json([
                'message' => 'Sorry, you cannot take attendance because this is not the time for attendance.'
            ], 400);
        }
        
        // update status presence
        $presence->status = 'Hadir';
        $presence->save();

        return response()->json([
            'message'=> 'Attendance has been successfully recorded.',
            'data' => $presence
        ], 201);
    }
    public function create($scheduleId, $activity_name, $date, $location, $users)
    {
        foreach ($users as $user) {
            Presence::create([
                'scheduleId' => $scheduleId,
                'userId' => $user,
                'activity_name' => $activity_name,
                'date' => $date,
                'location' => $location,
                'status' => ''
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($scheduleId)
    {
        // get presences data 
        $presences = Presence::where('scheduleId', $scheduleId)->get();

        // check if there is data that has empty status, then set status to "Alpha"
        foreach ($presences as $presence) {
            if ($presence->status == '') {
                $presence->status = 'Alpha';
                $presence->save();
            }
        }
        
        // return response success
        return response()->json([
            'message'=> 'Attendance data has been successfully saved.',
            'data' => $presence
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($scheduleId)
    {
        // get all presences data from database by scheduleId
        $presences = Presence::where('scheduleId', $scheduleId)->get();

        // return response if there's no record in database
        if ($presences->isEmpty()) {
            return response()->json([
                "message" => "Presences is empty",
                "data" => []
            ], 404);
        };

        // return response success get data presences
        return response()->json([
            "message" => "Presences found",
            "data" => $presences
        ], 200);
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
    public function update(Request $request, $presenceId)
    {
        // get data presence from db
        $presence = Presence::where('scheduleId', $presenceId)->first();

        // set the status and save
        $presence->status = $request['status'];
        $presence->save();
 
        // return response success
        return response()->json([
            'message'=> 'Attendance data has been successfully edit.',
            'data' => $presence
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
