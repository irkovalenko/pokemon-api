<?php

namespace App\Actions\Comments;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

class DeleteCommentAction
{
    public function execute(Comment $comment, User $user): string
    {
        if ($user->id !== $comment->user->id) {
            throw new AuthorizationException("This is not your comment, you can't remove it.");
        }

        $pokemonId = $comment->pokemon_id;
        $comment->delete();

        return $pokemonId;
    }
}
