<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required',
            'conversationId' => 'required|ulid',
            'signature' => 'required|string|size:128',
        ]);
        if (auth()->user()->conversations->contains($validated['conversationId'])) {
            auth()->user()->sendMessage($validated['conversationId'], $validated['content'], $validated['signature']);
            return redirect()->back();
        }
        throw new \Exception('Unauthorized');
    }

    /**
     * Display the specified resource.
     */
    public function show(Message $message)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Message $message)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Message $message)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Message $message)
    {
        if ($message->sender_id === auth()->id()) {
            $message->update(['content' => '']);
        } else {
            throw new \Exception('Unauthorized');
        }
    }
}
