<?php

namespace App\Http\Controllers;

use App\Models\Globals;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response | RedirectResponse
    {
        if (!auth()->user()->public_key_ecdh || !auth()->user()->public_key_eddsa) {
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
        $globals = Globals::first();
        logger($globals);
        return Inertia::render('Dashboard/Dashboard', [
            'userFiles' => auth()->user()->media()->orderBy($request->sort ?? 'created_at', $sortDir)->paginate(10),
            'filesCount' => auth()->user()?->getMedia('*')->count(),
            'storageUsage' => auth()->user()?->getMedia('*')->sum('size'),
            'quota' => $globals->max_file_size * $globals->max_file_count,
        ]);
    }

    public function vault(): Response
    {
        return Inertia::render('Dashboard/KeyVault', [
            'files' => auth()->user()->media()->paginate(10),
        ]);
    }
}
