import UpdateForm from '@/Components/UpdateForm';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';


export default function Create({ }) {

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    User
                </h2>
            }
        >
            <Head title="User" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                    <div className="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                        <UpdateForm
                        routeName="users.store"
                        cancelRoute="users"
                        method="post"
                        fields={[
                            { name: 'name', label: 'Name', value: '', type: 'text' , uppercase: true},
                            { name: 'email', label: 'Email', value: '', type: 'email' },
                        ]}
/>
                    </div>

                </div>
            </div>
        </AuthenticatedLayout>
    );
}
