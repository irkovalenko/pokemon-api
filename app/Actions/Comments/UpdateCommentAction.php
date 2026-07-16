<?php

namespace App\Actions\Comments;

use App\DataTransferObjects\Comments\UpdateCommentData;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

class UpdateCommentAction
{
    public function execute(Comment $comment, UpdateCommentData $data, User $user): Comment
    {
        if ($user->id !== $comment->user->id) {
            throw new AuthorizationException("This is not your comment, you can't edit it.");
        }

        $comment->update([
            'content' => $data->content,
        ]);

        return $comment->fresh();
    }
}
