<div id="logs-container" x-data="{ 
    search: '{{ request('search') }}',
    from: '{{ request('from', \Carbon\Carbon::today()->format('Y-m-d')) }}',
    to: '{{ request('to', \Carbon\Carbon::today()->format('Y-m-d')) }}',
    async performSearch() {
        const response = await fetch(`/transaction-logs?search=${this.search}&from=${this.from}&to=${this.to}`);
        const html = await response.text();
        
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        
        document.getElementById('logs-table-content').innerHTML = doc.getElementById('logs-table-content').innerHTML;
        document.getElementById('logs-pagination').innerHTML = doc.getElementById('logs-pagination').innerHTML;
    }
}" class="bg-[#161b22] border border-[#30363d] rounded-xl shadow-lg overflow-hidden">

    <!-- Search & Date Range Filter Bar -->
    <div class="p-4 border-b border-[#30363d] flex flex-col lg:flex-row gap-3 justify-between items-center">
        <div class="flex flex-col sm:flex-row gap-2 w-full lg:w-auto items-center">
            <!-- Search Bar -->
            <input type="text" 
                   x-model="search" 
                   @input.debounce.500ms="performSearch()"
                   placeholder="Search name, COC, plate no..." 
                   class="w-full sm:w-64 bg-[#0d1117] text-[#f0f6fc] placeholder-gray-500 border border-[#30363d] rounded-lg px-4 py-1.5 text-xs focus:outline-none focus:border-[#58a6ff] focus:ring-1 focus:ring-[#58a6ff]">

            <!-- From Date -->
            <div class="flex items-center gap-1.5 w-full sm:w-auto">
                <span class="text-[11px] text-gray-400">From:</span>
                <input type="date" 
                       x-model="from" 
                       @change="performSearch()"
                       class="w-full sm:w-auto bg-[#0d1117] text-[#f0f6fc] border border-[#30363d] rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:border-[#58a6ff] focus:ring-1 focus:ring-[#58a6ff]">
            </div>

            <!-- To Date -->
            <div class="flex items-center gap-1.5 w-full sm:w-auto">
                <span class="text-[11px] text-gray-400">To:</span>
                <input type="date" 
                       x-model="to" 
                       @change="performSearch()"
                       class="w-full sm:w-auto bg-[#0d1117] text-[#f0f6fc] border border-[#30363d] rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:border-[#58a6ff] focus:ring-1 focus:ring-[#58a6ff]">
            </div>
        </div>

        <button @click="search = ''; from = '{{ \Carbon\Carbon::today()->format('Y-m-d') }}'; to = '{{ \Carbon\Carbon::today()->format('Y-m-d') }}'; performSearch()" 
                class="text-xs text-gray-400 hover:text-[#58a6ff] underline transition">
            Reset Filter
        </button>
    </div>

    <!-- Table -->
    <div id="logs-table-content" class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[700px]">
            <thead>
                <tr class="border-b border-[#30363d] bg-[#161b22]">
                    <th class="px-6 py-2 text-[11px] font-semibold uppercase tracking-wider text-gray-400">Assured Name</th>
                    <th class="px-6 py-2 text-[11px] font-semibold uppercase tracking-wider text-gray-400">COC Number</th>
                    <th class="px-6 py-2 text-[11px] font-semibold uppercase tracking-wider text-gray-400">Plate Number</th>
                    <th class="px-6 py-2 text-[11px] font-semibold uppercase tracking-wider text-gray-400">Agent</th>
                    <th class="px-6 py-2 text-[11px] font-semibold uppercase tracking-wider text-gray-400">Date Issued</th>
                    <th class="px-6 py-2 text-[11px] font-semibold uppercase tracking-wider text-gray-400">Option</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#30363d]">
                @forelse($logs as $log)
                <tr class="hover:bg-[#21262d]/50 transition duration-150">
                    <td class="px-6 py-2 text-xs text-gray-300 font-medium">{{ $log->assured }}</td>
                    <td class="px-6 py-2 text-xs text-gray-300 font-mono">{{ $log->coc_no }}</td>
                    <td class="px-6 py-2 text-xs text-[#58a6ff] font-semibold">{{ $log->plate_no }}</td>
                    <td class="px-6 py-2 text-xs text-gray-400">
                        <span class="px-2 py-0.5 border border-[#30363d] rounded text-[10px] font-mono bg-[#0d1117]">
                            {{ $log->agent }}
                        </span>
                    </td>
                    <td class="px-6 py-2 text-xs text-gray-400 font-mono">{{ \Carbon\Carbon::parse($log->created_at)->format('Y-m-d H:i') }}</td>
                    <td class="px-6 py-2 text-xs">
                        <a href="#" class="text-[#58a6ff] hover:underline mr-2">View</a>
                        <a href="#" class="text-[#2ea043] hover:underline">Edit</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-6 text-center text-xs text-gray-400">No records found within this date range.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div id="logs-pagination" class="px-6 py-3 border-t border-[#30363d] bg-[#0d1117] custom-pagination-container">
        {{ $logs->links() }}
    </div>
</div>

<style>
    /* Naka-imbak na custom layout parameters */
    .custom-pagination-container nav > div:first-child {
        display: none !important;
    }

    .custom-pagination-container nav {
        display: flex !important;
        justify-content: center !important;
        width: 100% !important;
    }

    .custom-pagination-container nav > div:last-child {
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
    }

    .custom-pagination-container nav button,
    .custom-pagination-container nav a,
    .custom-pagination-container nav span {
        background-color: transparent !important;
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        font-size: 11px !important;       
        padding: 2px 6px !important;      
        color: #8b949e !important;        
        border-radius: 0px !important;
    }

    .custom-pagination-container nav span[aria-current="page"] span,
    .custom-pagination-container nav span[aria-current="page"] {
        color: #58a6ff !important;        
        font-weight: 700 !important;
        background: transparent !important;
    }

    .custom-pagination-container nav a:hover,
    .custom-pagination-container nav button:hover {
        color: #58a6ff !important;
        background: transparent !important;
    }

    .custom-pagination-container nav svg {
        width: 14px !important;
        height: 14px !important;
    }

    html, body {
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    html::-webkit-scrollbar, 
    body::-webkit-scrollbar {
        display: none;
    }
</style>