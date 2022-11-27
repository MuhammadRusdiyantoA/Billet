<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transactions = Transaction::all();
        if ($transactions->isEmpty()) {
            return response()->json([
                'status' => 200,
                'message' => 'Current table is empty.'
            ]);
        }
        else {
            return response()->json([
                'status' => 200,
                'message' => 'Data fetched successfully.',
                'data' => $transactions
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
            'sender_id' => 'numeric|nullable',
            'receiver_id' => 'numeric|nullable',
            'bal_amount' => 'numeric|nullable'
        ]);
        $transaction = Transaction::create([
            'sender_id' => $request->sender_id,
            'receiver_id' => $request->receiver_id,
            'bal_amount' => $request->bal_amount,
            'trans_date' => date('Y-m-d H:i:s')
        ]);
        return response()->json([
            'status' => 200,
            'message' => 'Data created successfully',
            'data' => $transaction
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $transaction = Transaction::find($id);
        if ($transaction == null) {
            return response()->json([
                'status' => 400,
                'message' => 'There is no transaction with the id of '.$id
            ]);
        }
        else {
            return response()->json([
                'status' => 200,
                'message' => 'Data fetched successfully.',
                'data' => $transaction
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'sender_id' => 'numeric|nullable',
            'receiver_id' => 'numeric|nullable',
            'bal_amount' => 'numeric|nullable',
            'trans_date' => 'date|nullable'
        ]);
        $transaction = Transaction::find($id);
        if ($transaction == null) {
            return response()->json([
                'status' => 400,
                'message' => 'There is no transaction with the id of '. $id
            ]);
        }
        else {
            $transaction->sender_id = $request->sender_id ? $request->sender_id : $transaction->sender_id;
            $transaction->receiver_id = $request->receiver_id ? $request->receiver_id : $transaction->receiver_id;
            $transaction->bal_amount = $request->bal_amount ? $request->bal_amount : $transaction->bal_amount;
            $transaction->trans_date = $request->trans_date ? $request->trans_date : $transaction->trans_date;
            $transaction->save();
            return response()->json([
                'status' => 200,
                'message' => 'Data updated successfully.',
                'data' => $transaction
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $transaction = Transaction::find($id);
        if ($transaction == null) {
            return response()->json([
                'status' => 400,
                'message' => 'There is no transaction with the id of '.$id
            ]);
        }
        else {
            $transaction->delete();
            return response()->json([
                'status' => 200,
                'message' => 'Data deleted successfully.'
            ]);
        }
    }

    public static function reqCreate($event_id, $buyer_id) {
        $checkQouta = EventController::checkQuota($event_id);
        if ($checkQouta) {
            return -1;
        }

        $event = EventController::findPriceAndHost($event_id);

        $trans_code = UserController::balTransfer($buyer_id, $event->host_id, $event->price);
        switch($trans_code) {
            case 0:
                return 0;
                break;
            case 2:
                return -2;
                break;
        }

        $transaction = Transaction::create([
            'sender_id' => $buyer_id,
            'receiver_id' => $event->host_id,
            'bal_amount' => $event->price,
            'trans_date' => date('Y-m-d H:i:s')
        ]);

        EventController::bought($event_id);

        return $transaction->id;
    }

    public function transactionProfile(Request $request) {
        $transactions = $request->user()->send ? $request->user()->send : $request->user()->receive;
        if ($transactions->isEmpty()) {
            return response()->json([
                'status' => 200,
                'message' => 'No transactions found.'
            ]);
        }
        else {
            return response()->json([
                'status' => 200,
                'message' => 'Transactions fetched successfully.',
                'data' => $transactions
            ]);
        }
    }

    public static function returnTransaction($sender_id, $receiver_id, $bal_amount) {
        Transaction::create([
            'sender_id' => $sender_id,
            'receiver_id' => $receiver_id,
            'bal_amount' => $bal_amount,
            'trans_date' => date('Y-m-d H:i:s')
        ]);
    }
}
