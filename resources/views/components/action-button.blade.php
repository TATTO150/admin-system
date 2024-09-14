@props(['type' => 'button', 'class' => '', 'route' => '#', 'method' => 'POST'])

<form action="{{ $route }}" method="POST" style="display: inline-block;" id="{{ $attributes->get('id') }}">
    @csrf
    @method($method)
    <button type="{{ $type }}" class="btn {{ $class }}" onclick="confirmDeletion(event, '{{ $attributes->get('id') }}')">{{ $slot }}</button>
</form>
