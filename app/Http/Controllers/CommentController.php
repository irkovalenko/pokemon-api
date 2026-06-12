<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use App\Http\Controllers\Controller;

class CommentController extends Controller
{

    public function store(CommentRequest $request)
    {
        $validated = $request->validated();

        Comment::create(
            [
                'pokemon_id' => $validated['pokemon_id'],
                'user_id' => $request->user()->id,
                'content' => $validated['content'],
                'parent_id' => $validated['parent_id'] ?? null,
            ]
        );

        return to_route('pokemons.show', $validated['pokemon_id'])->with('success', 'Comment created successfully.');
    }


    public function update(CommentRequest $request, Comment $comment)
    {
        $user = request()->user();
        if ($user->id !== $comment->user->id) {
            abort(403, 'This is not your comment, you can\'t edit it.');
        }

        $validated = $request->validated();
        $comment->update([
            'content' => $validated['content'],
        ]);

        return redirect()->route('pokemons.show', $comment->pokemon_id);
    }


    public function destroy(Comment $comment)
    {
        $user = request()->user();
        if ($user->id !== $comment->user->id) {
            abort(403, 'This is not your comment, you can\'t remove it.');
        }
        $pokemonId = $comment->pokemon_id;
        $comment->delete();
        return redirect()->route('pokemons.show', $pokemonId)->with('message', 'Comment deleted successfully');
    }
}
