<x-app-layout>
    <style>
        html, body {
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        html::-webkit-scrollbar, body::-webkit-scrollbar {
            display: none;
        }
        /* Pantanggal ng puting background kapag nag-autofill/select ng suggestion */
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus,
        input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 30px #0d1117 inset !important;
            -webkit-text-fill-color: #f0f6fc !important;
            caret-color: #f0f6fc !important;
        }
    </style>

    <div class="max-w-[95rem] mx-auto px-4 sm:px-6 lg:px-8"
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

                 // SEARCH STATES
                 searchType: 'plate_no',
                 searchValue: '',
                 isSearching: false,
                 searchResults: [],
                 searchError: '',

                 // API Check states para sa COC Validation
                 cocError: '',
                 cocValidating: false,
                 isCocVerified: false,

                 // API Check states para sa Policy Validation
                 policyError: '',
                 policyValidating: false,
                 isPolicyVerified: false,
                 policyTimeout: null,

                 denominations: {
                     'MC': ['MC', 'MTC'],
                     'PC': ['CAR', 'PASSENGER CAR', 'SEDAN', 'HATCHBACK', 'UTILITY VEHICLE', 'COUPE', 'SUV'],
                     'TC': ['TRICYCLE'],
                     'CV': ['TRUCK', 'TRAILER']
                 },

                 // Function para kusang mag-update ang Classification kapag nagbago ang Denomination
                 updateClassification() {
                     if (!this.denomination) {
                         this.vehicleType = '';
                         return;
                     }

                     let denomUpper = this.denomination.toUpperCase();
                     let matchedKey = '';

                     for (let classKey in this.denominations) {
                         if (this.denominations[classKey].includes(denomUpper)) {
                             matchedKey = classKey;
                             break;
                         }
                     }

                     this.vehicleType = matchedKey || 'PC';
                     
                     this.isCocVerified = false;
                     if (this.coc_no.length === 8) {
                         this.checkCocAvailability();
                     }
                 },

                 // Function para ma-reset ang buong form
                 resetForm() {
                     this.assured_name = '';
                     this.address = '';
                     this.vehicleType = '';
                     this.denomination = '';
                     this.year_model = '';
                     this.make = '';
                     this.series = '';
                     this.color = '';
                     this.mv_file = '';
                     this.plate_no = '';
                     this.chassis_no = '';
                     this.engine_no = '';
                     this.coc_no = '';
                     this.policy_no = '';
                     this.agent = '';
                     this.amount = '';
                     this.isCocVerified = false;
                     this.isPolicyVerified = false;
                     this.cocError = '';
                     this.policyError = '';
                     this.searchResults = [];
                     this.searchValue = '';
                 },

                 hasUnsavedChanges() {
                     return this.assured_name.trim() !== '' ||
                            this.address.trim() !== '' ||
                            this.vehicleType !== '' ||
                            this.plate_no.trim() !== '' ||
                            this.coc_no.trim() !== '' ||
                            this.policy_no.trim() !== '';
                 },

                 init() {
                     window.addEventListener('beforeunload', (event) => {
                         if (this.hasUnsavedChanges()) {
                             event.preventDefault();
                             event.returnValue = '';
                         }
                     });

                     window.addEventListener('pageshow', (event) => {
                         if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
                             this.resetForm();
                         }
                     });
                 },

                 filterNumbers(field) {
                     this[field] = this[field].replace(/[^0-9]/g, '');
                     
                     if(field === 'coc_no') {
                         this.isCocVerified = false;
                         this.cocError = '';
                         if(this.coc_no.length === 8) {
                             this.checkCocAvailability();
                         }
                     }

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

                 executeSearch() {
                     let val = this.searchValue.trim();
                     if(!val) {
                         this.searchError = 'Please enter a value to search.';
                         return;
                     }

                     this.isSearching = true;
                     this.searchError = '';
                     this.searchResults = [];

                     fetch(`/api/search-vehicle?type=${this.searchType}&value=${val}`)
                         .then(res => res.json())
                         .then(res => {
                             this.isSearching = false;
                             if(res.success && Array.isArray(res.data)) {
                                 if(res.data.length === 0) {
                                     this.searchError = 'No vehicle found.';
                                 } else {
                                     this.searchResults = res.data;
                                 }
                             } else {
                                 this.searchError = 'No vehicle found.';
                             }
                         })
                         .catch(() => {
                             this.isSearching = false;
                             this.searchError = 'Error connecting to search server.';
                         });
                 },

                 selectVehicle(d) {
                     this.assured_name  = d.assured || '';
                     this.address       = d.address || '';
                     this.denomination  = d.denomination ? d.denomination.toUpperCase() : '';
                     this.year_model    = d.year_model || '';
                     this.make          = d.make || '';
                     this.series        = d.series || '';
                     this.color         = d.color || '';
                     this.mv_file       = d.file_no || d.mv_file || '';
                     this.plate_no      = d.plate_no || '';
                     this.chassis_no    = d.chassis_no || '';
                     this.engine_no     = d.engine_no || '';

                     this.updateClassification();

                     this.searchResults = [];
                     this.searchValue = '';
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
                         this.isPolicyVerified && 
                         this.agent.trim() !== '' &&
                         this.amount !== '';
                 }
                }">

            <!-- Quick Vehicle Search Card -->
            <div class="bg-[#161b22] border border-[#30363d] rounded-xl p-4 shadow-xl mb-6">
                <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-3">Quick Vehicle Search & Autofill</h3>
                <div class="flex flex-col sm:flex-row gap-3">
                    <select x-model="searchType" class="bg-[#0d1117] text-[#f0f6fc] border border-[#30363d] rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-[#58a6ff] sm:w-1/4">
                        <option value="plate_no">Plate Number</option>
                        <option value="file_no">MV File Number</option>
                        <option value="chassis_no">Chassis Number</option>
                        <option value="engine_no">Engine Number</option>
                    </select>

                    <div class="flex-grow">
                        <input type="text" x-model="searchValue" 
                            autofocus
                            @keydown.enter.prevent="executeSearch()"
                            placeholder="Enter value and press Enter..." 
                            class="w-full bg-[#0d1117] text-[#f0f6fc] border border-[#30363d] rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-[#58a6ff] uppercase">
                    </div>

                    <button type="button" @click="executeSearch()" 
                            class="bg-[#21262d] hover:bg-[#30363d] text-[#f0f6fc] border border-[#30363d] px-4 py-2 rounded-lg text-xs font-semibold transition">
                        Search
                    </button>
                </div>

                <p x-show="isSearching" class="text-xs text-yellow-500 mt-2">Searching vehicle...</p>
                <p x-show="searchError" x-text="searchError" class="text-xs text-red-500 mt-2"></p>

                <div x-show="searchResults.length > 0" class="mt-3 border-t border-[#30363d] pt-3">
                    <p class="text-xs text-gray-400 mb-2">Select a vehicle to autofill the form:</p>
                    <div class="space-y-2 max-h-48 overflow-y-auto">
                        <template x-for="item in searchResults" :key="item.vehicle_id || item.plate_no">
                            <div @click="selectVehicle(item)" 
                               class="bg-[#0d1117] border border-[#30363d] hover:border-[#58a6ff] p-2.5 rounded-lg cursor-pointer flex justify-between items-center transition">
                                <div>
                                    <span class="font-bold text-[#58a6ff]" x-text="item.plate_no || item.file_no"></span> 
                                    <span class="text-gray-300 ml-2" x-text="(item.make || '') + ' ' + (item.series || '')"></span>
                                    <span class="text-gray-500 text-[10px] block" x-text="'Assured: ' + (item.assured || 'N/A')"></span>
                                </div>
                                <span class="text-[10px] bg-[#238636] text-white px-2.5 py-1 rounded font-medium">Select</span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Form Submission -->
            <form action="/ctpl-issuance" method="POST" @submit="window.removeEventListener('beforeunload', null)" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
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
                        <input type="text" name="address" x-model="address" maxlength="100" required placeholder="COMPLETE ADDRESS" 
                            class="w-full bg-[#0d1117] text-[#f0f6fc] border border-[#30363d] rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-[#58a6ff] uppercase">
                    </div>
                </div>

                <!-- COLUMN 2: Vehicle Specification -->
                <div class="lg:col-span-5 bg-[#161b22] border border-[#30363d] rounded-xl p-5 shadow-xl space-y-4 min-h-[460px]">
                    <h3 class="text-sm font-semibold border-b border-[#30363d] pb-2 text-[#58a6ff]">2. Vehicle Specification</h3>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-gray-400 mb-1 font-medium">Year Model</label>
                            <input type="number" name="year_model" x-model="year_model" min="1900" max="2100" required placeholder="E.G. 2026" 
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

                    <!-- DENOMINATION FIELD NA NAKALAGAY SA PINAKABABA -->
                    <div>
                        <label class="block text-xs text-gray-400 mb-1 font-medium">Denomination</label>
                        <select name="denomination" x-model="denomination" @change="updateClassification()" required 
                            class="w-full bg-[#0d1117] text-[#f0f6fc] border border-[#30363d] rounded-lg px-2 py-2 text-xs focus:outline-none focus:border-[#58a6ff]">
                            <option value="" disabled selected>Select Denomination...</option>
                            <optgroup label="Motorcycle">
                                <option value="MC">MC</option>
                                <option value="MTC">MTC</option>
                            </optgroup>
                            <optgroup label="Private Car">
                                <option value="CAR">CAR</option>
                                <option value="PASSENGER CAR">PASSENGER CAR</option>
                                <option value="SEDAN">SEDAN</option>
                                <option value="HATCHBACK">HATCHBACK</option>
                                <option value="UTILITY VEHICLE">UTILITY VEHICLE</option>
                                <option value="COUPE">COUPE</option>
                                <option value="SUV">SUV</option>
                            </optgroup>
                            <optgroup label="Tricycle">
                                <option value="TRICYCLE">TRICYCLE</option>
                            </optgroup>
                            <optgroup label="Commercial Vehicle">
                                <option value="TRUCK">TRUCK</option>
                                <option value="TRAILER">TRAILER</option>
                            </optgroup>
                        </select>
                        
                        <!-- NAKATAGO NA CLASSIFICATION (HIDDEN INPUT PARA SA BACKEND) -->
                        <input type="hidden" name="vehicle_type" x-model="vehicleType">
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

                        <!-- COC Number -->
                        <div>
                            <label class="block text-xs text-gray-400 mb-1 font-medium">COC Number</label>
                            <input type="text" name="coc_no" x-model="coc_no" @input="filterNumbers('coc_no')" maxlength="8" :disabled="!isSection1And2Valid()" required placeholder="E.G. 12345678" 
                                class="w-full bg-[#0d1117] text-[#f0f6fc] border rounded-lg px-3 py-2 text-xs focus:outline-none disabled:cursor-not-allowed"
                                :class="cocError ? 'border-red-500 focus:border-red-500' : (isCocVerified ? 'border-green-500 focus:border-green-500' : 'border-[#30363d] focus:border-[#58a6ff]')">
                            
                            <p x-show="cocValidating" class="text-[10px] text-yellow-500 mt-1">Verifying availability...</p>
                            <p x-show="cocError" x-text="cocError" class="text-[10px] text-red-500 mt-1"></p>
                            <p x-show="isCocVerified" class="text-[10px] text-green-500 mt-1">✓ COC is valid and available for this vehicle type.</p>
                        </div>

                        <!-- Policy Number -->
                        <div>
                            <label class="block text-xs text-gray-400 mb-1 font-medium">Policy Number</label>
                            <input type="text" name="policy_no" x-model="policy_no" @input="filterNumbers('policy_no')" maxlength="8" :disabled="!isSection1And2Valid()" required placeholder="E.G. 976503" 
                                class="w-full bg-[#0d1117] text-[#f0f6fc] border rounded-lg px-3 py-2 text-xs focus:outline-none disabled:cursor-not-allowed"
                                :class="policyError ? 'border-red-500 focus:border-red-500' : (isPolicyVerified ? 'border-green-500 focus:border-green-500' : 'border-[#30363d] focus:border-[#58a6ff]')">
                            
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
                                <input type="number" step="0.01" name="amount" x-model="amount" :disabled="!isSection1And2Valid()" required placeholder="1,050.00" 
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