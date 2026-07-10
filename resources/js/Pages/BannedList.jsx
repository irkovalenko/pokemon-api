import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router, usePage } from '@inertiajs/react';
import PrimaryButton from '@/Components/PrimaryButton';

export default function BannedPokemons({auth, pokemons}) {

    const {flash} = usePage().props;
    const user = usePage().props.auth.user;
    const isAdmin = user?.role === 'admin';
    return (
        <AuthenticatedLayout
        user={auth.user}
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Banned Pokemons
                </h2>
            }
        >
            <Head title="Banned pokemons" />

            <div className="py-12">
                              {flash?.message && (
                    <div className="mb-4 rounded-md bg-green-100 text-green-800 px-4 py-3 text-sm">
                        {flash.message}
                    </div>
                )}
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <table>
                        <thead className="text-sm text-body bg-neutral-secondary-soft border-b rounded-base border-default">
                        <tr>
                                       <th scope="col" className="px-6 py-3 font-medium">
                                           Name
                                       </th>
                                       <th scope="col" className="px-6 py-3 font-medium">
                                           Type
                                       </th>
                                       <th scope="col" className="px-6 py-3 font-medium">
                                           Action
                                       </th>
                                   </tr>
                               </thead>
                               <tbody>
                                {pokemons?.data?.map((pokemon) =>
                                (
                                    <tr
                                                                            key={pokemon.id}
                                                                            className="bg-white border-b dark:bg-gray-800 dark:border-gray-700"
                                                                            >
                                                                                <th
                                                                                scope="row"
                                                                                className="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                                                                                >
                                                                               {pokemon.name}
                                                                                </th>
                                                                                 <td
                                            className="px-6 py-4"
                                            >
                                                {pokemon.type}
                                            </td>
                                            <td className ="px-6 py-4"
                                            >
                                                {isAdmin && (
                                                                                <PrimaryButton onClick={(e) => {
                                                                                   e.stopPropagation();
                                                                                router.post(route('pokemons.toggleBan', pokemon.id));
                                           }
                                                                                }>
                                                                                   {pokemon.if_banned  ? '🚫 banned' : '✅ active'}
                                                                                   </PrimaryButton>
                                                                             
                                                                               )
                                                                           }
                                            </td>
                                                                                </tr>
                                                                                  )
                                        )
            }
                                  
                               </tbody>
                           </table>
                          
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
