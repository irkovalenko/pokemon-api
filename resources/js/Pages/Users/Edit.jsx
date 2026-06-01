import UpdateForm from '@/Components/UpdateForm';
import PrimaryButton from '@/Components/PrimaryButton';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router } from '@inertiajs/react';


export default function Edit({ user, roles}) {
    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    User {user.name}
                </h2>
            }
        >
            <Head title="User" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                    <div className="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                        <UpdateForm
                        routeName="users.update"
                        cancelRoute="users"
                        routeParams={user.id}
                        fields={[
                            { name: 'name', label: 'Name', value: user.name },
                            { name: 'email', label: 'Email', value: user.email, type: 'email' },
                            { 
    name: 'role', 
    label: 'Role', 
    value: user.role, 

    type: 'select', 
    options: roles.map(role => ({ value: role, label: role }))
}
                        ]}
/>
<div className="flex items-center gap-4 mt-4">
<PrimaryButton className="bg-red-500 hover:bg-red-600 ml-auto" onClick={() => {
     console.log('delete clicked', user.id);
    router.delete(route('users.delete', { user: user.id }));
    }}>
                            Delete user
                        </PrimaryButton>
                        </div>
                    </div>

                </div>
            </div>
        </AuthenticatedLayout>
    );
}
