@props(['url'])
<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            @if (trim($slot) === 'SistemaDeGestionAdministrativa')
                <img src="https://i.ibb.co/Dtg42J6/CTraterra.jpg" 
                     alt="Logo" 
                     style="width: 200px; height: 200px; border-radius: 50%; object-fit: cover;">
            @else
                {{ $slot }}
            @endif
        </a>
    </td>
</tr>

