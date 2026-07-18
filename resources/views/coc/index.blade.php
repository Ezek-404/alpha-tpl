<x-app-layout>
    <div class="py-6 bg-[#0d1117] min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Page Header -->
            <div class="mb-4">
                <h1 class="text-xl sm:text-2xl font-bold text-[#f0f6fc]">COC Management</h1>
            </div>

            <!-- Tinatawag ang custom Blade components para sa table grid -->
            <x-coc-table :cocs="$cocs" :search="$search" />

        </div>
    </div>

    <!-- Live Search Debounce Implementation sa Vanilla JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.querySelector('input[name="search_input"]');
            if (searchInput) {
                // Ibalik ang focus at ilagay ang cursor sa dulo ng text pagkatapos mag-refresh ng page
                searchInput.focus();
                const val = searchInput.value;
                searchInput.value = '';
                searchInput.value = val;

                let timeout = null;
                searchInput.addEventListener('input', function () {
                    clearTimeout(timeout);
                    // Maghihintay ng 400ms matapos mag-type ang user bago i-submit ang request para walang lag
                    timeout = setTimeout(() => {
                        const url = new URL(window.location.href);
                        url.searchParams.set('search', this.value);
                        url.searchParams.delete('page'); // I-reset pabalik sa Page 1 tuwing may bagong hinahanap
                        window.location.href = url.toString();
                    }, 400);
                });
            }
        });
    </script>
</x-app-layout>