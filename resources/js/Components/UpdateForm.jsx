
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import { Transition } from '@headlessui/react';
import { useForm } from '@inertiajs/react';

export default function UpdateForm({
    fields,
    routeName,
    routeParams,
    className = '',
}) {
    const initialData = fields.reduce((acc, field) => {
        acc[field.name] = field.value;
        return acc;
    }, {});

    const { data, setData, patch, errors, processing, recentlySuccessful } =
        useForm(initialData);

    const submit = (e) => {
        e.preventDefault();
        patch(route(routeName, routeParams));
    };

    return (
        <section className={className}>
            <form onSubmit={submit} className="mt-6 space-y-6">
                {fields.map((field) => (
                    <div key={field.name}>
                        <InputLabel htmlFor={field.name} value={field.label} />
                          {field.type === 'select' ? (
            <select
                id={field.name}
                className="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                value={data[field.name]}
                onChange={(e) => setData(field.name, e.target.value)}
            >
                {field.options.map((option) => (
                    <option key={option} value={option.value}>{option.label}</option>
                ))}
            </select>
        ) : (
                        <TextInput
                            id={field.name}
                            type={field.type || 'text'}
                            className="mt-1 block w-full"
                            value={data[field.name]}
                            onChange={(e) => setData(field.name, e.target.value)}
                            required
                        />
        )}
                        <InputError className="mt-2" message={errors[field.name]} />
                    </div>
                ))}

                <div className="flex items-center gap-4">
                    <PrimaryButton disabled={processing}>Save</PrimaryButton>
                    <Transition show={recentlySuccessful} enter="transition ease-in-out" enterFrom="opacity-0" leave="transition ease-in-out" leaveTo="opacity-0">
                        <p className="text-sm text-gray-600">Saved.</p>
                    </Transition>
                </div>
            </form>
        </section>
    );
}