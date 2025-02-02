<?php

namespace App\Http\Controllers;

use App\Models\AppChannel;
use App\Models\Client;
use App\Models\File;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MineController extends Controller
{
    public function dashboard(){
        return view('admin.index');
    }
    public function ticket(){
        $tickets = Ticket::paginate(10);
        return view('admin.ticket.list', compact('tickets'));
    }
    public function ticketShow($ticket, Request $request){
        $ticket = Ticket::where('id',$ticket)->first();
        return view('admin.ticket.edit' , compact('ticket'));
    }

    public function addMessage($ticket, Request $request)
    {
        $ticket = Ticket::where('id',$ticket)->first();

        // اعتبارسنجی ورودی
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        // ایجاد پیام جدید
        $ticket->messages()->create([
            'user_id' => Auth::user()->id,
            'description' => $request->input('message'),
        ]);

        return redirect()->route('ticket.show', $ticket->id)->with('success', 'پیام شما با موفقیت ثبت شد.');
    }


}
