@props([
    'active' => false,
    'href'
])

@if ($active)
    <a href="{{ $href }}"
       class="inline-flex items-center border-b-2 border-sky-500 px-3 pt-1 text-sm font-medium text-slate-800 transition-colors duration-150"
       aria-current="page">
        {{ $slot }}
    </a>
@else
    <a href="{{ $href }}"
       class="inline-flex items-center border-b-2 border-transparent px-3 pt-1 text-sm font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700 transition-colors duration-150">
        {{ $slot }}
    </a>
@endif
