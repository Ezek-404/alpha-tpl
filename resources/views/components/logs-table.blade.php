<div id="logs-container" x-data="{ 
    search: '{{ request('search') }}',
    from: '{{ request('from', \Carbon\Carbon::today()->format('Y-m-d')) }}',
    to: '{{ request('to', \Carbon\Carbon::today()->format('Y-m-d')) }}',
    showFilter: false,
    async performSearch() {
        const response = await fetch(`/transaction-logs?search=${this.search}&from=${this.from}&to=${this.to}`);
        const html = await response.text();
        
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        
        document.getElementById('logs-table-content').innerHTML = doc.getElementById('logs-table-content').innerHTML;
        document.getElementById('logs-pagination').innerHTML = doc.getElementById('logs-pagination').innerHTML;
    }
}" class="bg-[#161b22] border border-[#30363d] rounded-xl shadow-lg relative">

    <!-- Search & Filter Header Bar -->
    <div class="p-4 border-b border-[#30363d] flex justify-end items-center relative">
        <div class="flex items-center bg-[#0d1117] border border-[#30363d] rounded-lg overflow-hidden">
            
            <!-- Search Input -->
            <input type="text" 
                   x-model="search" 
                   @input.debounce.500ms="performSearch()"
                   placeholder="Search..." 
                   class="bg-transparent text-[#f0f6fc] placeholder-gray-500 px-3 py-1.5 text-xs focus:outline-none w-48 sm:w-64">

            <!-- Search Icon Button -->
            <button @click="performSearch()" class="px-3 py-1.5 text-gray-400 hover:text-white hover:bg-[#21262d] border-l border-[#30363d] transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </button>

            <!-- Filter Dropdown Toggle Button -->
            <button @click="showFilter = !showFilter" 
                    class="px-3 py-1.5 text-xs text-gray-300 hover:text-white hover:bg-[#21262d] border-l border-[#30363d] flex items-center gap-1.5 transition">
                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
                <span>Filter</span>
            </button>
        </div>

        <!-- Filter Dropdown Box -->
        <div x-show="showFilter" 
             @click.away="showFilter = false"
             x-transition
             class="absolute right-4 top-16 z-50 w-80 bg-[#161b22] border border-[#30363d] rounded-xl shadow-2xl p-4 text-xs">
            
            <div class="flex justify-between items-center mb-3 pb-2 border-b border-[#30363d]">
                <span class="font-semibold text-gray-200">Transactions Filter</span>
                <button @click="from = '{{ \Carbon\Carbon::today()->format('Y-m-d') }}'; to = '{{ \Carbon\Carbon::today()->format('Y-m-d') }}'; search = ''; performSearch(); showFilter = false;" 
                        class="text-gray-400 hover:text-[#58a6ff] transition">
                    Reset Filter
                </button>
            </div>

            <!-- Date Range Inputs -->
            <div class="space-y-3">
                <div>
                    <label class="block text-[10px] text-gray-400 mb-1">From Date</label>
                    <input type="date" 
                           x-model="from" 
                           class="w-full bg-[#0d1117] text-[#f0f6fc] border border-[#30363d] rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:border-[#58a6ff]">
                </div>

                <div>
                    <label class="block text-[10px] text-gray-400 mb-1">To Date</label>
                    <input type="date" 
                           x-model="to" 
                           class="w-full bg-[#0d1117] text-[#f0f6fc] border border-[#30363d] rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:border-[#58a6ff]">
                </div>

                <!-- Apply Filter Button -->
                <button @click="performSearch(); showFilter = false" 
                        class="w-full mt-2 bg-[#1f6feb] hover:bg-[#388bfd] text-white font-medium py-2 rounded-lg transition text-xs shadow">
                    Filter
                </button>
            </div>
        </div>
    </div>

    <!-- Table Container -->
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
                        <a href="{{ route('ctpl.show', $log->id) }}" class="text-[#58a6ff] hover:underline mr-2">View</a>
                        <a href="#" class="text-[#2ea043] hover:underline">Edit</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-6 text-center text-xs text-gray-400">No records found within this date range.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination & Summary Footer (Sinigurong laging kita kahit kakaunti ang record) -->
    <div id="logs-pagination" class="px-6 py-3 border-t border-[#30363d] bg-[#0d1117] flex flex-col sm:flex-row justify-between items-center text-xs text-gray-400 custom-pagination-container rounded-b-xl">
        <div class="mb-2 sm:mb-0">
            Showing 
            <span class="font-semibold text-gray-200">{{ $logs->firstItem() ?? 0 }}</span> 
            to 
            <span class="font-semibold text-gray-200">{{ $logs->lastItem() ?? 0 }}</span> 
            of 
            <span class="font-semibold text-gray-200">{{ $logs->total() }}</span> 
            results
        </div>
        <div>
            {{ $logs->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<style>
    /* Ibinabalik ang summary text sa kaliwa at ginagawang maayos ang pagination links sa kanang bahagi */
    .custom-pagination-container nav > div:first-child {
        display: none !important; /* Itinatago ang default na "Showing 1 to X..." ng Laravel para mapalitan ng custom natin sa kaliwa kung kinakailangan, o panatilihin kung gusto mo */
    }

    .custom-pagination-container nav {
        display: flex !important;
        justify-content: flex-end !important;
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
</style>