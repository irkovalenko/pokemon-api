import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router, usePage} from '@inertiajs/react';
import PrimaryButton from '@/Components/PrimaryButton';

export default function Users({auth, users}) {

    const handlePageChange=(url) => {
        if(url) router.visit(url);
    }
   const {flash} = usePage().props;


    return (
        <AuthenticatedLayout
        user={auth.user}
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Users
                </h2>
            }
        >
            <Head title="Users" />
            {flash.message && (
                <div className="bg-green-100 text-green-700 px-4 py-3 rounded mb-4">
                    {flash.message}
                </div>
            )}

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="ml-auto mb-5 flex items-center gap-4">
                                <PrimaryButton 
                                onClick={(e) => {
                                router.visit(route('users.create'));
                                }}
                                >
                                    Add User
                                </PrimaryButton>
                            </div>
                    
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                            

<div className="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-default">
    <table className="w-full text-sm text-left rtl:text-right text-body">
        <thead className="text-sm text-body bg-neutral-secondary-soft border-b rounded-base border-default">
            <tr>
                <th scope="col" className="px-6 py-3 font-medium">
                    Name
                </th>
                <th scope="col" className="px-6 py-3 font-medium">
                    Email
                </th>
                <th scope="col" className="px-6 py-3 font-medium">
                    Role
                </th>
                <th scope="col" className="px-6 py-3 font-medium">
                    Date
                </th>
            </tr>
        </thead>
        <tbody>
            {users?.data?.map((user) =>
                                    (
                                        <tr
                                        key={user.id}
                                        onClick={() => router.visit(route('users.edit', user.id))}
                                        className="bg-white border-b dark:bg-gray-800 dark:border-gray-700"
                                        >
                                            <th
                                            scope="row"
                                            className="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                                            >
                                           {user.name}
                                            </th>
                                            <td
                                            className="px-6 py-4"
                                            >
                                                {user.email}
                                            </td>
                                             <td
                                            className="px-6 py-4"
                                            >
                                                {user.role}
                                            </td>
                                             <td
                                            className="px-6 py-4"
                                            >
                                                {user.created_at}
                                            </td>
                                            </tr>
                                            )
                                        )
            }
        </tbody>
    </table>
    <div>
        { users.links.map((link, id) =>(
            <button
            key = {id}
            onClick={()=> handlePageChange(link.url)}
            disabled={!link.url}
            className="px-3 py-1 rounded"
            dangerouslySetInnerHTML={{__html:link.label}}
            />

        ))}
    </div>
    
</div>

                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
