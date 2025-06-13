<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use Illuminate\Http\Request;

class FolderController extends Controller
{
    public function index()
    {
        $rootFolders = Folder::where('user_id', auth()->id())
            ->whereNull('parent_id')
            ->with('subfolders')
            ->get();
            
        return view('folders.index', compact('rootFolders'));
    }
    
    public function show(Folder $folder)
    {
        // Check if user owns this folder
        if ($folder->user_id !== auth()->id()) {
            abort(403);
        }
        
        $folder->load('subfolders', 'files');
        
        return view('folders.show', compact('folder'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:folders,id',
        ]);
        
        // Check if parent folder belongs to user
        if ($validated['parent_id'] && Folder::find($validated['parent_id'])->user_id !== auth()->id()) {
            abort(403);
        }
        
        $folder = Folder::create([
            'name' => $validated['name'],
            'parent_id' => $validated['parent_id'],
            'user_id' => auth()->id(),
        ]);
        
        return redirect()->back()->with('success', 'Folder created successfully');
    }
    
    // Other methods (update, delete) would go here
}