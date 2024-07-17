<?php

namespace App\Http\Controllers;

use App\Models\Contact;
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
        return Inertia::render('Contacts/Contacts', [
            'contacts' => auth()->user()->contacts()->get(),
            'allUsers' => User::with('contacts')->
            where('id', '!=', auth()->id())->
            when($request->term, function ($query, $term) {
                $query->where('name', 'ILIKE', '%' . $term . '%')
                    ->orWhere('email', 'ILIKE', '%' . $term . '%');
            })->select('id', 'name', 'email')->orderBy('name')->paginate(10),
            'contactRequestsCount' => $this->getContactRequestsCount(),
        ]);
    }
    /**
     * Display a listing of the pending resource.
     */
    public function requestsIndex()
    {
        return Inertia::render('Contacts/ContactRequests', [
            'contactRequests' => Contact::with('user:id,name')->where('contact_id', auth()->id())
                ->where('contact_accepted', false)
                ->orderBy('contacts.created_at', 'desc')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        return Inertia::render('Contacts/ContactCreate', [
            'allUsers' => User::when($request->term, function ($query, $term) {
                $query->where('name', 'ILIKE', '%' . $term . '%')
                    ->orWhere('email', 'ILIKE', '%' . $term . '%');
            })->where('id', '!=', auth()->id())->select('id', 'name', 'email')->orderBy('name')->paginate(10),
            'contactRequestsCount' => $this->getContactRequestsCount(),
        ]);
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
            auth()->user()->contacts()->attach($validated['selectedContacts']);
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
            Contact::where('contact_id', auth()->id())->where('user_id', $id)->update([
                'contact_accepted' => $validated['choice'],
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
        auth()->user()->contacts()->detach($id);
        return redirect()->route('contacts.index');
    }

    private function getContactRequestsCount()
    {
        return Contact::where('contact_id', auth()->id())
            ->where('contact_accepted', false)
            ->orderBy('contacts.created_at', 'desc')->count();
    }
}
