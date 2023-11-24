<?php

namespace App\Http\Controllers;

use App\Models\TicketsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ticketsController extends Controller
{
    //

    public function store(Request $request)
    {
        $ticket = TicketsModel::create([
            'subject' => $request->input('subject'),
            'message' => $request->input('message'),
            'status' => TicketsModel::PENDIENTE,
            'user_id'=> auth()->user()->id
        ]);
        return response()->json(['success' => true, 'ticket' => $ticket->id], 200);
    }
}
