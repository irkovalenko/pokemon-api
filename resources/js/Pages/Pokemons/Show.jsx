import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router} from '@inertiajs/react';
import PrimaryButton from '@/Components/PrimaryButton';
import { SpeakerWaveIcon } from '@heroicons/react/24/solid';
import { useRef } from 'react'; 
import { POKEMON_TYPES } from '@/config/pokemonTypes';

export default function Show({ auth, pokemon }) {

    const audioRef = useRef(null);
    const typeInfo = POKEMON_TYPES[pokemon.type];

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
                        <h3 className="self-start px-3 py-1 rounded-full border border-solid border-gray-800  text-gray-700 text-sm">
                            {pokemon.type} {typeInfo?.icon}
                            </h3>
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
    <div>
        
       <audio ref={audioRef} src={pokemon.cry}>
                Your browser does not support audio.
            </audio>
        <PrimaryButton onClick={()=> audioRef.current.play()}> 
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
                                {pokemon.abilities.map((ability) => (
                                    <span
                                        key={ability.id}
                                        className="px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-sm capitalize"
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