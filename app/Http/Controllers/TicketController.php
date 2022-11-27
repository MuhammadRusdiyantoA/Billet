<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ticket = Ticket::all();
        if ($ticket->isEmpty()) {
            return response()->json([
                'status' => 200,
                'message' => 'Current table is empty.'
            ]);
        }
        else {
            return response()->json([
                'status' => 200,
                'message' => 'Data fetched successfully.',
                'data' => $ticket
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
            'event_id' => 'numeric',
            'buyer_id' => 'numeric',
            'trans_id' => 'numeric'
        ]);

        $ticket = Ticket::create([
            'event_id' => $request->event_id,
            'buyer_id' => $request->buyer_id,
            'trans_id' => $request->trans_id
        ]);
        return response()->json([
            'status' => 200,
            'message' => 'Data created successfully',
            'data' => $ticket
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Ticket  $ticket
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $ticket = Ticket::find($id);
        if ($ticket == null) {
            return response()->json([
                'status' => 400,
                'message' => 'There is no ticket with the id of '.$id
            ]);
        }
        else {
            return response()->json([
                'status' => 200,
                'message' => 'Data fetched successfully.',
                'data' => $ticket
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Ticket  $ticket
     * @return \Illuminate\Http\Response
     */
    public function edit(Ticket $ticket)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Ticket  $ticket
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'event_id' => 'numeric|nullable',
            'buyer_id' => 'numeric|nullable',
            'trans_id' => 'numeric|nullable'
        ]);

        $ticket = Ticket::find($id);
        if ($ticket == null) {
            return response()->json([
                'status' => 400,
                'message' => 'There is no ticket with the id of '.$id
            ]);
        }
        else {
            $ticket->event_id = $request->event_id ? $request->event_id : $ticket->event_id;
            $ticket->buyer_id = $request->buyer_id ? $request->buyer_id : $ticket->buyer_id;
            $ticket->trans_id = $request->trans_id ? $request->trans_id : $ticket->trans_id;
            $ticket->save();
            return response()->json([
                'status' => 200,
                'message' => 'Data updated successfully.',
                'data' => $ticket
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Ticket  $ticket
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $ticket = Ticket::find($id);
        if ($ticket == null) {
            return response()->json([
                'status' => 400,
                'message' => 'There is no ticket with the id of '.$id
            ]);
        }
        else {
            $ticket->delete();
            return response()->json([
                'status' => 200,
                'message' => 'Data deleted successfully.'
            ]);
        }
    }

    public function buy(Request $request) {
        $request->validate([
            'event_id' => 'numeric'
        ]);

        $trans_id = TransactionController::reqCreate($request->event_id, $request->user()->id);
        switch($trans_id) {
            case 0:
                return response()->json([
                    'status' => 400,
                    'message' => 'Buyer/Host not found.'
                ]);
                break;
            case -1:
                return response()->json([
                    'status' => 400,
                    'message' => 'Ticket quota is full.'
                ]);
                break;
            case -2:
                return response()->json([
                    'status' => 400,
                    'message' => 'Not enough balance.'
                ]);
        }

        $ticket = Ticket::create([
            'event_id' => $request->event_id,
            'buyer_id' => $request->user()->id,
            'trans_id' => $trans_id
        ]);
        return response()->json([
            'status' => 200,
            'message' => 'Data created successfully',
            'Data' => $ticket
        ]);
    }

    public function buyerTicket(Request $request) {
        $user_id = $request->user()->id;
        $tickets = Ticket::where('buyer_id', '=', $user_id)->get();

        if ($tickets->isEmpty()) {
            return response()->json([
                'status' => 200,
                'message' => 'No tickets found.'
            ]);
        }
        else {
            return response()->json([
                'status' => 200,
                'message' => "Buyer's tickets fetched successfully.",
                'data' => $tickets
            ]);
        }

    }
}
