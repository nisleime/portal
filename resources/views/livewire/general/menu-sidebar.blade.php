<div class="sidebar">

    <div class="menu-wrap scrollbar-macosx">

        <ul>
            <li class="{{ request()->is('panel/dashboard') ? 'active' : '' }}">
                <a href="{{ route('panel.dashboard.index') }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span class="label">Dashboard</span>
                </a>
            </li>
            @if ($user->is_admin == 'S')
            <li class="{{ request()->is('panel/companies') ? 'active' : '' }}">
                <a href="{{ route('panel.companies.index') }}">
                    <i class="far fa-building"></i>
                    <span class="label">Empresas</span>
                </a>
            </li>
            <li class="{{ request()->is('panel/users') ? 'active' : '' }}">
                <a href="{{ route('panel.users.index') }}">
                    <i class="fas fa-users"></i>
                    <span class="label">Usuários</span>
                </a>
            </li>
            @endif
            <li>
                <a href="#" wire:click.prevent="logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="label">Sair</span>
                </a>
            </li>
        </ul>

    </div>

</div>