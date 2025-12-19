<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tags = Tag::where('user_id', auth()->id())
            ->withCount('alunos')
            ->orderBy('nome')
            ->get();

        return view('tags.index', compact('tags'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tags.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'cor' => 'required|string|max:7',
        ]);

        Tag::create([
            'user_id' => auth()->id(),
            'nome' => $validated['nome'],
            'cor' => $validated['cor'],
        ]);

        return redirect()->route('tags.index')->with('success', 'Tag criada com sucesso!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tag $tag)
    {
        if ($tag->user_id !== auth()->id()) {
            abort(403);
        }

        return view('tags.edit', compact('tag'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tag $tag)
    {
        if ($tag->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'cor' => 'required|string|max:7',
        ]);

        $tag->update($validated);

        return redirect()->route('tags.index')->with('success', 'Tag atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tag $tag)
    {
        if ($tag->user_id !== auth()->id()) {
            abort(403);
        }

        $tag->delete();

        return redirect()->route('tags.index')->with('success', 'Tag excluída com sucesso!');
    }
}
