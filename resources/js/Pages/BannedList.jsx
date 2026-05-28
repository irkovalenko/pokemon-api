import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function BannedPokemons({auth, pokemons}) {
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
                                           Banned_on
                                       </th>
                                       <th scope="col" className="px-6 py-3 font-medium">
                                           Make visible
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
