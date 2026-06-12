import { router } from '@inertiajs/react';
import { useState } from 'react';
import CommentBox from '@/Components/CommentBox';

export default function CommentItem({ comment, pokemonId, editingId, setEditingId, editContent, setEditContent }) {

    const [showReplyBox, setShowReplyBox] = useState(false);

    return (
        <div className="border rounded-md p-3 text-sm text-gray-700 dark:text-gray-300">
            <div className="flex justify-between mb-1">
                <span className="font-medium">{comment.author}</span>
                <span className="text-gray-400 text-xs">{comment.date}</span>
            </div>

            {editingId === comment.id ? (
                <textarea
                    value={editContent}
                    onChange={(e) => setEditContent(e.target.value)}
                    onKeyDown={(e) => {
                        if (e.key === 'Enter' && !e.shiftKey && editContent.trim()) {
                            e.preventDefault();
                            router.patch(route('comments.update', { comment: comment.id }), {
                                content: editContent.trim(),
                                pokemon_id: pokemonId,
                            }, {
                                preserveScroll: true,
                                onSuccess: () => setEditingId(null),
                            });
                        }
                        if (e.key === 'Escape') setEditingId(null);
                    }}
                    rows={3}
                    className="w-full px-3 py-2 border rounded-md text-sm resize-none focus:outline-none focus:ring-2 focus:ring-gray-400"
                />
            ) : (
                <p>{comment.content}</p>
            )}

            <div className="flex gap-2 mt-2">
                {editingId === comment.id ? (
                    <button onClick={() => setEditingId(null)} className="text-xs text-gray-500 hover:text-gray-700">Cancel</button>
                ) : (
                    <button onClick={() => { setEditingId(comment.id); setEditContent(comment.content); }} className="text-xs text-gray-500 hover:text-gray-700">Edit</button>
                )}
                <button
                    onClick={() => router.delete(route('comments.destroy', { comment: comment.id }), { preserveScroll: true })}
                    className="text-xs text-red-500 hover:text-red-700"
                >
                    Delete
                </button>
                <button 
                    onClick={() => setShowReplyBox(!showReplyBox)}
                    className="text-xs text-blue-700 hover:text-blue-950 ml-auto">
                    {showReplyBox ? 'Cancel' : 'Reply'}
                </button>

                {showReplyBox && (
                    <CommentBox 
                        pokemonId={pokemonId} 
                        parentId={comment.id}
                        onSubmitted={() => setShowReplyBox(false)} 
                    />
                )}
            </div>
        </div>
    );
}