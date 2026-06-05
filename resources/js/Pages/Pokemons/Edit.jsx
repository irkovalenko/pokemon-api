import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router } from '@inertiajs/react';
import { POKEMON_TYPES } from '@/config/pokemonTypes';
import Dropdown from '@/Components/Dropdown';
import { useState } from 'react';
import { useForm } from 'react-hook-form';
import PrimaryButton from '@/Components/PrimaryButton';

export default function Edit({ auth, pokemon, canBeDeletedOrUpdated }) {
    const [selectedType, setSelectedType] = useState(pokemon.data.type);
    const { handleSubmit, register, formState: { errors } } = useForm();
    const [abilities, setAbilities] = useState(pokemon.data.abilities.map(a => a.name));
    const [abilityInput, setAbilityInput] = useState('');

    const addAbility = (e) => {
        if (e.key === 'Enter' && abilityInput.trim()) {
            e.preventDefault();
            setAbilities([...abilities, abilityInput.trim()]);
            setAbilityInput('');
        }
    };

    const removeAbility = (index) => {
        setAbilities(abilities.filter((_, i) => i !== index));
    };

    const onSubmit = (values) => {
        router.post(route('pokemons.update', pokemon.data.id), {
            //_method: 'PATCH',
            name: values.name,
            type: selectedType || pokemon.data.type,
            abilities: JSON.stringify(abilities),
            cry: values.cry?.[0] ?? null,
            image: values.image?.[0] ?? null,
        },
        {forceFormData:true}
    );
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Edit pokemon {pokemon.data.name}
                </h2>
            }
        >
            <Head title="Edit pokemon" />
            <div className="py-12">
                <div className="mx-auto max-w-3xl sm:px-6 lg:px-8">
                    <div className="bg-white rounded-lg shadow-md p-8 dark:bg-zinc-900 flex flex-col gap-6">
                        <div className="flex items-center gap-4">
                            <Dropdown>
                                <Dropdown.Trigger>
                                    <button className="px-4 py-2 bg-white border border-black rounded-md shadow text-sm text-gray-700 dark:bg-zinc-900 dark:text-white">
                                        {selectedType ? `${POKEMON_TYPES[selectedType]?.icon} ${selectedType}` : `Current type: ${pokemon.data.type}`}
                                    </button>
                                </Dropdown.Trigger>
                                <Dropdown.Content align="left" width="70">
                                    <button
                                        onClick={() => setSelectedType('')}
                                        className="block w-full px-4 py-2 text-start text-sm text-gray-700 hover:bg-gray-100"
                                    >
                                        None
                                    </button>
                                    {Object.entries(POKEMON_TYPES).map(([type, info]) => (
                                        <button
                                            key={type}
                                            onClick={() => setSelectedType(type)}
                                            className="block w-full px-4 py-2 text-start text-sm text-gray-700 hover:bg-gray-100 capitalize"
                                        >
                                            {info.icon} {type}
                                        </button>
                                    ))}
                                </Dropdown.Content>
                            </Dropdown>
                            {canBeDeletedOrUpdated && (
                                <PrimaryButton
                                    className="bg-red-500 hover:bg-red-600 ml-auto"
                                    onClick={() => router.delete(route('pokemons.delete', { id: pokemon.data.id }))}
                                >
                                    Delete Pokemon
                                </PrimaryButton>
                            )}
                        </div>

                        <form onSubmit={handleSubmit(onSubmit)} className="flex flex-col gap-4">
                            <input
                                {...register('name', { required: 'Name is required' })}
                                placeholder="Pokemon name"
                                defaultValue={pokemon.data.name}
                                className="px-4 py-2 border rounded-md text-sm"
                            />
                            {errors.name && <span className="text-red-500 text-sm">{errors.name.message}</span>}

                            <div className="flex flex-wrap gap-2 mb-2">
                                {abilities.map((ability, index) => (
                                    <span key={index} className="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm flex items-center gap-1">
                                        {ability}
                                        <button type="button" onClick={() => removeAbility(index)}>✕</button>
                                    </span>
                                ))}
                            </div>
                            <input
                                value={abilityInput}
                                onChange={(e) => setAbilityInput(e.target.value)}
                                onKeyDown={addAbility}
                                placeholder="Type ability and press Enter..."
                                className="px-4 py-2 border rounded-md text-sm w-full"
                            />

                            <div className="flex flex-col gap-1">
                            <label className="text-sm font-medium text-gray-700 dark:text-gray-300">
                                Cry (mp3, ogg)
                            </label>
                            {pokemon.data.cry && (
                                <p className="text-xs text-gray-500">Current: {pokemon.data.cry}</p>
                            )}
                            <input
                                type="file"
                                accept=".ogg,.mp3"
                                {...register('cry')}
                                className="px-4 py-2 border rounded-md text-sm"
                            />
                        </div>

                            <div className="flex flex-col gap-1">
                            <label className="text-sm font-medium text-gray-700 dark:text-gray-300">
                                Image (png, jpg, jpeg)
                            </label>
                            {pokemon.data.image_path && (
                                <img src={pokemon.data.image_path} alt="current" className="w-16 h-16 object-contain" />
                            )}
                            <input
                                type="file"
                                accept=".png,.jpg,.jpeg"
                                {...register('image')}
                                className="px-4 py-2 border rounded-md text-sm"
                            />
                        </div>

                            <button type="submit" className="px-4 py-2 bg-gray-800 text-white rounded-md">
                                Update
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}