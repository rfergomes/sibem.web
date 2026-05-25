@php
    $user = Auth::user();
    $activeLocalId = session()->get('active_admlc_id') ?? $user->local_id;
    $activeLocal = \App\Models\Local::find($activeLocalId);
    $availableLocais = $user->getAvailableLocais();
@endphp

@if($user->isAdminSistema() || $user->isAdminRegional())
    <form action="{{ route('tenant.select') }}" method="POST" class="d-flex align-items-center" id="tenant-selector-form">
        @csrf
        <div class="input-group input-group-sm">
            <span class="input-group-text bg-light border-0"><i class="ti ti-building-store text-primary"></i></span>
            <select name="local_id" class="form-select border-0 bg-light fw-600 text-dark" style="max-width: 250px;" onchange="document.getElementById('tenant-selector-form').submit();">
                @foreach($availableLocais as $localOption)
                    <option value="{{ $localOption->id }}" {{ $localOption->id == $activeLocalId ? 'selected' : '' }}>
                        {{ $localOption->nome }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>
@else
    @if($activeLocal)
        <span class="badge bg-light-primary text-primary fw-600 px-3 py-2">
            <i class="ti ti-building-store me-1"></i> {{ $activeLocal->nome }}
        </span>
    @endif
@endif
