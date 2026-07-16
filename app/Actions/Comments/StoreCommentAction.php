<?php

namespace App\Actions\Comments;

use App\DataTransferObjects\Comments\StoreCommentData;
use App\Models\Comment;

class StoreCommentAction
{
    public function execute(StoreCommentData $data): Comment
    {
        return Comment::create([
            'pokemon_id' => $data->pokemonId,
            'user_id' => $data->userId,
            'content' => $data->content,
            'parent_id' => $data->parentId,
        ]);
    }
}
