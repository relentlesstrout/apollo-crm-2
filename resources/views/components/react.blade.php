@props([
    'name',
    'props' => [],
])

<div
    data-react-component="{{ $name }}"
    data-react-props='@json($props)'
></div>
