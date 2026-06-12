// Components/CommentBox.jsx
import { useState } from 'react';
import { router } from '@inertiajs/react';

export default function CommentBox({ pokemonId, onSubmitted,  parentId = null}) {
    const [content, setContent] = useState('');

    const handleKeyDown = (e) => {
        if (e.key === 'Enter' && !e.shiftKey && content.trim()) {
            e.preventDefault();
            router.post(route('comments.store'), {
                pokemon_id: pokemonId,
                content: content.trim(),
                parent_id: parentId,
            }, {
                preserveScroll: true,
                onSuccess: () => {
                setContent('');
                onSubmitted();
                },
                 onError: (errors) => {
        console.log('errors', errors);
    },
            });
        }
    };

    return (
        <textarea
            value={content}
            onChange={(e) => setContent(e.target.value)}
            onKeyDown={handleKeyDown}
            placeholder={parentId ?"Write a reply and press Enter to submit..." : "Write a comment and press Enter to submit..."}
            rows={3}
            className="w-full px-4 py-2 border rounded-md text-sm resize-none focus:outline-none focus:ring-2 focus:ring-gray-400"
        />
    );
}