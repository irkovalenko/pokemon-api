import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router} from '@inertiajs/react';
import PrimaryButton from '@/Components/PrimaryButton';
import { SpeakerWaveIcon } from '@heroicons/react/24/solid';
import { useRef, useState } from 'react'; 
import SecondaryButton from '@/Components/SecondaryButton';
import CommentBox from '@/Components/CommentBox';
import CommentItem from '@/Components/CommentItem';

export default function Show({ auth, pokemon, canBeDeletedOrUpdated, pokemonTypes}) {

    const { data } = pokemon; // data resource json wraps by default in data
    const currentUser = auth.user;
    const isAdmin = currentUser?.role === 'admin';
    const audioRef = useRef(null);
    const type = pokemonTypes.find((t) => t.value === data.type);
    const [showCommentBox, setShowCommentBox] = useState(false);
    const [editingId, setEditingId] = useState(null);
    const [editContent, setEditContent] = useState('');
    const [showFullDescription, setShowFullDescription] = useState(false);

    const fullDescription = "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum."

    const description = showFullDescription 
        ? fullDescription 
        : fullDescription.substring(0, 50) + '...';


    return (
        <AuthenticatedLayout
            currentUser={auth.currentUser}
            header={
                <h2 className="text-xl capitalize font-semibold leading-tight text-gray-800">
                    {data.name}
                </h2>
            }
        >
            <Head title={data.name}/>

            <div className="py-12">
                <div className="mx-auto max-w-3xl sm:px-6 lg:px-8">

                    <div className="bg-white rounded-lg shadow-md p-8 dark:bg-zinc-900 flex flex-col items-center gap-6">
                        <div className="flex items-center justify-between w-full">
                            <h3 className="self-start px-3 py-1 rounded-full border border-solid border-gray-800 text-gray-700 text-sm">
                                {data.type} {type?.icon}
                            </h3>
                            {canBeDeletedOrUpdated && (
                                <PrimaryButton onClick={() => router.visit(route('pokemons.edit', { uuid: data.uuid }))}>
                                    Edit data
                                </PrimaryButton>
                            )}

                             {isAdmin && !data.if_banned && (
                                    <PrimaryButton onClick={(e) => {
                                    router.post(route('pokemons.toggleBan', data.uuid));
                                                                      }
                                    }>
                                🚫 hide pokemon
                                </PrimaryButton>
                                                                                                        
                                )
                            }
                        </div>

                        {data.user && <h3>User: {data.user}</h3>}

                        <img
                            src={data.image_path}
                            alt={data.name}
                            className="w-48 h-48 object-contain"
                        />

                        <h1 className="text-3xl font-bold capitalize text-gray-800 dark:text-white">
                            {data.name}
                        </h1>

                        <div className="flex justify-between text-sm text-gray-500">
                            <span>Status: {data.if_banned ? '🚫 Banned' : '✅ Active'}</span>
                        </div>

                        {data.cry && (
                            <div>
                                <audio ref={audioRef} src={data.cry}>
                                    Your browser does not support audio.
                                </audio>
                                <PrimaryButton onClick={() => audioRef.current.play()}>
                                    Sound
                                    <SpeakerWaveIcon className="w-4 h-4 ml-2" />
                                </PrimaryButton>
                            </div>
                        )}

                        <div>
                            <h2 className="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                Abilities
                            </h2>
                            <div className="flex flex-wrap gap-2">
    {data.abilities.map((ability) => (
        <div key={ability.uuid} className="relative group">
            <span className="px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-sm capitalize cursor-help">
                {ability.name}
            </span>

            {ability.description && (
                <div className="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-56 rounded-md bg-gray-900 text-white text-xs px-3 py-2 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-10">
                    {ability.description}
                    <div className="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-900" />
                </div>
            )}
        </div>
    ))}
</div>
                        </div>

                        <div className="mt-4">
                                {description}
                            
                             <button
              onClick =  {() => setShowFullDescription((prevState) => !prevState )}
              className="text-indigo-500 mb-5 hover:text-indigo-600">
                {showFullDescription ? 'Less' : 'More'}
              </button>
                            </div>
                    </div>

                    <div className="mt-6">
    <div className="flex items-center justify-between mb-4">
        <span className="text-lg font-semibold text-gray-700 dark:text-gray-300">Comments</span>
        <SecondaryButton onClick={() => setShowCommentBox(!showCommentBox)}>
            {showCommentBox ? 'Cancel' : 'Add comment'}
        </SecondaryButton>
    </div>

    {showCommentBox && (
        <div className="mb-4">
            <CommentBox pokemonId={data.uuid} onSubmitted={() => setShowCommentBox(false)} />
        </div>
    )}

    <div className="flex flex-col gap-3">
        {data.comments.map((comment) => (
            <div key={comment.id}>
            <CommentItem
            key={comment.id}
            comment={comment}
            pokemonId={data.uuid}
            editingId={editingId}
            setEditingId={setEditingId}
            editContent={editContent}
            setEditContent={setEditContent}

            />
            {comment.replies?.length > 0 && (
            <div className="ml-6 mt-2 flex flex-col gap-2 border-l-2 border-gray-200 pl-3">
                {comment.replies.map((reply) => (
                    <CommentItem
                        key={reply.id}
                        comment={reply}
                        dataId={data.uuid}
                        editingId={editingId}
                        setEditingId={setEditingId}
                        editContent={editContent}
                        setEditContent={setEditContent}
                    />
                ))}
            </div>
        )}
        </div>
        ))}
    </div>
</div>

                </div>
            </div>
        </AuthenticatedLayout>
    );
}