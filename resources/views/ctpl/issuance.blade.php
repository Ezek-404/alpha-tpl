<x-app-layout>
    <style>
        html, body {
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        html::-webkit-scrollbar, body::-webkit-scrollbar {
            display: none;
        }
    </style>

    <div class="py-6 bg-[#0d1117] min-h-screen text-[#f0f6fc]" 
         x-data="{ 
            assured_name: '',
            address: '',
            vehicleType: '',
            denomination: '',
            year_model: '',
            make: '',
            series: '',
            color: '',
            mv_file: '',
            plate_no: '',
            chassis_no: '',
            engine_no: '',
            
            coc_no: '',
            policy_no: '',
            agent: '',
            amount: '',

            // API Check states para sa COC Validation
            cocError: '',
            cocValidating: false,
            isCocVerified: false,

            // BAGONG DAGDAG: API Check states para sa Policy Validation
            policyError: '',
            policyValidating: false,
            isPolicyVerified: false,
            policyTimeout: null,

            denominations: {
                'MC': ['MC', 'MTC'],
                'PC': ['Car', 'Passenger Car', 'Sedan', 'Hatchback', 'Utility Vehicle', 'Coupe', 'SUV'],
                'TC': ['Tricycle'],
                'CV': ['Truck', 'Trailer']
            },

            // Filter para pure numbers lang ang pumasok
            filterNumbers(field) {
                this[field] = this[field].replace(/[^0-9]/g, '');
                
                // Kapag COC ang binago, i-reset ang server validation state nito
                if(field === 'coc_no') {
                    this.isCocVerified = false;
                    this.cocError = '';
                    if(this.coc_no.length === 8) {
                        this.checkCocAvailability();
                    }
                }

                // BAGONG DAGDAG: Realtime validation para sa Policy Number gamit ang debounce (500ms)
                if(field === 'policy_no') {
                    this.isPolicyVerified = false;
                    this.policyError = '';
                    
                    clearTimeout(this.policyTimeout);
                    if(this.policy_no.trim() !== '') {
                        this.policyTimeout = setTimeout(() => {
                            this.checkPolicyUniqueness();
                        }, 500);
                    }
                }
            },

            // Realtime backend check gamit ang Fetch API para sa COC
            checkCocAvailability() {
                if(this.coc_no.length !== 8 || !this.vehicleType) return;
                
                this.cocValidating = true;
                this.cocError = '';
                
                fetch(`/api/validate-coc?coc_no=${this.coc_no}&classification=${this.vehicleType}`)
                    .then(res => res.json())
                    .then(data => {
                        this.cocValidating = false;
                        if(data.valid) {
                            this.isCocVerified = true;
                            this.cocError = '';
                        } else {
                            this.isCocVerified = false;
                            this.cocError = data.message;
                        }
                    })
                    .catch(() => {
                        this.cocValidating = false;
                        this.cocError = 'Error connecting to validation server.';
                    });
            },

            // BAGONG DAGDAG: Realtime backend check gamit ang Fetch API para sa Policy
            checkPolicyUniqueness() {
                if(this.policy_no.trim() === '') return;
                
                this.policyValidating = true;
                this.policyError = '';
                
                fetch(`/api/validate-policy?policy_no=${this.policy_no}`)
                    .then(res => res.json())
                    .then(data => {
                        this.policyValidating = false;
                        if(data.valid) {
                            this.isPolicyVerified = true;
                            this.policyError = '';
                        } else {
                            this.isPolicyVerified = false;
                            this.policyError = data.message;
                        }
                    })
                    .catch(() => {
                        this.policyValidating = false;
                        this.policyError = 'Error connecting to validation server.';
                    });
            },

            isSection1And2Valid() {
                return this.assured_name.trim() !== '' &&
                       this.address.trim() !== '' &&
                       this.vehicleType !== '' &&
                       this.denomination !== '' &&
                       this.year_model >= 1900 && this.year_model <= 2100 &&
                       this.make.trim() !== '' &&
                       this.series.trim() !== '' &&
                       this.color.trim() !== '' &&
                       this.mv_file.trim() !== '' &&
                       this.plate_no.trim() !== '' &&
                       this.chassis_no.trim() !== '' &&
                       this.engine_no.trim() !== '';
            },

            isFormValid() {
                return this.isSection1And2Valid() &&
                    this.isCocVerified &&
                    this.isPolicyVerified && // INAYOS: Dapat verified at unique ang policy mula sa database
                    this.agent.trim() !== '' &&
                    this.amount !== '';
            }
         }">
        <div class="max-w-[95rem] mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="mb-6">
                <h2 class="text-xl font-bold tracking-tight">CTPL Insurance Issuance</h2>
            </div>

            <form action="/ctpl-issuance" method="POST" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                @csrf

                <!-- COLUMN 1: Assured Details -->
                <div class="lg:col-span-3 bg-[#161b22] border border-[#30363d] rounded-xl p-5 shadow-xl space-y-4 min-h-[460px]">
                    <h3 class="text-sm font-semibold border-b border-[#30363d] pb-2 text-[#58a6ff]">1. Assured Details</h3>
                    
                    <div>
                        <label class="block text-xs text-gray-400 mb-1 font-medium">Assured Name</label>
                        <input type="text" name="assured" x-model="assured_name" maxlength="100" required placeholder="JUANA DELA CRUZ" 
                            class="w-full bg-[#0d1117] text-[#f0f6fc] border border-[#30363d] rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-[#58a6ff] uppercase">
                    </div>

                    <div>
                        <label class="block text-xs text-gray-400 mb-1 font-medium">Address</label>
                        <textarea name="address" x-model="address" maxlength="100" rows="7" required placeholder="Complete Address" 
                            class="w-full bg-[#0d1117] text-[#f0f6fc] border border-[#30363d] rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-[#58a6ff] resize-none"></textarea>
                    </div>
                </div>

                <!-- COLUMN 2: Vehicle Specification -->
                <div class="lg:col-span-5 bg-[#161b22] border border-[#30363d] rounded-xl p-5 shadow-xl space-y-4 min-h-[460px]">
                    <h3 class="text-sm font-semibold border-b border-[#30363d] pb-2 text-[#58a6ff]">2. Vehicle Specification</h3>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-gray-400 mb-1 font-medium">Classification</label>
                            <select name="vehicle_type" x-model="vehicleType" @change="denomination = ''; isCocVerified = false; checkCocAvailability();" required 
                                class="w-full bg-[#0d1117] text-[#f0f6fc] border border-[#30363d] rounded-lg px-2 py-2 text-xs focus:outline-none focus:border-[#58a6ff]">
                                <option value="" disabled selected>Select Class...</option>
                                <option value="MC">Motorcycle (MC)</option>
                                <option value="PC">Private Car (PC)</option>
                                <option value="TC">Tricycle (TC)</option>
                                <option value="CV">Commercial (CV)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs text-gray-400 mb-1 font-medium">Denomination</label>
                            <select name="denomination" x-model="denomination" :disabled="!vehicleType" required 
                                class="w-full bg-[#0d1117] text-[#f0f6fc] border border-[#30363d] rounded-lg px-2 py-2 text-xs focus:outline-none focus:border-[#58a6ff] disabled:opacity-50">
                                <option value="" disabled selected>Select Denomination...</option>
                                <template x-if="vehicleType">
                                    <template x-for="item in denominations[vehicleType]" :key="item">
                                        <option :value="item" x-text="item"></option>
                                    </template>
                                </template>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-gray-400 mb-1 font-medium">Year Model</label>
                            <input type="number" name="year_model" x-model="year_model" min="1900" max="2100" required placeholder="e.g. 2026" 
                                class="w-full bg-[#0d1117] text-[#f0f6fc] border border-[#30363d] rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-[#58a6ff]">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1 font-medium">Make</label>
                            <input type="text" name="make" x-model="make" maxlength="50" required placeholder="TOYOTA" 
                                class="w-full bg-[#0d1117] text-[#f0f6fc] border border-[#30363d] rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-[#58a6ff] uppercase">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1 font-medium">Series</label>
                            <input type="text" name="series" x-model="series" maxlength="50" required placeholder="VIOS" 
                                class="w-full bg-[#0d1117] text-[#f0f6fc] border border-[#30363d] rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-[#58a6ff] uppercase">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1 font-medium">Color</label>
                            <input type="text" name="color" x-model="color" maxlength="50" required placeholder="BLACK" 
                                class="w-full bg-[#0d1117] text-[#f0f6fc] border border-[#30363d] rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-[#58a6ff] uppercase">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-gray-400 mb-1 font-medium">MV File</label>
                            <input type="text" name="mv_file" x-model="mv_file" maxlength="15" required placeholder="MV FILE NO." 
                                class="w-full bg-[#0d1117] text-[#f0f6fc] border border-[#30363d] rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-[#58a6ff] uppercase">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1 font-medium">Plate Number</label>
                            <input type="text" name="plate_no" x-model="plate_no" maxlength="7" required placeholder="PLATE NO." 
                                class="w-full bg-[#0d1117] text-[#f0f6fc] border border-[#30363d] rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-[#58a6ff] uppercase">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1 font-medium">Chassis Number</label>
                            <input type="text" name="chassis_no" x-model="chassis_no" maxlength="30" required placeholder="CHASSIS NO." 
                                class="w-full bg-[#0d1117] text-[#f0f6fc] border border-[#30363d] rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-[#58a6ff] uppercase">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1 font-medium">Engine Number</label>
                            <input type="text" name="engine_no" x-model="engine_no" maxlength="30" required placeholder="ENGINE NO." 
                                class="w-full bg-[#0d1117] text-[#f0f6fc] border border-[#30363d] rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-[#58a6ff] uppercase">
                        </div>
                    </div>
                </div>

                <!-- COLUMN 3: Allocation and Payment -->
                <div class="lg:col-span-4 bg-[#161b22] border border-[#30363d] rounded-xl p-5 shadow-xl space-y-4 min-h-[460px] flex flex-col justify-between transition-opacity duration-300"
                     :class="!isSection1And2Valid() ? 'opacity-40 select-none' : ''">
                    <div class="space-y-4">
                        <h3 class="text-sm font-semibold border-b border-[#30363d] pb-2 text-[#2ea043] flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            3. Allocation and Payment
                        </h3>

                        <!-- COC Number (Pure Numbers, Max 8) -->
                        <div>
                            <label class="block text-xs text-gray-400 mb-1 font-medium">COC Number</label>
                            <input type="text" name="coc_no" x-model="coc_no" @input="filterNumbers('coc_no')" maxlength="8" :disabled="!isSection1And2Valid()" required placeholder="e.g. 12345678" 
                                class="w-full bg-[#0d1117] text-[#f0f6fc] border rounded-lg px-3 py-2 text-xs focus:outline-none disabled:cursor-not-allowed"
                                :class="cocError ? 'border-red-500 focus:border-red-500' : (isCocVerified ? 'border-green-500 focus:border-green-500' : 'border-[#30363d] focus:border-[#58a6ff]')">
                            
                            <!-- Realtime Feedback Messages -->
                            <p x-show="cocValidating" class="text-[10px] text-yellow-500 mt-1">Verifying availability...</p>
                            <p x-show="cocError" x-text="cocError" class="text-[10px] text-red-500 mt-1"></p>
                            <p x-show="isCocVerified" class="text-[10px] text-green-500 mt-1">✓ COC is valid and available for this vehicle type.</p>
                        </div>

                        <!-- Policy Number -->
                        <div>
                            <label class="block text-xs text-gray-400 mb-1 font-medium">Policy Number</label>
                            <input type="text" name="policy_no" x-model="policy_no" @input="filterNumbers('policy_no')" maxlength="8" :disabled="!isSection1And2Valid()" required placeholder="e.g. 976503" 
                                class="w-full bg-[#0d1117] text-[#f0f6fc] border rounded-lg px-3 py-2 text-xs focus:outline-none disabled:cursor-not-allowed"
                                :class="policyError ? 'border-red-500 focus:border-red-500' : (isPolicyVerified ? 'border-green-500 focus:border-green-500' : 'border-[#30363d] focus:border-[#58a6ff]')">
                            
                            <!-- Realtime Feedback Messages para sa Policy -->
                            <p x-show="policyValidating" class="text-[10px] text-yellow-500 mt-1">Checking policy availability...</p>
                            <p x-show="policyError" x-text="policyError" class="text-[10px] text-red-500 mt-1"></p>
                            <p x-show="isPolicyVerified" class="text-[10px] text-green-500 mt-1">✓ Policy number is available and unique.</p>
                        </div>

                        <!-- Agent Name -->
                        <div>
                            <label class="block text-xs text-gray-400 mb-1 font-medium">Agent Name</label>
                            <input type="text" name="agent" x-model="agent" :disabled="!isSection1And2Valid()" required placeholder="E.G. TS NA" 
                                class="w-full bg-[#0d1117] text-[#f0f6fc] border border-[#30363d] rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-[#58a6ff] uppercase disabled:cursor-not-allowed">
                        </div>

                        <!-- Amount Paid -->
                        <div>
                            <label class="block text-xs text-gray-400 mb-1 font-medium">Amount Paid (Premium)</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 text-xs">₱</span>
                                <input type="number" step="0.01" name="amount" x-model="amount" :disabled="!isSection1And2Valid()" required placeholder="1050.00" 
                                    class="w-full bg-[#0d1117] text-[#f0f6fc] border border-[#30363d] rounded-lg pl-7 pr-3 py-2 text-xs focus:outline-none focus:border-[#58a6ff] disabled:cursor-not-allowed">
                            </div>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" :disabled="!isFormValid()"
                                class="w-full py-2 rounded-lg text-xs font-semibold transition shadow-md text-white
                                       disabled:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed
                                       bg-[#238636] hover:bg-[#2ea043]">
                            Issue CTPL Policy
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</x-app-layout>