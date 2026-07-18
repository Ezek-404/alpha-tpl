<x-app-layout>
    <div class="py-6 bg-[#0d1117] min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold text-[#f0f6fc] mb-4">Transaction Logs</h1>
            
            <!-- Gamitin ang x- prefix para sa dash-separated file name -->
            <x-logs-table :logs="$logs" />
        </div>
    </div>
</x-app-layout>