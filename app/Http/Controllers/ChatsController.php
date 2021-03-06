<?php

namespace App\Http\Controllers;

use App\Message;
use Illuminate\Http\Request;
use App\Events\MessageSent;

class ChatsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('chats');
    }

    public function fetchMessages()
    {
        return Message::with('user')->get();
    }

    public function sendMessage(Request $request)
    {
        if(request()->has('file')){
            $filename = request('file')->store('chat');
            $message = auth()->user()->messages()->create([
                'message' => $request->message,
                'file' => $filename,
            ]);
        }else{
            $message = auth()->user()->messages()->create([
                'message' => $request->message
            ]);
        }

        broadcast(new MessageSent($message->load('user')))->toOthers();

        return ['status' => 'success'];
    }
}
