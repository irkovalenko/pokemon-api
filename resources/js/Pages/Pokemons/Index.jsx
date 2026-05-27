import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router } from '@inertiajs/react';

export default function Pokemons({ auth, pokemons }) {

    const handlePageChange = (url) => {
        if (url) router.visit(url);
    }

    const getIdFromUrl = (url) => {
        const parts = url.split('/').filter(Boolean);
        return parts[parts.length - 1];
    }

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

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">

                    <div className="grid gap-6 lg:grid-cols-4">
                        {pokemons.data.map((pokemon) => {
                            const id = getIdFromUrl(pokemon.url);
                            const image = `https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/${id}.png`;

                            return (
                                <div
                                    key={pokemon.name}
                                    className="flex flex-col items-center gap-4 rounded-lg bg-white p-6 shadow-md hover:shadow-lg transition cursor-pointer dark:bg-zinc-900"
                                    onClick={() => router.visit(route('pokemons.show', id))}
                                >
                                    <img
                                        src={image}
                                        alt={pokemon.name}
                                        className="w-32 h-32 object-contain"
                                    />
                                    <h2 className="text-lg font-semibold capitalize text-gray-800 dark:text-white">
                                        {pokemon.name}
                                    </h2>
                                </div>
                            );
                        })}
                    </div>

                    <div className="flex justify-center gap-2 mt-8">
                        {pokemons.links.map((link, id) => (
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
        </AuthenticatedLayout>
    );
}