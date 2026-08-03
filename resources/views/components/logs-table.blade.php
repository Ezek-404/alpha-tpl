<div x-data="logsData()" class="bg-[#161b22] border border-[#30363d] rounded-xl shadow-lg relative">

    <!-- Search, Limiter & Filter Header Bar -->
    <div class="p-3 border-b border-[#30363d] flex flex-col sm:flex-row justify-between items-center gap-3 relative">
        
        <!-- Left Side: Batch Buttons & Limiter -->
        <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
            
            <!-- Batch ISAP Button -->
            <button type="button" 
                    @click="batchAction('Batch ISAP')"
                    :disabled="selectedLogs.length === 0"
                    :class="selectedLogs.length === 0 ? 'opacity-40 cursor-not-allowed bg-gray-800' : 'bg-[#1f6feb] hover:bg-[#388bfd] shadow'"
                    class="text-white font-medium px-3 py-1.5 rounded-lg transition text-xs flex items-center gap-1.5">
                <span>Batch ISAP</span>
                <span x-show="selectedLogs.length > 0" class="bg-black/30 px-1.5 py-0.5 rounded-full text-[10px]" x-text="selectedLogs.length"></span>
            </button>

            <!-- Batch OICP Button -->
            <button type="button" 
                    @click="batchAction('Batch OICP')"
                    :disabled="selectedLogs.length === 0"
                    :class="selectedLogs.length === 0 ? 'opacity-40 cursor-not-allowed bg-gray-800' : 'bg-[#8957e5] hover:bg-[#a371f7] shadow'"
                    class="text-white font-medium px-3 py-1.5 rounded-lg transition text-xs flex items-center gap-1.5">
                <span>Batch OICP</span>
                <span x-show="selectedLogs.length > 0" class="bg-black/30 px-1.5 py-0.5 rounded-full text-[10px]" x-text="selectedLogs.length"></span>
            </button>

            <!-- Limiter -->
            <div class="flex items-center gap-1.5 text-xs text-gray-400 ml-1">
                <span>Show:</span>
                <select x-model="perPage" @change="performSearch()" class="bg-[#0d1117] text-gray-200 border border-[#30363d] rounded-lg px-2 py-1 text-xs focus:outline-none focus:border-[#58a6ff] cursor-pointer">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span>entries</span>
            </div>
        </div>

        <!-- Right Side: Search & Filter Group -->
        <div class="flex justify-end items-center relative w-full sm:w-auto">
            <div class="flex items-center bg-[#0d1117] border border-[#30363d] rounded-lg overflow-hidden w-full sm:w-auto">
                <input type="text" 
                       x-model="search" 
                       @input.debounce.500ms="performSearch()"
                       placeholder="Search..." 
                       class="bg-transparent text-[#f0f6fc] placeholder-gray-500 px-3 py-1.5 text-xs focus:outline-none w-full sm:w-64">

                <button @click="performSearch()" class="px-3 py-1.5 text-gray-400 hover:text-white hover:bg-[#21262d] border-l border-[#30363d] transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>

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
                 x-cloak
                 @click.away="showFilter = false"
                 x-transition
                 class="absolute right-0 top-12 z-50 w-80 bg-[#161b22] border border-[#30363d] rounded-xl shadow-2xl p-4 text-xs">
                <div class="flex justify-between items-center mb-3 pb-2 border-b border-[#30363d]">
                    <span class="font-semibold text-gray-200">Transactions Filter</span>
                    <button @click="from = '{{ \Carbon\Carbon::today()->format('Y-m-d') }}'; to = '{{ \Carbon\Carbon::today()->format('Y-m-d') }}'; search = ''; performSearch(); showFilter = false;" 
                            class="text-gray-400 hover:text-[#58a6ff] transition">
                        Reset Filter
                    </button>
                </div>
                <div class="space-y-3">
                    <div>
                        <label class="block text-[10px] text-gray-400 mb-1">From Date</label>
                        <input type="date" x-model="from" class="w-full bg-[#0d1117] text-[#f0f6fc] border border-[#30363d] rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:border-[#58a6ff]">
                    </div>
                    <div>
                        <label class="block text-[10px] text-gray-400 mb-1">To Date</label>
                        <input type="date" x-model="to" class="w-full bg-[#0d1117] text-[#f0f6fc] border border-[#30363d] rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:border-[#58a6ff]">
                    </div>
                    <button @click="performSearch(); showFilter = false" 
                            class="w-full mt-2 bg-[#1f6feb] hover:bg-[#388bfd] text-white font-medium py-2 rounded-lg transition text-xs shadow">
                        Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Container -->
    <div id="logs-table-content" class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[700px]">
          <thead>
                <tr class="border-b border-[#30363d] bg-[#161b22]">
                    <th class="px-3 py-1.5 w-10 text-center">
                        <input type="checkbox" @click="toggleSelectAll()" class="rounded bg-[#0d1117] border-[#30363d] text-[#58a6ff] focus:ring-0 cursor-pointer">
                    </th>
                    <th class="px-3 py-1.5 text-[10px] font-semibold uppercase tracking-wider text-gray-400">Assured Name</th>
                    <th class="px-3 py-1.5 text-[10px] font-semibold uppercase tracking-wider text-gray-400 w-28">COC Number</th>
                    <th class="px-3 py-1.5 text-[10px] font-semibold uppercase tracking-wider text-gray-400 w-32">Plate Number</th>
                    <th class="px-3 py-1.5 text-[10px] font-semibold uppercase tracking-wider text-gray-400 w-40">Agent</th>
                    <th class="px-3 py-1.5 text-[10px] font-semibold uppercase tracking-wider text-gray-400 w-36">Date Issued</th>
                    <th class="px-3 py-1.5 text-[10px] font-semibold uppercase tracking-wider text-gray-400 w-20">Option</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#30363d]">
                @forelse($logs as $log)
                <tr class="hover:bg-[#21262d]/50 transition duration-150" 
                    data-coc="{{ $log->coc_no ?? '' }}"
                    data-plate="{{ $log->plate_no ?? 'None' }}"
                    data-assured="{{ $log->assured ?? 'None' }}"
                    data-mvtype="{{ $log->denomination ?? 'HB' }}"
                    data-mvfile="{{ $log->mv_file ?? 'None' }}" 
                    data-motor="{{ $log->engine_no ?? 'None' }}" 
                    data-chassis="{{ $log->chassis_no ?? 'None' }}"
                    data-created="{{ $log->created_at }}">
                    
                    <td class="px-3 py-1 text-center">
                        <input type="checkbox" 
                               value="{{ $log->id }}" 
                               x-model="selectedLogs" 
                               class="log-checkbox rounded bg-[#0d1117] border-[#30363d] text-[#58a6ff] focus:ring-0 cursor-pointer">
                    </td>

                    <td class="px-3 py-1 text-[11px] text-gray-300 font-medium">
                        <div class="max-w-[240px] sm:max-w-[300px] truncate" title="{{ $log->assured }}">
                            {{ $log->assured }}
                        </div>
                    </td>

                    <td class="px-3 py-1 text-[11px] text-gray-300 font-mono">{{ $log->coc_no }}</td>
                    <td class="px-3 py-1 text-[11px] text-[#58a6ff] font-semibold">{{ $log->plate_no }}</td>
                    
                    <td class="px-3 py-1 text-[11px] text-gray-400">
                        <span class="inline-block max-w-[140px] truncate align-middle px-1.5 py-0.2 border border-[#30363d] rounded text-[9px] font-mono bg-[#0d1117]" title="{{ $log->agent }}">
                            {{ $log->agent }}
                        </span>
                    </td>

                    <td class="px-3 py-1 text-[11px] text-gray-400 font-mono">{{ \Carbon\Carbon::parse($log->created_at)->format('Y-m-d H:i') }}</td>
                    <td class="px-3 py-1 text-[11px]" x-data="{ openEditModal: false }">
                        <a href="{{ route('ctpl.show', $log->transaction_id) }}" class="text-[#58a6ff] hover:underline mr-1.5">View</a>
                        
                        <!-- Trigger Button -->
                        <button @click="openEditModal = true" type="button" class="text-[#2ea043] hover:underline bg-transparent border-0 p-0 cursor-pointer">
                            Edit
                        </button>

                        <!-- Modal Overlay -->
                        <div x-show="openEditModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4 overflow-y-auto">
                            
                            <!-- Modal Box (Laki na kasya ang 3 columns) -->
                            <div @click.away="openEditModal = false" class="bg-[#161b22] border border-[#30363d] rounded-xl shadow-2xl p-6 w-[98vw] max-w-[1500px] text-gray-200 text-left relative my-8">
                                
                                <!-- Header -->
                                <div class="flex justify-between items-center mb-4 pb-3 border-b border-[#30363d]">
                                    <h3 class="text-sm font-semibold text-gray-100">Edit Transaction Log (ID: {{ $log->transaction_id }})</h3>
                                    <button @click="openEditModal = false" class="text-gray-400 hover:text-gray-200 text-base font-bold">✕</button>
                                </div>

                                <!-- Form na may 3 Columns -->
                                <form action="{{ route('transaction-logs.update', $log->transaction_id) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 text-xs">
                                        
                                        <!-- 1. Assured Details -->
                                        <div class="bg-[#0d1117] border border-[#30363d] rounded-xl p-4 space-y-3">
                                            <h4 class="font-semibold text-[#58a6ff] pb-2 border-b border-[#30363d]">1. Assured Details</h4>
                                            
                                            <div>
                                                <x-input-label value="Assured Name" class="text-[11px] text-gray-400 mb-1" />
                                                <x-text-input name="assured" type="text" value="{{ $log->assured }}" class="w-full bg-[#161b22] text-gray-200 border-[#30363d] text-xs uppercase" required />
                                            </div>

                                            <div>
                                                <x-input-label value="Address" class="text-[11px] text-gray-400 mb-1" />
                                                <x-text-input name="address" type="text" value="{{ $log->address }}" class="w-full bg-[#161b22] text-gray-200 border-[#30363d] text-xs uppercase" required />
                                            </div>
                                        </div>

                                        <!-- 2. Vehicle Specification -->
                                        <div class="bg-[#0d1117] border border-[#30363d] rounded-xl p-4 space-y-3">
                                            <h4 class="font-semibold text-[#58a6ff] pb-2 border-b border-[#30363d]">2. Vehicle Specification</h4>
                                            
                                            <div class="grid grid-cols-2 gap-2">
                                                <div>
                                                    <x-input-label value="Year Model" class="text-[11px] text-gray-400 mb-1" />
                                                    <x-text-input name="year_model" type="text" value="{{ $log->year_model ?? '' }}" class="w-full bg-[#161b22] text-gray-200 border-[#30363d] text-xs" />
                                                </div>
                                                <div>
                                                    <x-input-label value="Make" class="text-[11px] text-gray-400 mb-1" />
                                                    <x-text-input name="make" type="text" value="{{ $log->make ?? '' }}" class="w-full bg-[#161b22] text-gray-200 border-[#30363d] text-xs uppercase" />
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-2 gap-2">
                                                <div>
                                                    <x-input-label value="Series" class="text-[11px] text-gray-400 mb-1" />
                                                    <x-text-input name="series" type="text" value="{{ $log->series ?? '' }}" class="w-full bg-[#161b22] text-gray-200 border-[#30363d] text-xs uppercase" />
                                                </div>
                                                <div>
                                                    <x-input-label value="Color" class="text-[11px] text-gray-400 mb-1" />
                                                    <x-text-input name="color" type="text" value="{{ $log->color ?? '' }}" class="w-full bg-[#161b22] text-gray-200 border-[#30363d] text-xs uppercase" />
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-2 gap-2">
                                                <div>
                                                    <x-input-label value="MV File No." class="text-[11px] text-gray-400 mb-1" />
                                                    <x-text-input name="file_no" type="text" value="{{ $log->mv_file ?? '' }}" class="w-full bg-[#161b22] text-gray-200 border-[#30363d] text-xs uppercase" />
                                                </div>
                                                <div>
                                                    <x-input-label value="Plate Number" class="text-[11px] text-gray-400 mb-1" />
                                                    <x-text-input name="plate_no" type="text" value="{{ $log->plate_no ?? '' }}" class="w-full bg-[#161b22] text-gray-200 border-[#30363d] text-xs uppercase" />
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-2 gap-2">
                                                <div>
                                                    <x-input-label value="Chassis Number" class="text-[11px] text-gray-400 mb-1" />
                                                    <x-text-input name="chassis_no" type="text" value="{{ $log->chassis_no ?? '' }}" class="w-full bg-[#161b22] text-gray-200 border-[#30363d] text-xs uppercase" />
                                                </div>
                                                <div>
                                                    <x-input-label value="Engine Number" class="text-[11px] text-gray-400 mb-1" />
                                                    <x-text-input name="engine_no" type="text" value="{{ $log->engine_no ?? '' }}" class="w-full bg-[#161b22] text-gray-200 border-[#30363d] text-xs uppercase" />
                                                </div>
                                            </div>

                                            <div>
                                                <x-input-label value="Denomination" class="text-[11px] text-gray-400 mb-1" />
                                                <x-text-input name="denomination" type="text" value="{{ $log->denomination ?? '' }}" class="w-full bg-[#161b22] text-gray-200 border-[#30363d] text-xs uppercase" />
                                            </div>
                                        </div>

                                        <!-- 3. Allocation and Payment (Locked Policy & COC) -->
                                        <div class="bg-[#0d1117] border border-[#30363d] rounded-xl p-4 space-y-3 flex flex-col justify-between">
                                            <div class="space-y-3">
                                                <h4 class="font-semibold text-[#3fb950] pb-2 border-b border-[#30363d]">3. Allocation and Payment</h4>
                                                
                                                <!-- Locked COC -->
                                                <div>
                                                    <span class="block text-[10px] text-gray-400 mb-1 uppercase">COC Number (Locked)</span>
                                                    <div class="w-full bg-[#161b22] border border-[#30363d] rounded-md px-3 py-2 text-xs text-gray-300 font-mono">
                                                        {{ $log->coc_no ?? 'N/A' }}
                                                    </div>
                                                </div>

                                                <!-- Locked Policy -->
                                                <div>
                                                    <span class="block text-[10px] text-gray-400 mb-1 uppercase">Policy Number (Locked)</span>
                                                    <div class="w-full bg-[#161b22] border border-[#30363d] rounded-md px-3 py-2 text-xs text-gray-300 font-mono">
                                                        {{ $log->policy_no }}
                                                    </div>
                                                </div>

                                                <div>
                                                    <x-input-label value="Agent Name" class="text-[11px] text-gray-400 mb-1" />
                                                    <x-text-input name="agent" type="text" value="{{ $log->agent ?? '' }}" class="w-full bg-[#161b22] text-gray-200 border-[#30363d] text-xs uppercase" />
                                                </div>

                                                <div>
                                                    <x-input-label value="Amount Paid (Premium)" class="text-[11px] text-gray-400 mb-1" />
                                                    <x-text-input name="amount" type="text" value="{{ $log->amount ?? '' }}" class="w-full bg-[#161b22] text-gray-200 border-[#30363d] text-xs" />
                                                </div>
                                            </div>

                                            <!-- Action Buttons sa loob ng Column 3 -->
                                            <div class="pt-4 border-t border-[#30363d] flex items-center justify-between">
                                                
                                                <!-- Cancel Policy Button (Kulay Pula / Danger) -->
                                                <form action="{{ route('transaction-logs.cancel', $log->transaction_id) }}" method="POST" onsubmit="return confirm('Sigurado ka bang gusto mong i-cancel ang policy na ito?');">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="px-3 py-2 bg-[#da3633] hover:bg-[#b62324] text-white rounded-md font-medium text-xs shadow transition-colors">
                                                        Cancel Policy
                                                    </button>
                                                </form>

                                                <!-- Save & Cancel Modal Buttons -->
                                                <div class="flex gap-2">
                                                    <button @click="openEditModal = false" type="button" class="px-3 py-2 bg-[#21262d] hover:bg-[#30363d] text-gray-300 rounded-md font-medium text-xs border border-[#30363d]">
                                                        Close
                                                    </button>
                                                    <button type="submit" class="px-4 py-2 bg-[#2ea043] hover:bg-[#238636] text-white rounded-md font-medium text-xs shadow">
                                                        Save Changes
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </form>

                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-3 py-3 text-center text-xs text-gray-400">No records found within this date range.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination & Summary Footer -->
    <div id="logs-pagination" class="px-4 py-2.5 border-t border-[#30363d] bg-[#0d1117] flex flex-col sm:flex-row justify-between items-center text-xs text-gray-400 custom-pagination-container rounded-b-xl gap-3">
        <div>
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
    [x-cloak] { display: none !important; }

    .custom-pagination-container nav > div:first-child {
        display: none !important;
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

<script>
function logsData() {
    return {
        search: '{{ request('search') }}',
        from: '{{ request('from', \Carbon\Carbon::today()->format('Y-m-d')) }}',
        to: '{{ request('to', \Carbon\Carbon::today()->format('Y-m-d')) }}',
        perPage: '{{ request('per_page', 10) }}',
        showFilter: false,
        selectedLogs: [],
        
        toggleSelectAll() {
            const checkboxes = document.querySelectorAll('.log-checkbox');
            if (this.selectedLogs.length === checkboxes.length) {
                this.selectedLogs = [];
            } else {
                this.selectedLogs = Array.from(checkboxes).map(cb => cb.value);
            }
        },

        async performSearch() {
            const response = await fetch(`/transaction-logs?search=${this.search}&from=${this.from}&to=${this.to}&per_page=${this.perPage}`);
            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            document.getElementById('logs-table-content').innerHTML = doc.getElementById('logs-table-content').innerHTML;
            document.getElementById('logs-pagination').innerHTML = doc.getElementById('logs-pagination').innerHTML;
            this.selectedLogs = [];
        },

        batchAction(type) {
            if (this.selectedLogs.length === 0) {
                alert('Please select at least one transaction.');
                return;
            }
            
            if (type === 'Batch ISAP') {
                const rows = document.querySelectorAll('tbody tr');
                let csvContent = 'ISAP\n';
                csvContent += 'COC_NO,PLATE_NO,MVFILE_NO,MOTOR_NO,CHASSIS_NO,INCE_DATE,EXPI_DATE,PREM_TYPE,REG_TYPE,TAX_TYPE,ASSURED_NAME,ASSURED_TIN,MV_TYPE\n';

                rows.forEach(row => {
                    const checkbox = row.querySelector('.log-checkbox');
                    if (checkbox && this.selectedLogs.includes(checkbox.value)) {
                        const rawCoc = row.getAttribute('data-coc') || '';
                        const cocNo = '010' + rawCoc;
                        const plateNo = row.getAttribute('data-plate') || 'None';
                        const mvFileNo = row.getAttribute('data-mvfile') || 'None';
                        const motorNo = row.getAttribute('data-motor') || 'None';
                        const chassisNo = row.getAttribute('data-chassis') || 'None';

                        let rawAssuredName = row.getAttribute('data-assured') || 'None';
                        const assuredName = rawAssuredName.replace(/[^a-zA-Z0-9\s]/g, '').trim();

                        const createdAtStr = row.getAttribute('data-created') || '';
                        const createdDate = new Date(createdAtStr);
                        const validDate = isNaN(createdDate.getTime()) ? new Date() : createdDate;

                        const inceDate = (validDate.getMonth() + 1) + '/' + validDate.getDate() + '/' + validDate.getFullYear();

                        const expDateObj = new Date(validDate);
                        expDateObj.setFullYear(expDateObj.getFullYear() + 1);
                        const expiDate = (expDateObj.getMonth() + 1) + '/' + expDateObj.getDate() + '/' + expDateObj.getFullYear();

                        const regType = 'R';
                        const taxType = '0';
                        const assuredTin = '111-111-111-11111';

                        const rawMvType = (row.getAttribute('data-mvtype') || '').trim().toUpperCase();
                        let mvType = 'HB';
                        switch (rawMvType) {
                            case 'MC': mvType = 'M'; break;
                            case 'MTC': mvType = 'MS'; break;
                            case 'CAR':
                            case 'PASSENGER CAR':
                            case 'COUPE':
                            case 'HATCHBACK':
                            case 'SEDAN': mvType = 'C'; break;
                            case 'SUV': mvType = 'SV'; break;
                            case 'UTILITY VEHICLE': mvType = 'UV'; break;
                            case 'TRICYCLE': mvType = 'TC'; break;
                            case 'TRUCK': mvType = 'TK'; break;
                            case 'TRAILER': mvType = 'TL'; break;
                            default: mvType = rawMvType || 'HB';
                        }

                        let premType = '10';
                        switch (mvType) {
                            case 'C':
                            case 'SV':
                            case 'UV':
                                premType = '1';
                                break;
                            case 'M':
                            case 'MS':
                            case 'TC':
                            case 'TL':
                                premType = '7';
                                break;
                            case 'TK':
                                premType = '3';
                                break;
                        }

                        const rowData = [
                            cocNo,
                            plateNo,
                            mvFileNo,
                            motorNo,
                            chassisNo,
                            inceDate,
                            expiDate,
                            premType,
                            regType,
                            taxType,
                            assuredName,
                            assuredTin,
                            mvType
                        ];

                        csvContent += rowData.join(',') + '\n';
                    }
                });

                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                const url = URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.setAttribute('href', url);
                link.setAttribute('download', 'SampleBatch.csv');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

            } else {
                console.log(`${type} for logs IDs:`, this.selectedLogs);
                alert(`Successfully triggered ${type} for ${this.selectedLogs.length} item(s).`);
            }
        }
    }
}
</script>