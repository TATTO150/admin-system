@props(['type'])

@php
    $alertType = [
        'success' => 'alert-success',
        'error' => 'alert-danger',
        'warning' => 'alert-warning', // Bootstrap's alert-warning class has a yellowish-orange color
    ][$type] ?? 'alert-info';
@endphp

<div class="alert {{ $alertType }} alert-dismissible fade show" role="alert">
    {{ $slot }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
