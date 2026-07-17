<?php

namespace App\Http\Controllers;

use App\Actions\Comments\DeleteCommentAction;
use App\Actions\Comments\StoreCommentAction;
use App\Actions\Comments\UpdateCommentAction;
use App\DataTransferObjects\Comments\StoreCommentData;
use App\DataTransferObjects\Comments\UpdateCommentData;
use App\Http\Requests\Comments\StoreCommentRequest;
use App\Http\Requests\Comments\UpdateCommentRequest;
use App\Models\Comment;
use Illuminate\Http\RedirectResponse;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request, StoreCommentAction $action): RedirectResponse
    {
        $data = StoreCommentData::fromArray($request->validated(), $request->user()->id);

        $comment = $action->execute($data);

        return to_route('pokemons.show', $comment->pokemon_id)
            ->with('success', 'Comment created successfully.');
    }

    public function update(UpdateCommentRequest $request, Comment $comment, UpdateCommentAction $action): RedirectResponse
    {
        $data = UpdateCommentData::fromArray($request->validated());

        $action->execute($comment, $data, $request->user());

        return redirect()->route('pokemons.show', $comment->pokemon_id);
    }

    public function destroy(Comment $comment, DeleteCommentAction $action): RedirectResponse
    {
        $pokemonId = $action->execute($comment, request()->user());

        return redirect()->route('pokemons.show', $pokemonId);
    }
}
