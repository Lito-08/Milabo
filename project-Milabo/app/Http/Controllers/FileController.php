<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'folder_id' => 'nullable|exists:folders,id',
        ]);
        
        // Check if folder belongs to user
        if ($validated['folder_id'] && Folder::find($validated['folder_id'])->user_id !== auth()->id()) {
            abort(403);
        }
        
        $uploadedFile = $request->file('file');
        $path = $uploadedFile->store('pnph_files/' . auth()->id(), 'public');
        
        $file = File::create([
            'name' => $uploadedFile->getClientOriginalName(),
            'path' => $path,
            'size' => $uploadedFile->getSize(),
            'type' => $uploadedFile->getMimeType(),
            'user_id' => auth()->id(),
            'folder_id' => $validated['folder_id'],
        ]);
        
        return redirect()->back()->with('success', 'File uploaded successfully');
    }
    
    public function download(File $file)
    {
        // Check if user owns this file
        if ($file->user_id !== auth()->id()) {
            abort(403);
        }
        
        return Storage::disk('public')->download($file->path, $file->name);
    }
    
    public function destroy(File $file)
    {
        // Check if user owns this file
        if ($file->user_id !== auth()->id()) {
            abort(403);
        }
        
        // Delete the file from storage
        Storage::disk('public')->delete($file->path);
        
        // Delete the database record
        $file->delete();
        
        return redirect()->back()->with('success', 'File deleted successfully');
    }
}