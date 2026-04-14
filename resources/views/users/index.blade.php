<x-layouts.app>
    <div>
        <x-title
            title="Manage Accounts"
            description="Manage all user accounts from here">
        </x-title>
    </div>
    <div>
        <x-react
            name="Table"
            :props="[
                'users' => $users,
                'routes' => [
                    'show' => route('users.show', ['user' =>'__ID__']),
                    'edit' => route('users.edit', ['user' =>'__ID__']),
                    'destroy' => route('users.destroy', ['user' =>'__ID__']),
                ]
            ]"
        />
    </div>
    <div class="my-4">
        {{ $users->links() }}
    </div>
</x-layouts.app>
