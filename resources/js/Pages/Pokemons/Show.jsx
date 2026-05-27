import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router } from '@inertiajs/react';

export default function Show({ auth, pokemon }) {

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <h2 className="text-xl capitalize font-semibold leading-tight text-gray-800">
                    {pokemon.name}
                </h2>
            }
        >
            <Head title={pokemon.name}/>

            <div className="py-12">
              <div className="mx-auto max-w-3xl sm:px-6 lg:px-8">
                    <div className="bg-white rounded-lg shadow-md p-8 dark:bg-zinc-900 flex flex-col items-center gap-6">
                        
                        <img
                            src={pokemon.image_path}
                            alt={pokemon.name}
                            className="w-48 h-48 object-contain"
                        />

                        <h1 className="text-3xl font-bold capitalize text-gray-800 dark:text-white">
                            {pokemon.name}
                        </h1>
                        <div className="flex justify-between text-sm text-gray-500">
                            <span>Status: {pokemon.if_banned ? '🚫 Banned' : '✅ Active'}</span>
                        </div>
                        {pokemon.cry && (
    <div className="w-full">
        <h2 className="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-3">
            Cry
        </h2>
        <audio controls src={pokemon.cry} className="w-full">
            Your browser does not support audio.
        </audio>
    </div>
)}

                        <div>
                            <h2 className="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                Abilities
                            </h2>
                            <div className="flex flex-wrap gap-2">
                                {pokemon.abilities.map((ability) => (
                                    <span
                                        key={ability.id}
                                        className="px-3 py-1 rounded-full bg-red-100 text-red-700 text-sm capitalize"
                                    >
                                        {ability.name}
                                    </span>
                                ))}
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}  