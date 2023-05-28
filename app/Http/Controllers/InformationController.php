<?php

namespace App\Http\Controllers;

use App\Models\Information;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InformationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $information = Information::all();
        return response()->json([
            'data' => $information
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $validation = $request->validate([
            'title' => 'required|max:100',
            'content' => 'required',
            'category' => 'required',
            'image' => 'nullable',
            'attachment' => 'nullable'
        ]);

        $data = Information::create([
            'title' => $validation['title'],
            'content' => $validation['content'],
            'category' => $validation['category'],
            'image' => $validation['image'],
            'attachment' => $validation['attachment']
        ]);

        return response()->json([
            "message" => "Information added successfully",
            "data" => $data
        ], 201);
    }

    public function markAsRead(Request $request) 
    {
        $information = Information::findOrFail($request['informationId']);
        $readBy = $information->read_by ?? [];


        if (!in_array(auth()->user()->id, $readBy)) {
            $readBy[] = auth()->user()->id;
            $information->read_by = $readBy;
            $information->save();
        }

        return response()->json([
            'message' => 'Informasi berhasil ditandai sebagai telah dibaca'
        ], 200);
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
    public function show(string $id)
    {
        //
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
    public function update(Request $request)
    {
        $validation = $request->validate([
            'informationId' => 'required',
            'title' => 'required|max:100',
            'content' => 'required',
            'category' => 'required',
            'image' => 'nullable',
            'attachment' => 'nullable'
        ]);

        $information = Information::where('informationId', $validation['informationId'])->first();

        $information->update([
            'title' => $validation['title'],
            'content' => $validation['content'],
            'category' => $validation['category'],
            'image' => $validation['image'],
            'attachment' => $validation['attachment']
        ]);

        return response()->json([
            'message'=> 'Information has been successfully edit.',
            'data' => $information
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'informationId' => 'required'
        ]);

        $data = Information::where('informationId', $request['informationId'])->first();
        $data->forceDelete();
        
        return response()->json([
            'message'=> 'Information has been successfully deleted.',
        ], 201);
    }

    public function latestInfo()
    {
        $date = Carbon::today()->subDays(7);

        $information = Information::whereDate('created_at', '>=', $date)->get();

        return response()->json([
            'message' => 'Latest Information',
            'data' => $information
        ], 200);
    }
}
