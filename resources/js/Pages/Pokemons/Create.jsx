import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router, usePage } from '@inertiajs/react';
import { POKEMON_TYPES } from '@/config/pokemonTypes';
import Dropdown from '@/Components/Dropdown';
import { useEffect, useState } from 'react';
import { useForm } from 'react-hook-form';

export default function Create({ auth }) {
    const { errors: serverErrors } = usePage().props;
    const [selectedType, setSelectedType] = useState('');
    const { handleSubmit, register, formState: { errors } } = useForm();

    const [abilities, setAbilities] = useState([]); // [{ name, description, uuid }]
    const [abilityInput, setAbilityInput] = useState('');
    const [abilityDescInput, setAbilityDescInput] = useState('');
    const [suggestions, setSuggestions] = useState([]);

    useEffect(() => {
        if (!abilityInput.trim()) {
            setSuggestions([]);
            return;
        }
        const timeout = setTimeout(() => {
            fetch(route('abilities.search', { query: abilityInput }))
                .then((res) => res.json())
                .then((json) => setSuggestions(json.data));
        }, 300);
        return () => clearTimeout(timeout);
    }, [abilityInput]);

    const addAbility = (ability) => {
        setAbilities([...abilities, ability]);
        setAbilityInput('');
        setAbilityDescInput('');
        setSuggestions([]);
    };

    const addNewAbility = () => {
        if (!abilityInput.trim()) return;
        addAbility({
            name: abilityInput.trim(),
            description: abilityDescInput.trim() || null,
            uuid: null,
        });
    };

    const removeAbility = (index) => {
        setAbilities(abilities.filter((_, i) => i !== index));
    };

    const onSubmit = (values) => {
        if (abilities.length === 0) {
            return;
        }

        router.post(route('pokemons.store'), {
            ...values,
            type: selectedType,
            abilities: JSON.stringify(abilities),
            cry: values.cry?.[0] ?? null,
            image: values.image?.[0] ?? null,
        }, {
            onError: (errors) => {
                console.log('Create failed:', errors);
            },
        });
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Create your own pokemon
                </h2>
            }
        >
            <Head title="Add pokemon" />

            <div className="py-12">
                <div className="mx-auto max-w-3xl sm:px-6 lg:px-8">
                    <div className="bg-white rounded-lg shadow-md p-8 dark:bg-zinc-900 flex flex-col gap-6">

                        <Dropdown>
                            <Dropdown.Trigger>
                                <button className="px-4 py-2 bg-white border border-black rounded-md shadow text-sm text-gray-700 dark:bg-zinc-900 dark:text-white">
                                    {selectedType ? `${POKEMON_TYPES[selectedType]?.icon} ${selectedType}` : 'Select pokemon type ▼'}
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

                        <form onSubmit={handleSubmit(onSubmit)} className="flex flex-col gap-4">
                            <input
                                {...register('name', { required: 'Name is required' })}
                                placeholder="Pokemon name"
                                className="px-4 py-2 border rounded-md text-sm"
                            />
                            {errors.name && <span className="text-red-500 text-sm">{errors.name.message}</span>}

                            <div className="flex flex-wrap gap-2 mb-2">
                                {abilities.map((ability, index) => (
                                    <span
                                        key={ability.uuid ?? `${ability.name}-${index}`}
                                        title={ability.description ?? undefined}
                                        className="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm flex items-center gap-1 capitalize"
                                    >
                                        {ability.name}
                                        <button
                                            type="button"
                                            onClick={() => removeAbility(index)}
                                            className="text-blue-500 hover:text-blue-700"
                                        >
                                            ✕
                                        </button>
                                    </span>
                                ))}
                            </div>

                            <div className="relative">
                                <input
                                    value={abilityInput}
                                    onChange={(e) => setAbilityInput(e.target.value)}
                                    placeholder="Type ability name..."
                                    className="px-4 py-2 border rounded-md text-sm w-full"
                                />
                                {suggestions.length > 0 && (
                                    <div className="absolute z-10 bg-white border rounded-md w-full mt-1 shadow-md dark:bg-zinc-800">
                                        {suggestions.map((s) => (
                                            <button
                                                type="button"
                                                key={s.uuid}
                                                onClick={() => addAbility({ name: s.name, description: s.description, uuid: s.uuid })}
                                                className="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-zinc-700 capitalize"
                                            >
                                                {s.name}
                                            </button>
                                        ))}
                                    </div>
                                )}
                            </div>

                            <input
                                value={abilityDescInput}
                                onChange={(e) => setAbilityDescInput(e.target.value)}
                                placeholder="Description (optional, only used for new abilities)"
                                className="px-4 py-2 border rounded-md text-sm w-full"
                            />

                            <button
                                type="button"
                                onClick={addNewAbility}
                                className="self-start px-4 py-2 bg-blue-600 text-white rounded-md text-sm"
                            >
                                Add ability
                            </button>

                            {serverErrors.abilities && (
                                <span className="text-red-500 text-sm">{serverErrors.abilities}</span>
                            )}
                            {abilities.length === 0 && (
                                <span className="text-red-500 text-sm">
                                    A pokemon must have at least one ability.
                                </span>
                            )}

                            <div className="flex flex-col gap-1">
                                <label className="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Cry (mp3, ogg)
                                </label>
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
                                <input
                                    type="file"
                                    accept=".png,.jpg,.jpeg"
                                    {...register('image')}
                                    className="px-4 py-2 border rounded-md text-sm"
                                />
                            </div>

                            <button
                                type="submit"
                                disabled={abilities.length === 0}
                                className="px-4 py-2 bg-gray-800 text-white rounded-md disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                Create
                            </button>
                        </form>

                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}