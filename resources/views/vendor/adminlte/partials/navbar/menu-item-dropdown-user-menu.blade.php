@php( $logout_url = View::getSection('logout_url') ?? config('adminlte.logout_url', 'logout') )
@php( $profile_url = View::getSection('profile_url') ?? config('adminlte.profile_url', 'logout') )

@if (config('adminlte.usermenu_profile_url', false))
    @php( $profile_url = Auth::user()->adminlte_profile_url() )
@endif

@if (config('adminlte.use_route_url', false))
    @php( $profile_url = $profile_url ? route($profile_url) : '' )
    @php( $logout_url = $logout_url ? route($logout_url) : '' )
@else
    @php( $profile_url = $profile_url ? url($profile_url) : '' )
    @php( $logout_url = $logout_url ? url($logout_url) : '' )
@endif

<li class="nav-item dropdown user-menu">
    {{-- User menu toggler --}}
    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
        {{-- Ícono de usuario y nombre --}}
        <i class="fas fa-user-circle"></i>
        <span>{{ Auth::user()->Usuario ?? 'Usuario' }}</span>
    </a>

    {{-- User menu dropdown --}}
    <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <li class="user-footer">
            {{-- Botón de perfil --}}
            <a href="{{ $profile_url }}" class="btn btn-default btn-flat">
                <i class="fas fa-user-cog"></i> Perfil
            </a>        
            {{-- Botón de cerrar sesión --}}
            <a class="btn btn-default btn-flat float-right" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i> Cerrar sesión
            </a>
            
            {{-- Formulario de cierre de sesión oculto --}}
            <form id="logout-form" action="{{ $logout_url }}" method="POST" style="display: none;">
                {{ csrf_field() }}
            </form>
        </li>
        
    </ul>
</li>


