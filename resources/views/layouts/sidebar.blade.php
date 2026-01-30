@php
    $role = auth()->user()->role; 
@endphp

@if ($role === 'admin')
    @include('layouts.Partials.admin')

@elseif ($role === 'coach')
    @include('layouts.Partials.coach')

@elseif ($role === 'investisseur')
    @include('layouts.Partials.investisseur')

@elseif ($role === 'startup')
    @include('layouts.Partials.startup')

@else
    <div class="text-center py-10">
        <p class="text-red-600 font-semibold">Rôle non reconnu. Veuillez contacter l’administrateur.</p>
    </div>
@endif
