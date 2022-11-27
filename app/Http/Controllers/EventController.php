<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $events = Event::all();
        if ($events->isEmpty()) {
            return response()->json([
                'status' => 200,
                'message' => 'Current table is empty.'
            ]);
        }
        else {
            return response()->json([
                'status' => 200,
                'message' => 'Data fetched successfully.',
                'data' => $events,
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'string|max:255',
            'description' => 'string',
            'tnc' => 'string',
            'latitude' => 'numeric',
            'longitude' => 'numeric',
            'date_start' => 'date',
            'date_end' => 'date',
            'quota' => 'numeric',
            'price' => 'numeric',
            'host_id' => 'numeric'
        ]);
        Event::create([
            'title' => $request->title,
            'description' => $request->description,
            'tnc' => $request->tnc,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'date_start' => $request->date_start,
            'date_end' => $request->date_end,
            'quota' => $request->quota,
            'sold' => 0,
            'price' => $request->price,
            'host_id' => $request->host_id
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Data created successfully.'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $event = Event::find($id);
        if ($event == null) {
            return response()->json([
                'status' => 400,
                'message' => 'There is no event with the id of '.$id
            ]);
        }
        else {
            return response()->json([
                'status' => 200,
                'message' => 'Data fetched successfully.',
                'data' => $event,
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function edit(Event $event)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'string|max:255|nullable',
            'description' => 'string|nullable',
            'tnc' => 'string|nullable',
            'latitude' => 'numeric|nullable',
            'longitude' => 'numeric|nullable',
            'date_start' => 'date|nullable',
            'date_end' => 'date|nullable',
            'quota' => 'numeric|nullable',
            'price' => 'numeric|nullable'
        ]);
        $event = Event::find($id);
        if ($event == null) {
            return response()->json([
                'status' => 400,
                'message' => 'There is no event with the id of '.$id
            ]);
        }
        else {
            $event->title = $request->title ? $request->title : $event->title;
            $event->description = $request->description ? $request->description : $event->description;
            $event->tnc = $request->tnc ? $request->tnc : $event->tnc;
            $event->latitude = $request->latitude ? $request->latitude : $event->latitude;
            $event->longitude = $request->longitude ? $request->longitude : $event->longitude;
            $event->date_start = $request->date_start ? $request->date_start : $event->date_start;
            $event->date_end = $request->date_end ? $request->date_end : $event->date_end;
            $event->quota = $request->quota ? $request->quota : $event->quota;
            $event->price = $request->price ? $request->price : $event->price;
            $event->save();

            return response()->json([
                'status' => 200,
                'message' => 'Data updated successfully.',
                'data' => $event,
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $event = Event::find($id);
        if ($event == null) {
            return response()->json([
                'status' => 400,
                'message' => 'There is no event with the id of '.$id
            ]);
        }
        else {
            $event->delete();
            return response()->json([
                'status' => 200,
                'message' => 'Data deleted successfully.'
            ]);
        }
    }

    public static function checkQuota($id) {
        $event = Event::find($id);
        if ($event->quota == $event->sold) {
            return true;
        }
        else {
            return false;
        }
    }

    public static function findPriceAndHost($id) {
        return Event::find($id);
    }

    public static function bought($id) {
        $event = Event::find($id);
        $event->sold+=1;
        $event->save();
    }

    public function randomEvents() {
        $events = Event::inRandomOrder()->limit(10)->get();
        
        if ($events->isEmpty()) {
            return response()->json([
                'status' => 200,
                'message' => 'There are no events yet.'
            ]);
        }
        else {
            return response()->json([
                'status' => 200,
                'message' => 'Date fetched successfully.',
                'Data' => $events
            ]);
        }
    }

    public function indexHostEvent(Request $request) {
        $events = $request->user()->events;
        if ($events->isEmpty()) {
            return response()->json([
                'status' => 200,
                'message' => 'Events not found.'
            ]);
        }
        else {
            return response()->json([
                'status' => 200,
                'message' => 'Host events data fetched successfully.',
                'data' => $request->user()->events
            ]);
        }
    }

    public function showHostEvent(Request $request, $id) {
        $event = Event::find($id);
        if ($event->host_id != $request->user()->id) {
            return redirect(route('unauth'));
        }

        if ($event == null) {
            return response()->json([
                'status' => 200,
                'message' => 'Events not found.'
            ]);
        }
        else {
            return response()->json([
                'status' => 200,
                'message' => 'Host events data fetched successfully.',
                'data' => $event
            ]);
        }
    }

    public function createHostEvent(Request $request) {
        $request->validate([
            'title' => 'string|max:255',
            'description' => 'string',
            'tnc' => 'string',
            'latitude' => 'numeric',
            'longitude' => 'numeric',
            'date_start' => 'date',
            'date_end' => 'date',
            'quota' => 'numeric',
            'price' => 'numeric'
        ]);
        $event = Event::create([
            'title' => $request->title,
            'description' => $request->description,
            'tnc' => $request->tnc,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'date_start' => $request->date_start,
            'date_end' => $request->date_end,
            'quota' => $request->quota,
            'sold' => 0,
            'price' => $request->price,
            'host_id' => $request->user()->id
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Event created successfully.',
            'data' => $event
        ]);
    }

    public function updateHostEvent(Request $request, $id) {
        $request->validate([
            'title' => 'string|max:255|nullable',
            'description' => 'string|nullable',
            'tnc' => 'string|nullable',
            'latitude' => 'numeric|nullable',
            'longitude' => 'numeric|nullable',
            'date_start' => 'date|nullable',
            'date_end' => 'date|nullable',
            'quota' => 'numeric|nullable',
            'price' => 'numeric|nullable'
        ]);
        $event = Event::find($id);
        if ($event->host_id != $request->user()->id) {
            return redirect(route('unauth'));
        }

        if ($event == null) {
            return response()->json([
                'status' => 400,
                'message' => 'There is no event with the id of '.$id
            ]);
        }
        else {
            $event->title = $request->title ? $request->title : $event->title;
            $event->description = $request->description ? $request->description : $event->description;
            $event->tnc = $request->tnc ? $request->tnc : $event->tnc;
            $event->latitude = $request->latitude ? $request->latitude : $event->latitude;
            $event->longitude = $request->longitude ? $request->longitude : $event->longitude;
            $event->date_start = $request->date_start ? $request->date_start : $event->date_start;
            $event->date_end = $request->date_end ? $request->date_end : $event->date_end;
            $event->quota = $request->quota ? $request->quota : $event->quota;
            $event->price = $request->price ? $request->price : $event->price;
            $event->save();

            return response()->json([
                'status' => 200,
                'message' => 'Event updated successfully.',
                'data' => $event,
            ]);
        }
    }

    public function deleteHostEvent(Request $request, $id) {
        $event = Event::with('tickets.transaction')->where('host_id', '=', $request->user()->id)->first();
        if ($event == null) {
            return response()->json([
                'status' => 200,
                'message' => 'There is no event with the id of '.$id
            ]);
        }
        else {
            if ($event->host_id != $request->user()->id) {
                return redirect(route('unauth'));
            }
            else {
                if ($event->tickets->isNotEmpty()) {
                    foreach ($event->tickets as $ticket) {
                        UserController::balTransfer($event->host_id, $ticket->buyer_id, $ticket->transaction->bal_amount);
                        TransactionController::returnTransaction($event->host_id, $ticket->buyer_id, $ticket->transaction->bal_amount);
                    }
                }
                $event->delete();
    
                return response()->json([
                    'status' => 200,
                    'message' => 'Event deleted successfully.'
                ]);
            }
        }
    }
}
