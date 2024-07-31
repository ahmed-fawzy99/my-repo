<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFileRequest;
use App\Models\User;
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
        $globals = \App\Models\Globals::first();
        if (auth()->user()->getMedia('*')->count() >= $globals->max_file_count) {
            return redirect()->back()->withErrors(['error' => 'You have reached the maximum number of files allowed, which is ' . $globals->max_file_count]);
        }
        if ($request->file('file')->getSize() > $globals->max_file_size) {
            return redirect()->back()->withErrors(['error' => 'File size exceeds the maximum allowed size, which is ' . $this->human_filesize($globals->max_file_size)]);
        }

        // Store it
        $media = auth()->user()->addMedia($validated['file'])
            ->usingName($validated['name'])
            ->usingFileName($validated['name'])
            ->withCustomProperties([
                'enc_key' => $validated['key'], // if null, then it's a custom key
                'checksum' => $validated['checksum'],
                'sharable' => false,
            ])
            ->toMediaCollection('encrypted_files');
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
        $request->validate(['uuid' => 'required|uuid']);
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
        $request->validate(['uuid' => 'required|uuid']);
        $media = Media::firstWhere('uuid', $request->uuid);
        if ($media) {
            if ($media->model_id !== auth()->id())
                return response()->json(['error' => 'File not found or Unauthorized'], 404);
            $media->delete();
            return redirect('dashboard')->with('success', 'File deleted successfully');
        }
        return response()->json(['error' => 'File not found'], 404);
    }

    public function share(Request $request)
    {
        $request->validate(['uuid' => 'required|uuid']);
        $media = Media::firstWhere('uuid', $request->uuid);
        if ($media) {
            if ($media->custom_properties['enc_key'] !== null){
                return response()->json(['error' => 'Cannot Share private-key encrypted file'], 400);
            } else {
                $media->custom_properties['sharable'] = true;
                $media->save();
                return response()->json(['file_uuid' => $request->uuid], 200);
            }
        }
        return response()->json(['error' => 'File not found'], 404);
    }

    public function getShared(String $file_uuid){
        $media = Media::firstWhere('uuid', $file_uuid);
        if ($media) {
            if ($media->custom_properties['enc_key'] !== null || !$media->custom_properties['sharable']){
                return response()->json(['bad_request' => 'Bas Request'], 400);
            }
            return Inertia::render('Share/GetSharedFile', [
                'file' => $media,
                'owner' => User::find($media->model_id)->name,
            ]);
        }
        return response()->json(['error' => 'File not found'], 404);
    }

    public function downloadShared(Request $request)
    {
        $request->validate(['uuid' => 'required|uuid']);
        $media = Media::firstWhere('uuid', $request->uuid);
        if ($media) {
            if ($media->custom_properties['enc_key'] !== null){
                return response()->json(['error' => 'Cannot Share private-key encrypted file'], 400);
            } else {
                return $media;
            }
        }
        return response()->json(['error' => 'File not found'], 404);
    }

    private function human_filesize($bytes, $dec = 0): string {
        $size   = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $factor = floor((strlen($bytes) - 1) / 3);
        if ($factor == 0) $dec = 0;
        return sprintf("%.{$dec}f %s", $bytes / (1024 ** $factor), $size[$factor]);
    }

}
