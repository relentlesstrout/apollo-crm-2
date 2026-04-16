import * as React from 'react'

import {
    flexRender,
    getCoreRowModel,
    getFilteredRowModel,
    getSortedRowModel,
    useReactTable,
} from '@tanstack/react-table'
import {useState} from "react";

export default function Table({ users, routes }) {

    const[globalFilter, setGlobalFilter] = useState('')

    const [columnFilters, setColumnFilters] = useState([]);

    const[sorting, setSorting] = React.useState([])

    const columns = [
        {
            header: 'Name',
            accessorKey: 'name',
        },
        {
            header: 'Phone',
            accessorKey: 'phone',
        },
        {
            header: 'Email',
            accessorKey: 'email',
        },
        {
            header: 'Role',
            accessorKey: 'role',
            cell: (info) => {
                const role = info.getValue();
                const styles = {
                    admin:   'bg-sky-50 text-sky-700',
                    cleaner: 'bg-amber-50 text-amber-700',
                    customer:'bg-slate-100 text-slate-600',
                }
                return (
                    <span className={`${styles[role] ?? styles.customer} text-s font-medium px-2.5 py-0.5 rounded-full`}>
                        {role}
                    </span>
                )
            }
        },
        {
            header: 'Action',
            enableSorting: false,
            cell: (info) => {
                const user = info.row.original;
                const showUrl = routes.show.replace('__ID__', user.id);
                const editUrl = routes.edit.replace('__ID__', user.id);
                const destroyUrl = routes.destroy.replace('__ID__', user.id);

                return (
                    <div className="flex items-center gap-2">
                        <a
                            href={showUrl}
                            className="bg-white hover:bg-slate-100 text-slate-700 text-sm font-medium px-4 py-2 rounded-md border border-slate-200 transition-colors duration-150"
                        >
                            View
                        </a>
                        <a
                            href={editUrl}
                            className="bg-sky-500 hover:bg-sky-600 text-white text-sm font-medium px-4 py-2 rounded-md transition-colors duration-150"
                        >
                            Edit
                        </a>
                        <form
                            method="POST"
                            action={destroyUrl}
                            onSubmit={(e) => !confirm(`Are you sure you want to delete ${user.name}?`) && e.preventDefault()}
                        >
                            <input type="hidden" name="_token" value={document.querySelector('meta[name="csrf-token"]').content} />
                            <input type="hidden" name="_method" value="DELETE" />
                            <button
                                type="submit"
                                className="bg-white hover:bg-red-100 text-red-600 text-sm font-medium px-4 py-2 rounded-md border border-red-200 transition-colors duration-150"
                            >
                                Delete
                            </button>
                        </form>
                    </div>
                )
            }
        }
    ]

    const table = useReactTable(
        {
            data: users.data,
            columns,
            getCoreRowModel: getCoreRowModel(),
            getFilteredRowModel: getFilteredRowModel(),
            getSortedRowModel: getSortedRowModel(),
            state: {
                globalFilter,
                columnFilters,
                sorting,
            },
            onGlobalFilterChange: setGlobalFilter,
            onColumnFiltersChange: setColumnFilters,
            onSortingChange: setSorting,
        });

    return (
        <div>
            <div className="my-4">
                <input
                    value={globalFilter}
                    onChange={e => setGlobalFilter(e.target.value)}
                    placeholder="Search..."
                    className="w-50 rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                />
            </div>
            

            <div className="rounded-md border border-slate-200 overflow-hidden">
                <table className="min-w-full divide-y divide-slate-200">
                    <thead className="bg-slate-50">
                    {table.getHeaderGroups().map((headerGroup) => (
                        <tr key={headerGroup.id}>
                            {headerGroup.headers.map((header) => (
                                <th
                                    key={header.id}
                                    onClick={header.column.getToggleSortingHandler()}
                                    className="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider"
                                    style={{cursor: header.column.getCanSort() ? 'pointer' : 'default'}}
                                >
                                    {flexRender(header.column.columnDef.header, header.getContext())}

                                    {header.column.getIsSorted() === 'asc' ? ' ↑'
                                        : header.column.getIsSorted() === 'desc' ? ' ↓'
                                            : null
                                    }
                                </th>
                            ))}
                        </tr>
                    ))}
                    </thead>
                    <tbody className="bg-white divide-y divide-slate-200">
                    {table.getRowModel().rows.map((row) => (
                        <tr key={row.id} className="hover:bg-slate-50">
                            {row.getVisibleCells().map((cell) => (
                                <td key={cell.id} className="px-6 py-4 text-sm text-slate-800">
                                    {flexRender(
                                        cell.column.columnDef.cell,
                                        cell.getContext()
                                    )}
                                </td>
                            ))}
                        </tr>
                    ))}
                    </tbody>
                </table>
            </div>

        </div>
    )
}
