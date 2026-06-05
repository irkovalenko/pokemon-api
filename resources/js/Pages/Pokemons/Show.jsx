import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router} from '@inertiajs/react';
import PrimaryButton from '@/Components/PrimaryButton';
import { SpeakerWaveIcon } from '@heroicons/react/24/solid';
import { useRef } from 'react'; 
import { POKEMON_TYPES } from '@/config/pokemonTypes';
import SecondaryButton from '@/Components/SecondaryButton';

export default function Show({ auth, pokemon, canBeDeletedOrUpdated}) {

    const { data } = pokemon;
    const audioRef = useRef(null);
    const typeInfo = POKEMON_TYPES[data.type];

    console.log(pokemon);

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

                        <h3 className="self-start px-3 py-1 rounded-full border border-solid border-gray-800  text-gray-700 text-sm">
                            {data.type} {typeInfo?.icon}
                            </h3>

                            {canBeDeletedOrUpdated && (
                            
                                    <SecondaryButton 
                                    onClick={(e) => {
                                    router.visit(route('pokemons.edit', { id: data.id }));
                                    }}
                                    >
                                        Edit Pokemon
                                    </SecondaryButton>
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
                                {data.abilities.map((ability) => (
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