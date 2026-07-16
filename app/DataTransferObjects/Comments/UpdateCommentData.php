<?php

namespace App\DataTransferObjects\Comments;

readonly class UpdateCommentData
{
    public function __construct(
        public string $content,
    ) {}

    public static function fromArray(array $validated): self
    {
        return new self(
            content: $validated['content'],
        );
    }
}
