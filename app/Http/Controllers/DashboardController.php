<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response | RedirectResponse
    {
        if (!auth()->user()->public_key) {
            return redirect()->route('register-keys');
        }
        $sortDir = 'asc';
        if ($request->has('sort')) {
            $request->validate([
                'sort' => 'in:file_name,size,created_at',
                'sort_dir' => 'required|boolean',
            ]);
            $sortDir = $request->sort_dir ? 'asc' : 'desc';
        }
        return Inertia::render('Dashboard/Dashboard', [
            'userFiles' => auth()->user()->media()->orderBy($request->sort ?? 'created_at', $sortDir)->paginate(10),
            'filesCount' => auth()->user()?->getMedia('*')->count(),
            'storageUsage' => auth()->user()?->getMedia('*')->sum('size'),
            'quota' => 3000000, // To be replaced with the user's quota based on the plan
        ]);
    }

    public function vault(): Response
    {
        return Inertia::render('Dashboard/KeyVault', [
            'files' => auth()->user()->media()->paginate(10),
        ]);
    }
}
