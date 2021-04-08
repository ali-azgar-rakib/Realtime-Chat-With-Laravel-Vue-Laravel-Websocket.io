<?php

namespace App\Http\Controllers;

use App\Events\MessageSend;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{


    public function user_list()
    {
        $users = User::latest()->where('id', '!=', Auth::id())->get();
        if (\Request::ajax()) {
            return response()->json($users, 200);
        } else {
            return abort(404);
        }
    }

    public function user_messages(Request $request, $id)
    {
        // if (!$request->ajax()) {
        //     return abort(404);
        // }
        $user          = User::find($id);
        $user_messages = $this->user_messages_by_id($id);
        return response()->json([
            'userMessages' => $user_messages,
            'user'         => $user
        ], 200);
    }

    public function insert_message(Request $request)
    {
        $types = [1, 0];
        foreach ($types as $type) {
            $message = Message::create([
                'user_id' => Auth::id(),
                'receiver' => $request->receiver,
                'message' => $request->message,
                'type' => $type,
                'created_at' => Carbon::now()
            ]);
        }
        broadcast(new MessageSend($message));

        return response()->json('ok', 201);
    }


    public function delete_single_message(Message $message)
    {
        $message->delete();
        return response()->json('ok', 200);
    }

    public function delete_all_message($id)
    {

        $all_delete_id = $this->user_messages_by_id($id);
        foreach ($all_delete_id as $delete_id) {
            Message::find($delete_id->id)->delete();
        }
        return response()->json('ok', 200);
    }


    private function user_messages_by_id($id)
    {
        return Message::where(function ($q) use ($id) {
            $q->where('user_id', Auth::id());
            $q->where('receiver', $id);
            $q->where('type', 0);
        })->orWhere(function ($q) use ($id) {
            $q->where('user_id', $id);
            $q->where('receiver', Auth::id());
            $q->where('type', 1);
        })->with('user')->get();
    }
}
