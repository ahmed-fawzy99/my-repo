<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'contactId' => 'nullable|ulid',
            'contactSearch' => 'string|nullable',
        ]);

        // If the request contains a contactId, for redirection purpose
        if (array_key_exists('contactId', $validated)){
            $conversation = Conversation::where(function($query) use ($validated) {
                $query->where('user_1', auth()->id())
                    ->where('user_2', $validated['contactId']);
            })->orWhere(function($query) use ($validated) {
                $query->where('user_1', $validated['contactId'])
                    ->where('user_2', auth()->id());
            })->first();

            if (!$conversation) {
                $conversation = Conversation::create([
                    'user_1' => auth()->id(),
                    'user_2' => $validated['contactId'],
                ]);
            }
        } else {
            $conversation = null;
        }




        return inertia('Conversation/Conversations', [
            'conversations' => auth()->user()->conversations()
                ->with('user_1', 'user_2', 'messages')
                ->when($request->contactSearch, function($query, $contactSearch) {
                    $query->where(function($query) use ($contactSearch) {
                        $query->whereHas('user_1', function($query) use ($contactSearch) {
                            $query->where('name', 'ILIKE', '%' . $contactSearch . '%');
                        })->orWhereHas('user_2', function($query) use ($contactSearch) {
                            $query->where('name', 'ILIKE', '%' . $contactSearch . '%');
                        });
                    });
                })
                ->paginate(10),

            'passedConversationId' => $conversation?->id,
        ]);
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Conversation $conversation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Conversation $conversation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Conversation $conversation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Conversation $conversation)
    {
        //
    }
}
