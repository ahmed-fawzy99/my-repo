<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFileRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use function Laravel\Prompts\error;

class FileController extends Controller
{
    public function store(StoreFileRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $media = auth()->user()->addMedia($validated['file'])
            ->usingName($validated['name'])
            ->usingFileName($validated['name'])
            ->withCustomProperties([
                'enc_key' => $validated['key'], // if null, then it's a custom key
                'checksum' => $validated['checksum'],
            ])
            ->toMediaCollection('encrypted_files' );
        $media->mime_type = $validated['type'];
        $media->save();
        return redirect()->route('dashboard');
    }

    public function get(Request $request)
    {
        //  VALIDATION YA 3LE2

        $media = Media::firstWhere('uuid', $request->uuid);
        if ($media) {
            if ($media->model_id !== auth()->id())
                return response()->json(['error' => 'File not found or Unauthorized'], 404);
            return $media;
        }
        return response()->json(['error' => 'File not found'], 404);
    }

    public function modify(Request $request)
    {
        //  VALIDATION YA 3LE2
        $media = Media::firstWhere('uuid', $request->uuid);
        if ($media) {
            if ($media->model_id !== auth()->id())
                return response()->json(['error' => 'File not found or Unauthorized'], 404);
            // 'name' field should be without extension
            $media->update(['name' => $request->newFileName, 'file_name' => $request->newFileName]);
            return back();

        }
        return response()->json(['error' => 'File not found'], 404);
    }

    public function destroy(Request $request)
    {
        //  VALIDATION YA 3LE2

        $media = Media::firstWhere('uuid', $request->uuid);
        if ($media) {
            if ($media->model_id !== auth()->id())
                return response()->json(['error' => 'File not found or Unauthorized'], 404);
            $media->delete();
            return redirect('dashboard')->with('success', 'File deleted successfully');
        }
        return response()->json(['error' => 'File not found'], 404);
    }
}
