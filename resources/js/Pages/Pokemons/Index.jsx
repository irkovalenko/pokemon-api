import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router, usePage } from '@inertiajs/react';
import { POKEMON_TYPES } from '@/config/pokemonTypes';
import Dropdown from '@/Components/Dropdown';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';


export default function Pokemons({ auth, pokemons }) {
    const user = usePage().props.auth.user;
    const isAdmin = user?.role === 'admin';
    const handlePageChange = (url) => {
        if (url) router.visit(url, { preserveState: true});
    }


    const handleTypeFilter = (type) => {
    router.visit(route('pokemons-dashboard'), {
        data: { type },
        preserveState: true,
    });
};

  const handleNameFilter = (name) => {
    router.visit(route('pokemons-dashboard'), {
        data: { name },
        preserveState: true,
    });
};

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Pokemons
                </h2>
            }
        >
            <Head title="Pokemons" />
            <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
<div className = "py-6 flex items-center gap-4">
            <Dropdown>
    <Dropdown.Trigger>
        <button className="px-4 py-2 bg-white rounded-md shadow text-sm text-gray-700 dark:bg-zinc-900 dark:text-white">
            Filter by pokemon type ▼
        </button>
    </Dropdown.Trigger>
    <Dropdown.Content align="left" width="70">
        {/* Reset filter */}
        <button
            onClick={() => handleTypeFilter('')}
            className="block w-full px-4 py-2 text-start text-sm text-gray-700 hover:bg-gray-100"
        >
            All types
        </button>

        {/* Type options */}
        {Object.entries(POKEMON_TYPES).map(([type, info]) => (
            <button
                key={type}
                onClick={() => handleTypeFilter(type)}
                className="block w-full px-4 py-2 text-start text-sm text-gray-700 hover:bg-gray-100 capitalize"
            >
                {info.icon} {type}
            </button>
        ))}
    </Dropdown.Content>
</Dropdown>

 <input
        type="text"
        placeholder="Search by name..."
        onChange={(e) => handleNameFilter(e.target.value)}
        className="px-4 py-2 bg-white rounded-md shadow text-sm text-gray-700 dark:bg-zinc-900 dark:text-white border border-gray-200"
    />
 
    <div className="ml-auto">
        <SecondaryButton 
        onClick={(e) => {
        router.visit(route('pokemons.create'));
        }}
        >
            Add Pokemon
        </SecondaryButton>
    </div>
</div>

            <div className="py-3">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="grid gap-6 lg:grid-cols-4">
                        {pokemons.data.map((pokemon) => {
                            const typeInfo = POKEMON_TYPES[pokemon.type];
                            console.log(pokemons)
                            
                            return(
                                <div
                                    key={pokemon.name}
                                    className="flex flex-col items-center gap-4 rounded-lg bg-white p-6 shadow-md hover:shadow-lg transition cursor-pointer dark:bg-zinc-900"
                                    onClick={() => {
                                        router.visit(route('pokemons.show', pokemon.name))}}
                                >
                                    {isAdmin && (
                                    <div className ="self-start">
                                     <PrimaryButton onClick={(e) => {
                                        e.stopPropagation();
                                     router.post(route('pokemons.toggleBan', pokemon.id));
}
                                     }>
                                        {pokemon.if_banned  ? '🚫 banned' : '✅ active'}
                                        </PrimaryButton>
                                     </div>
                                    )
                                }
                                    <img
                                        src={pokemon.image_path}
                                        alt={pokemon.name}
                                        className="w-32 h-32 object-contain"
                                    />
                                    <h2 className="text-lg font-semibold capitalize text-gray-800 dark:text-white">
                                        {pokemon.name}
                                    </h2>
                                    <span>{pokemon.type} {typeInfo?.icon} </span>
                                </div>
                            );
                        })
                        }
                    </div>

                    <div className="flex justify-center gap-2 mt-8">
                        {pokemons.meta.links.map((link, id) => (
                            <button
                                key={id}
                                onClick={() => handlePageChange(link.url)}
                                disabled={!link.url}
                                className={`px-3 py-1 rounded border ${link.active ? 'bg-red-500 text-white' : 'bg-white text-gray-700'} disabled:opacity-50`}
                                dangerouslySetInnerHTML={{ __html: link.label }}
                            />
                        ))}
                    </div>

                </div>
            </div>
            </div>
        </AuthenticatedLayout>
    );
}