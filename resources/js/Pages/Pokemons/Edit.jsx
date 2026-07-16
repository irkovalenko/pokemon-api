import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router, usePage } from '@inertiajs/react';
import { POKEMON_TYPES } from '@/config/pokemonTypes';
import Dropdown from '@/Components/Dropdown';
import { useEffect, useState } from 'react';
import { useForm } from 'react-hook-form';
import PrimaryButton from '@/Components/PrimaryButton';
import axios from 'axios';

export default function Edit({ auth, pokemon, canBeDeletedOrUpdated }) {
    const { errors: serverErrors } = usePage().props;
    const [selectedType, setSelectedType] = useState(pokemon.data.type);
    const { handleSubmit, register, formState: { errors } } = useForm();

    // All abilities currently attached to this pokemon. Any of them can be
    // removed from this pokemon (detached), regardless of source. None of
    // them can have their name/description edited here — that would mutate
    // the shared ability record itself, affecting every other pokemon that
    // uses it. api_id is kept only to show the 🔒 "from PokeAPI" badge.
    const [attachedAbilities, setAttachedAbilities] = useState(
        pokemon.data.abilities.map((a) => ({
            uuid: a.uuid,
            name: a.name,
            description: a.description,
            api_id: a.api_id,
            canEdit: a.canEdit,
        }))
    );

    // Inline editing of an already-attached ability's own name/description.
    // Only shown when the ability's canEdit flag is true (creator or admin).
    const [editingIndex, setEditingIndex] = useState(null);
    const [editName, setEditName] = useState('');
    const [editDescription, setEditDescription] = useState('');
    const [savingEdit, setSavingEdit] = useState(false);
    const [editError, setEditError] = useState(null);

    const startEditing = (index) => {
        setEditingIndex(index);
        setEditName(attachedAbilities[index].name);
        setEditDescription(attachedAbilities[index].description ?? '');
        setEditError(null);
    };

    const cancelEditing = () => {
        setEditingIndex(null);
        setEditError(null);
    };

    const saveEditing = async (index) => {
        const ability = attachedAbilities[index];
        setSavingEdit(true);
        setEditError(null);

        try {
            const { data } = await axios.patch(route('abilities.update', ability.uuid), {
                name: editName.trim(),
                description: editDescription.trim() || null,
            });

            const updated = [...attachedAbilities];
            updated[index] = {
                ...updated[index],
                name: data.data.name,
                description: data.data.description,
            };
            setAttachedAbilities(updated);
            setEditingIndex(null);
        } catch (err) {
            setEditError(
                err.response?.data?.message ?? 'Could not save changes to this ability.'
            );
        } finally {
            setSavingEdit(false);
        }
    };

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
        setAttachedAbilities([...attachedAbilities, ability]);
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
            api_id: null,
            canEdit: false, // not persisted yet — edited directly via these fields, not inline
        });
    };

    const removeAbility = (index) => {
        setAttachedAbilities(attachedAbilities.filter((_, i) => i !== index));
    };

    const onSubmit = (values) => {
        if (attachedAbilities.length === 0) {
            return;
        }

        const abilities = attachedAbilities;

        router.post(route('pokemons.update', pokemon.data.uuid), {
            name: values.name,
            type: selectedType || pokemon.data.type,
            abilities: JSON.stringify(abilities),
            cry: values.cry?.[0] ?? null,
            image: values.image?.[0] ?? null,
        }, {
            forceFormData: true,
            onError: (errors) => {
                console.log('Update failed:', errors);
            },
        });
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
                                    onClick={() => router.delete(route('pokemons.delete', { uuid: pokemon.data.uuid }))}
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

                            {/* All abilities currently attached to this pokemon.
                                Any can be removed from this pokemon; none can have
                                their name/description edited here (that would change
                                the shared ability record for every other pokemon). */}
                            <div className="flex flex-col gap-2">
                                <label className="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Abilities
                                </label>

                                {attachedAbilities.length > 0 && (
                                    <div className="flex flex-col gap-2">
                                        {attachedAbilities.map((ability, index) => (
                                            <div
                                                key={ability.uuid ?? `${ability.name}-${index}`}
                                                className={`flex flex-col gap-1 px-3 py-2 rounded-md border ${
                                                    ability.api_id
                                                        ? 'bg-gray-100 dark:bg-zinc-800 border-gray-200 dark:border-zinc-700'
                                                        : 'bg-blue-50 dark:bg-zinc-800 border-blue-100 dark:border-zinc-700'
                                                }`}
                                            >
                                                {editingIndex === index ? (
                                                    <div className="flex flex-col gap-2">
                                                        <input
                                                            value={editName}
                                                            onChange={(e) => setEditName(e.target.value)}
                                                            className="px-2 py-1 border rounded text-sm"
                                                            placeholder="Ability name"
                                                        />
                                                        <input
                                                            value={editDescription}
                                                            onChange={(e) => setEditDescription(e.target.value)}
                                                            className="px-2 py-1 border rounded text-sm"
                                                            placeholder="Description (optional)"
                                                        />
                                                        {editError && (
                                                            <span className="text-xs text-red-500">{editError}</span>
                                                        )}
                                                        <div className="flex gap-2">
                                                            <button
                                                                type="button"
                                                                disabled={savingEdit}
                                                                onClick={() => saveEditing(index)}
                                                                className="px-3 py-1 bg-green-600 text-white rounded text-xs disabled:opacity-50"
                                                            >
                                                                {savingEdit ? 'Saving...' : 'Save'}
                                                            </button>
                                                            <button
                                                                type="button"
                                                                onClick={cancelEditing}
                                                                className="px-3 py-1 bg-gray-300 dark:bg-zinc-700 rounded text-xs"
                                                            >
                                                                Cancel
                                                            </button>
                                                        </div>
                                                    </div>
                                                ) : (
                                                    <>
                                                        <div className="flex items-center justify-between">
                                                            <span className="text-sm font-medium text-gray-800 dark:text-gray-100 capitalize">
                                                                {ability.api_id ? '🔒 ' : ''}{ability.name}
                                                            </span>
                                                            <div className="flex items-center gap-3">
                                                                {ability.canEdit && ability.uuid && (
                                                                    <button
                                                                        type="button"
                                                                        onClick={() => startEditing(index)}
                                                                        className="text-gray-500 hover:text-gray-700 text-sm"
                                                                    >
                                                                        ✏️ edit
                                                                    </button>
                                                                )}
                                                                <button
                                                                    type="button"
                                                                    onClick={() => removeAbility(index)}
                                                                    className="text-blue-500 hover:text-blue-700 text-sm"
                                                                >
                                                                    ✕ remove
                                                                </button>
                                                            </div>
                                                        </div>
                                                        {ability.description && (
                                                            <span className="text-xs text-gray-500 dark:text-gray-400">
                                                                {ability.description}
                                                            </span>
                                                        )}
                                                    </>
                                                )}
                                            </div>
                                        ))}
                                    </div>
                                )}

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
                                                    onClick={() => addAbility({ name: s.name, description: s.description, uuid: s.uuid, api_id: s.api_id, canEdit: s.canEdit })}
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

                                {errors.abilities && <span className="text-red-500 text-sm">{errors.abilities.message}</span>}
                                {serverErrors.abilities && (
                                    <span className="text-red-500 text-sm">{serverErrors.abilities}</span>
                                )}
                                {attachedAbilities.length === 0 && (
                                    <span className="text-red-500 text-sm">
                                        A pokemon must have at least one ability.
                                    </span>
                                )}
                            </div>

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

                            <button
                                type="submit"
                                disabled={attachedAbilities.length === 0}
                                className="px-4 py-2 bg-gray-800 text-white rounded-md disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                Update
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}