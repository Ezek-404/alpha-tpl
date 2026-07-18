@props(['cocs', 'search'])

<!-- Binalot natin sa Alpine.js state x-data para sa open/close ng modal -->
<div x-data="{ openAddModal: false, openDeleteModal: false }">

    @if (session('success'))
        <div id="flash-success-alert" class="mb-4 p-3 bg-[#238636]/10 border border-[#238636]/40 text-[#3fb950] rounded-lg text-xs flex justify-between items-center transition duration-150">
            <div class="flex items-center gap-2">
                <!-- Green Check Icon -->
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <!-- Close Button para sa Banner -->
            <button onclick="document.getElementById('flash-success-alert').remove()" class="text-gray-400 hover:text-white text-sm font-bold ml-2">&times;</button>
        </div>
    @endif

    @if ($errors->has('delete_error'))
        <div id="flash-error-alert" class="mb-4 p-3 bg-[#da3633]/10 border border-[#da3633]/40 text-[#f85149] rounded-lg text-xs flex justify-between items-center transition duration-150">
            <div class="flex items-center gap-2">
                <!-- Warning Icon -->
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>{{ $errors->first('delete_error') }}</span>
            </div>
            <button onclick="document.getElementById('flash-error-alert').remove()" class="text-gray-400 hover:text-white text-sm font-bold ml-2">&times;</button>
        </div>
    @endif
    
    <!-- Top Actions: Search Input Area at Buttons -->
    <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-4 mb-4">
        <div class="w-full sm:w-80">
            <div class="relative">
                <input 
                    type="text" 
                    name="search_input"
                    value="{{ $search }}"
                    placeholder="Search COC number, type, or status..." 
                    class="w-full bg-[#0d1117] text-[#f0f6fc] placeholder-gray-500 border border-[#30363d] rounded-lg px-4 py-1.5 text-xs focus:outline-none focus:border-[#58a6ff] focus:ring-1 focus:ring-[#58a6ff]"
                >
            </div>
        </div>
        
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <!-- Pagpindot dito, magiging TRUE ang state para bumukas ang Modal -->
            <button @click="openAddModal = true" class="w-full sm:w-auto bg-[#238636] hover:bg-[#2ea043] text-white px-4 py-1.5 rounded-lg text-xs font-semibold shadow transition duration-150">
                Add COC Series
            </button>
            <button @click="openDeleteModal = true" class="bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg text-xs px-4 py-2 transition">
                Delete Series
            </button>
        </div>
    </div>

    <!-- Responsive Table Wrapper -->
    <div class="bg-[#161b22] border border-[#30363d] rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[700px]">
                <thead>
                    <tr class="border-b border-[#30363d] bg-[#161b22]">
                        <th class="px-6 py-1.5 text-[11px] font-semibold uppercase tracking-wider text-gray-400">ID</th>
                        <th class="px-6 py-1.5 text-[11px] font-semibold uppercase tracking-wider text-gray-400">COC Number</th>
                        <th class="px-6 py-1.5 text-[11px] font-semibold uppercase tracking-wider text-gray-400">Vehicle Type</th>
                        <th class="px-6 py-1.5 text-[11px] font-semibold uppercase tracking-wider text-gray-400">Status</th>
                        <th class="px-6 py-1.5 text-[11px] font-semibold uppercase tracking-wider text-gray-400">Created At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#30363d]">
                    @forelse($cocs as $coc)
                    <tr class="hover:bg-[#21262d]/50 transition duration-150">
                        <td class="px-6 py-1 text-xs text-gray-500">{{ $coc->coc_id }}</td>
                        <td class="px-6 py-1 text-xs font-medium text-[#f0f6fc]">{{ $coc->coc_no }}</td>
                        <td class="px-6 py-1 text-xs text-gray-300">{{ $coc->coc_type }}</td>
                        <td class="px-6 py-1 text-xs">
                            @if(strtolower($coc->coc_status) === 'available')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-[#238636]/10 text-[#3fb950] border border-[#238636]/20">
                                    Available
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-[#da3633]/10 text-[#f85149] border border-[#da3633]/20">
                                    {{ $coc->coc_status }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-1 text-xs text-gray-400">{{ $coc->created_at }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-6 text-center text-xs text-gray-400">
                            No COC records found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Section -->
        <div class="px-6 py-2 border-t border-[#30363d] bg-[#0d1117] custom-pagination-container">
            {{ $cocs->links() }}
        </div>
    </div>

    <!-- ================= MODAL COMPONENT (ADD COC SERIES) ================= -->
    <div 
        x-show="openAddModal" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
        style="display: none;"
    >
        <!-- Modal Backdrop click close -->
        <div class="absolute inset-0" @click="openAddModal = false"></div>

        <!-- Modal Content Box -->
        <div class="relative w-full max-w-md bg-[#161b22] border border-[#30363d] rounded-xl shadow-2xl overflow-hidden p-6 z-10 text-[#f0f6fc]">
    
            <!-- Header -->
            <div class="flex justify-between items-center mb-4 border-b border-[#30363d] pb-2">
                <h3 class="text-sm font-semibold text-[#f0f6fc]">Create New COC Series</h3>
                <!-- Pinalitan ang close action para sumakto sa Alpine state ng parent wrapper -->
                <button @click="openAddModal = false" class="text-gray-500 hover:text-white transition text-lg">&times;</button>
            </div>

            <!-- Form Block -->
            <form action="/coc-management" method="POST" id="addCocForm" onsubmit="return validateCocSeries(event)">
                @csrf
                
                <!-- Dropdown Select para sa COC Type -->
                <div class="mb-4">
                    <label class="block text-xs text-gray-400 mb-1 font-medium">COC Type</label>
                    <!-- Idinagdag ang onchange="resetAvailability()" para kung palitan ang type, mag-check ulit -->
                    <select name="coc_type" id="coc_type" required onchange="resetAvailability()" class="w-full bg-[#0d1117] text-[#f0f6fc] border border-[#30363d] rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-[#58a6ff]">
                        <option value="" disabled selected>Select Vehicle Type...</option>
                        <option value="MC">Motorcycle (MC)</option>
                        <option value="PC">Private Car (PC)</option>
                        <option value="TC">Tricycle (TC)</option>
                        <option value="CV">Commercial Vehicle (CV)</option>
                    </select>
                </div>

                <!-- Range Flex Input Fields -->
                <div class="grid grid-cols-2 gap-4 mb-3">
                    <div>
                        <label class="block text-xs text-gray-400 mb-1 font-medium">Series Start</label>
                        <!-- Idinagdag ang resetAvailability() sa oninput -->
                        <input 
                            type="number" 
                            name="series_start" 
                            id="series_start"
                            required 
                            placeholder="e.g. 1812400"
                            oninput="checkLiveSeriesRange(); resetAvailability();"
                            class="w-full bg-[#0d1117] text-[#f0f6fc] border border-[#30363d] rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-[#58a6ff]"
                        >
                    </div>
                    <div>
                        <label class="block text-xs text-gray-400 mb-1 font-medium">Series End</label>
                        <!-- Idinagdag ang resetAvailability() sa oninput -->
                        <input 
                            type="number" 
                            name="series_end" 
                            id="series_end"
                            required 
                            placeholder="e.g. 1812499"
                            oninput="checkLiveSeriesRange(); resetAvailability();"
                            class="w-full bg-[#0d1117] text-[#f0f6fc] border border-[#30363d] rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-[#58a6ff]"
                        >
                    </div>
                </div>

                <!-- Dynamic Alert Block (Para sa Range Warning at Availability Status) -->
                <div id="validation-error" class="hidden mb-4 p-2.5 bg-[#da3633]/10 border border-[#da3633]/30 text-[#f85149] rounded-lg text-[11px] flex items-center gap-2">
                    <span id="error-message">Ang Series Start ay hindi pwedeng mas malaki sa Series End.</span>
                </div>
                
                <div id="availability-success" class="hidden mb-4 p-2.5 bg-[#238636]/10 border border-[#238636]/30 text-[#3fb950] rounded-lg text-[11px] flex items-center gap-2">
                    <span>Available ang series range na ito at pwedeng i-save.</span>
                </div>

                <!-- Footer Action Buttons inside Modal -->
                <div class="flex justify-end gap-2 border-t border-[#30363d] pt-3 mt-4">
                    <button type="button" onclick="handleCheckAvailability()" class="bg-[#21262d] hover:bg-[#30363d] text-gray-300 px-4 py-1.5 rounded-lg text-xs font-semibold transition">
                        Check Availability
                    </button>
                    <!-- Naka-set sa disabled, opacity-50, at cursor-not-allowed sa simula -->
                    <button type="submit" id="submit-btn" disabled class="bg-[#238636] hover:bg-[#2ea043] text-white px-4 py-1.5 rounded-lg text-xs font-semibold transition opacity-50 cursor-not-allowed">
                        Save Series
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================= MODAL COMPONENT (DELETE COC SERIES) ================= -->
    <div x-show="openDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm" x-cloak>
        <div class="relative w-full max-w-md bg-[#161b22] border border-[#30363d] rounded-xl shadow-2xl overflow-hidden p-6 text-[#f0f6fc]">
            
            <!-- Header -->
            <div class="flex justify-between items-center mb-4 border-b border-[#30363d] pb-2">
                <h3 class="text-sm font-semibold text-[#f85149] flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Delete COC Series
                </h3>
                <button @click="openDeleteModal = false" class="text-gray-500 hover:text-white transition text-lg">&times;</button>
            </div>

            <!-- Form Block -->
            <form action="/coc-management/delete-series" method="POST" id="deleteCocForm" onsubmit="return validateDeleteRange(event)">
                @csrf
                @method('DELETE')
                
                <p class="text-[11px] text-gray-400 mb-4 bg-[#da3633]/10 p-2 border border-[#da3633]/20 rounded-lg">
                    <strong>Paalala:</strong> Ang mga COC na may status na <strong>Available</strong> lamang sa loob ng ibibigay mong range ang permanenteng mabubura.
                </p>

                <!-- Range Fields -->
                <div class="grid grid-cols-2 gap-4 mb-3">
                    <div>
                        <label class="block text-xs text-gray-400 mb-1 font-medium">Series Start</label>
                        <input 
                            type="number" 
                            name="delete_start" 
                            id="delete_start"
                            required 
                            placeholder="e.g. 18985001"
                            oninput="checkDeleteLiveRange()"
                            class="w-full bg-[#0d1117] text-[#f0f6fc] border border-[#30363d] rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-[#f85149]"
                        >
                    </div>
                    <div>
                        <label class="block text-xs text-gray-400 mb-1 font-medium">Series End</label>
                        <input 
                            type="number" 
                            name="delete_end" 
                            id="delete_end"
                            required 
                            placeholder="e.g. 18985010"
                            oninput="checkDeleteLiveRange()"
                            class="w-full bg-[#0d1117] text-[#f0f6fc] border border-[#30363d] rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-[#f85149]"
                        >
                    </div>
                </div>

                <!-- Warning Container -->
                <div id="delete-validation-error" class="hidden mb-4 p-2.5 bg-[#da3633]/10 border border-[#da3633]/30 text-[#f85149] rounded-lg text-[11px]">
                    <span id="delete-error-message">Ang Series Start ay hindi pwedeng mas malaki sa Series End.</span>
                </div>

                <!-- Footer Buttons -->
                <div class="flex justify-end gap-2 border-t border-[#30363d] pt-3 mt-4">
                    <button type="button" @click="openDeleteModal = false" class="bg-[#21262d] hover:bg-[#30363d] text-gray-300 px-4 py-1.5 rounded-lg text-xs font-semibold transition">
                        Cancel
                    </button>
                    <button type="submit" id="delete-submit-btn" class="bg-[#da3633] hover:bg-[#f85149] text-white px-4 py-1.5 rounded-lg text-xs font-semibold transition">
                        Confirm Delete
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript Logic at CSS Overrides -->
<script>
    // State tracker kung na-check na ba sa DB ang control numbers
    let isCheckedAndAvailable = false;

    // Live validation function habang nag-eencode ang user
    function checkLiveSeriesRange() {
        const startInput = document.getElementById('series_start').value;
        const endInput = document.getElementById('series_end').value;
        const errorDiv = document.getElementById('validation-error');
        const errorMsg = document.getElementById('error-message');

        if (startInput && endInput) {
            const startVal = parseInt(startInput, 10);
            const endVal = parseInt(endInput, 10);

            if (startVal > endVal) {
                errorMsg.innerText = "Warning: Ang Series Start ay hindi pwedeng mas malaki sa Series End.";
                errorDiv.classList.remove('hidden');
                return false;
            }
        }
        errorDiv.classList.add('hidden');
        return true;
    }

    // Awtomatikong ibinabalik sa disabled ang Save button kapag may binagong input ang user
    function resetAvailability() {
        isCheckedAndAvailable = false;
        document.getElementById('availability-success').classList.add('hidden');
        
        const submitBtn = document.getElementById('submit-btn');
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
    }

    // Function kapag pinindot ang Check Availability (Ikandinabit na sa totoong API)
    function handleCheckAvailability() {
        const typeInput = document.getElementById('coc_type').value;
        const startInput = document.getElementById('series_start').value;
        const endInput = document.getElementById('series_end').value;
        const errorDiv = document.getElementById('validation-error');
        const errorMsg = document.getElementById('error-message');
        const successDiv = document.getElementById('availability-success');
        const submitBtn = document.getElementById('submit-btn');

        // Validation 1: Siguraduhing may laman lahat ng inputs
        if (!typeInput || !startInput || !endInput) {
            errorMsg.innerText = "Mangyaring punan ang COC Type, Series Start, at Series End bago mag-check.";
            errorDiv.classList.remove('hidden');
            return;
        }

        // Validation 2: Siguraduhing valid ang range
        if (!checkLiveSeriesRange()) return;

        // I-reset ang states bago mag-fetch request
        errorDiv.classList.add('hidden');
        successDiv.classList.add('hidden');

        // Tinanggal ang '/api' at ginawang '/check-coc-availability' para tumugma sa web.php
        fetch(`/check-coc-availability?start=${startInput}&end=${endInput}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => {
            if (!res.ok) {
                throw new Error('Network response was not ok');
            }
            return res.json();
        })
        .then(data => {
            if (!data.available) {
                // Kapag existing na sa database (Used man o Available), babalaan ang user
                errorMsg.innerHTML = `<strong>Hindi Pwede:</strong> ${data.message}`;
                errorDiv.classList.remove('hidden');
                resetAvailability();
            } else {
                // Kapag malinis at walang kapareho, i-enable na ang Save Series button
                isCheckedAndAvailable = true;
                successDiv.classList.remove('hidden');
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        })
        .catch(err => {
            console.error(err);
            errorMsg.innerText = "May naganap na error sa pag-check sa database. Siguraduhing na-save mo ang route sa routes/web.php.";
            errorDiv.classList.remove('hidden');
            resetAvailability();
        });
    }

    // Pangwakas na harang sa form bago tuluyang ipadala ang data sa store method
    function validateCocSeries(event) {
        if (!isCheckedAndAvailable) {
            event.preventDefault();
            alert('Kailangan mo munang i-click ang Check Availability button.');
            return false;
        }
        return checkLiveSeriesRange();
    }

    function checkDeleteLiveRange() {
        const startInput = document.getElementById('delete_start').value;
        const endInput = document.getElementById('delete_end').value;
        const errorDiv = document.getElementById('delete-validation-error');
        const errorMsg = document.getElementById('delete-error-message');
        const submitBtn = document.getElementById('delete-submit-btn');

        if (startInput && endInput) {
            const startVal = parseInt(startInput, 10);
            const endVal = parseInt(endInput, 10);

            if (startVal > endVal) {
                errorMsg.innerText = "Warning: Ang Series Start ay hindi pwedeng mas malaki sa Series End sa pagbura.";
                errorDiv.classList.remove('hidden');
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                return false;
            }
        }
        errorDiv.classList.add('hidden');
        submitBtn.disabled = false;
        submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        return true;
    }

    function validateDeleteRange(event) {
        if (!checkDeleteLiveRange()) {
            event.preventDefault();
            return false;
        }
        // Kumpirmasyon bago bitayin ang pagbura
        return confirm('Sigurado ka ba na nais mong BURAHIN ang serye ng COC na ito? Hindi na ito mababawi kailanman.');
    }

    document.addEventListener('DOMContentLoaded', function () {
        const successAlert = document.getElementById('flash-success-alert');
        if (successAlert) {
            setTimeout(() => {
                // Dahan-dahang itatago bago tuluyang tanggalin sa screen
                successAlert.style.opacity = '0';
                successAlert.style.transition = 'opacity 0.5s ease';
                setTimeout(() => successAlert.remove(), 500);
            }, 5000); // Mawawala pagkatapos ng 5 segundo
        }
    });
</script>

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