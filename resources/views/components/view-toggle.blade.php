@props(['storageKey' => 'view_mode'])

<div class="flex items-center gap-2 bg-gray-100 rounded-lg p-1">
    <button type="button" data-view="table" data-storage-key="{{ $storageKey }}"
        class="view-toggle-btn px-3 py-1.5 rounded-md text-sm font-medium transition-all flex items-center gap-1.5">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
        </svg>
        <span class="hidden sm:inline">Tabela</span>
    </button>
    <button type="button" data-view="card" data-storage-key="{{ $storageKey }}"
        class="view-toggle-btn px-3 py-1.5 rounded-md text-sm font-medium transition-all flex items-center gap-1.5">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
        </svg>
        <span class="hidden sm:inline">Cards</span>
    </button>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const storageKey = '{{ $storageKey }}';
            const savedView = localStorage.getItem(storageKey) || 'card';

            // Apply saved view
            document.querySelectorAll('[data-view-content]').forEach(el => {
                if (el.dataset.viewContent === savedView) {
                    el.classList.remove('hidden');
                } else {
                    el.classList.add('hidden');
                }
            });

            // Update button states
            document.querySelectorAll('.view-toggle-btn[data-storage-key="{{ $storageKey }}"]').forEach(btn => {
                if (btn.dataset.view === savedView) {
                    btn.classList.add('bg-white', 'text-blue-600', 'shadow-sm');
                    btn.classList.remove('text-gray-600', 'hover:bg-gray-200');
                } else {
                    btn.classList.add('text-gray-600', 'hover:bg-gray-200');
                    btn.classList.remove('bg-white', 'text-blue-600', 'shadow-sm');
                }

                // Click handler
                btn.addEventListener('click', function () {
                    const view = this.dataset.view;
                    localStorage.setItem(storageKey, view);

                    // Hide all views
                    document.querySelectorAll('[data-view-content]').forEach(el => {
                        el.classList.add('hidden');
                    });

                    // Show selected view
                    document.querySelector(`[data-view-content="${view}"]`)?.classList.remove('hidden');

                    // Update button states
                    document.querySelectorAll('.view-toggle-btn[data-storage-key="{{ $storageKey }}"]').forEach(b => {
                        if (b.dataset.view === view) {
                            b.classList.add('bg-white', 'text-blue-600', 'shadow-sm');
                            b.classList.remove('text-gray-600', 'hover:bg-gray-200');
                        } else {
                            b.classList.add('text-gray-600', 'hover:bg-gray-200');
                            b.classList.remove('bg-white', 'text-blue-600', 'shadow-sm');
                        }
                    });
                });
            });
        });
    </script>
@endpush
