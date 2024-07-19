<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Inertia::render('Contact/Contacts', [
            'contacts' => auth()->user()->contacts()->get(),
            'allUsers' => User::with('contacts')->
            where('id', '!=', auth()->id())->
            when($request->term, function ($query, $term) {
                $query->where('name', 'ILIKE', '%' . $term . '%')
                    ->orWhere('email', 'ILIKE', '%' . $term . '%');
            })->select('id', 'name', 'email')->orderBy('name')->paginate(10),
            'contactRequestsCount' => $this->incomingContactRequestsCount(),
            'sentRequestsCount' => $this->sentContactRequestsCount(),
        ]);
    }
    /**
     * Display a listing of the pending resource.
     */
    public function requestsIndex()
    {
        return Inertia::render('Contact/ContactRequests', [
            'contactRequests' => Contact::with('user:id,name')->where('contact_id', auth()->id())
                ->where('contact_accepted', false)
                ->orderBy('contacts.created_at', 'desc')->get(),
            'sentRequestsCount' => $this->sentContactRequestsCount(),
        ]);
    }
    public function sentRequestsIndex()
    {
        return Inertia::render('Contact/ContactSentRequests', [
            'contactSentRequests' => Contact::with('contact:id,name')->where('user_id', auth()->id())
                ->where('contact_accepted', false)
                ->orderBy('contacts.created_at', 'desc')->get(),
            'contactRequestsCount' => $this->incomingContactRequestsCount(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'selectedContacts' => 'required|array',
            'selectedContacts.*' => 'required|ulid',
        ]);
        try {
            foreach ($validated['selectedContacts'] as $contactId) {
                $contact = User::find($contactId);
                auth()->user()->contacts()->attach($contact);
                Conversation::create([
                    'user_1' => auth()->id(),
                    'user_2' => $contactId,
                ]);
            }
            return redirect()->route('contacts.index');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors('You already sent a request to one of the selected contacts.');
        }
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
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'choice' => 'required|boolean',
        ]);
        if ($validated['choice'] === true) {
            Contact::where('user_id', $id)->where('contact_id', auth()->id())->update([
                'contact_accepted' => $validated['choice'],
            ]);
            Contact::create([
                'user_id' => auth()->id(),
                'contact_id' => $id,
                'contact_accepted' => true,
            ]);

        } else {
            Contact::where('contact_id', auth()->id())->where('user_id', $id)->delete();
        }
        return redirect()->route('contacts-requests');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $contact = User::find($id);

        // Delete Conversations
        Conversation::where(function ($query) use ($id) {
            $query->where('user_1', auth()->id())
                ->where('user_2', $id);
        })->orWhere(function ($query) use ($id) {
            $query->where('user_1', $id)
                ->where('user_2', auth()->id());
        })->delete();


        // Delete Contact
        auth()->user()->contacts()->detach($contact);
        $contact->contacts()->detach(auth()->user());
        return redirect()->route('contacts.index');
    }

    // ^^^ inconsistent parameters. stick with either $id or $request.
    public function destroySentRequest(Request $request)
    {
        auth()->user()->contacts()->detach($request->id);
        return redirect()->route('contacts-sent-requests');
    }

    private function incomingContactRequestsCount()
    {
        return Contact::where('contact_id', auth()->id())
            ->where('contact_accepted', false)->count();
    }
    private function sentContactRequestsCount()
    {
        return Contact::where('user_id', auth()->id())
            ->where('contact_accepted', false)->count();
    }
}
