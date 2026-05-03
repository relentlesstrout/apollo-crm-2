@if (session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 text-sm mt-4 px-4 py-3 rounded-md">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="bg-red-50 border border-red-200 text-red-800 text-sm mt-4 px-4 py-3 rounded-md">
        {{ session('error') }}
    </div>
@endif
