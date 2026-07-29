<x-app-layout>
    <div class="py-6 bg-[#0d1117] min-h-screen text-black">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Navigation Tabs & Print Button Bar -->
            <div class="mb-6 flex justify-between items-center print:hidden">
                <!-- Tab Buttons sa Kaliwa -->
                <div class="inline-flex rounded-md shadow-sm bg-[#161b22] p-1 border border-[#30363d] relative z-20">
                    <button type="button" onclick="switchTab('coc')" id="tab-coc-btn" class="px-4 py-2 text-xs font-semibold rounded-md bg-[#1f6feb] text-white transition cursor-pointer">
                        COC & POLICY
                    </button>
                    <button type="button" onclick="switchTab('invoice')" id="tab-invoice-btn" class="px-4 py-2 text-xs font-semibold rounded-md text-gray-300 hover:text-white transition cursor-pointer">
                        SERVICE INVOICE
                    </button>
                </div>

                <!-- Right Side Actions -->
                <div class="space-x-2 relative z-20">
                    <button onclick="window.print()" class="bg-[#238636] hover:bg-[#2ea043] text-white px-4 py-2 rounded-lg text-xs font-semibold transition shadow cursor-pointer">
                        🖨️ Print Documents
                    </button>
                    <a href="/ctpl-issuance" class="bg-[#21262d] hover:bg-[#30363d] text-[#f0f6fc] border border-[#30363d] px-4 py-2 rounded-lg text-xs font-semibold transition">
                        + New Issuance
                    </a>
                </div>
            </div>

            <!-- TAB 1 CONTAINER: COC & POLICY -->
            <div id="tab-coc-container" class="space-y-6">
                <!-- Page 1: COC -->
                <div id="page-coc" class="relative w-full max-w-[850px] mx-auto bg-white shadow-2xl overflow-hidden print:shadow-none print:w-full block">
                    <img src="{{ asset('images/coc_pc.png') }}" alt="COC Template" class="w-full h-auto block print:hidden">
                    
                    <div class="absolute inset-0 text-[11px] font-bold uppercase tracking-tight data-container" style="font-family: 'Times New Roman', Times, serif !important;">
                        <div class="absolute top-[140px] left-[627px]">{{ $policy->policy_no }}</div>
                        <div class="absolute top-[365px] left-[55px] max-w-[350px] leading-tight">{{ $policy->assured }}</div>
                        <div class="absolute top-[365px] left-[55px] max-w-[350px] leading-tight">{{ $policy->address }}</div>

                        <div class="absolute top-[430px] left-[550px]">{{ \Carbon\Carbon::parse($policy->created_at)->format('m/d/Y') }}</div>
                        <div class="absolute top-[535px] left-[565px]">{{ \Carbon\Carbon::parse($policy->created_at)->format('F d, Y') }}</div>
                        <div class="absolute top-[535px] left-[770px]">{{ \Carbon\Carbon::parse($policy->created_at)->addYear()->format('F d, Y') }}</div>

                        <div class="absolute top-[633px] left-[55px]">{{ $policy->year_model ?? '' }}</div>
                        <div class="absolute top-[633px] left-[210px]">{{ $policy->make }}</div>
                        <div class="absolute top-[633px] left-[420px]">{{ $policy->denomination }}</div>
                        <div class="absolute top-[633px] left-[620px]">{{ $policy->color }}</div>
                        <div class="absolute top-[633px] left-[790px]">{{ $policy->mv_file }}</div>

                        <div class="absolute top-[695px] left-[55px]">{{ $policy->plate_no }}</div>
                        <div class="absolute top-[695px] left-[210px]">{{ $policy->chassis_no }}</div>
                        <div class="absolute top-[695px] left-[420px]">{{ $policy->engine_no }}</div>

                        <div class="absolute top-[825px] left-[620px] text-right">₱ {{ number_format($policy->amount, 2) }}</div>
                    </div>
                </div>

                <!-- Page 2: Stand-Alone Private Car Policy -->
                <div id="page-policy" class="relative w-full max-w-[850px] mx-auto bg-white shadow-2xl overflow-hidden print:shadow-none print:w-full block">
                    <img src="{{ asset('images/pc_policy.jpg') }}" alt="Private Car Policy Template" class="w-full h-auto block print:hidden">
                    
                    <div class="absolute inset-0 text-[11px] font-bold uppercase tracking-tight data-container" style="font-family: 'Times New Roman', Times, serif !important;">
                        <div class="absolute top-[195px] left-[780px]">{{ $policy->policy_no }}</div>
                        <div class="absolute top-[218px] left-[780px] text-red-600">{{ $policy->coc_no }}</div>

                        <div class="absolute top-[195px] left-[85px] max-w-[380px] leading-tight">
                            {{ $policy->assured }}<br>
                            <span class="text-[9px] font-normal text-gray-700 address-sub">{{ $policy->address }}</span>
                        </div>

                        <div class="absolute top-[242px] left-[560px]">{{ \Carbon\Carbon::parse($policy->created_at)->format('m/d/Y') }}</div>
                        <div class="absolute top-[265px] left-[565px]">{{ \Carbon\Carbon::parse($policy->created_at)->format('F d, Y') }}</div>
                        <div class="absolute top-[265px] left-[770px]">{{ \Carbon\Carbon::parse($policy->created_at)->addYear()->format('F d, Y') }}</div>

                        <div class="absolute top-[325px] left-[85px]">{{ $policy->year_model ?? '' }}</div>
                        <div class="absolute top-[325px] left-[250px]">{{ $policy->make }}</div>
                        <div class="absolute top-[325px] left-[430px]">{{ $policy->denomination }}</div>
                        <div class="absolute top-[325px] left-[620px]">{{ $policy->color }}</div>
                        <div class="absolute top-[325px] left-[790px]">{{ $policy->mv_file }}</div>

                        <div class="absolute top-[365px] left-[85px]">{{ $policy->plate_no }}</div>
                        <div class="absolute top-[365px] left-[250px]">{{ $policy->chassis_no }}</div>
                        <div class="absolute top-[365px] left-[500px]">{{ $policy->engine_no }}</div>

                        <div class="absolute top-[438px] left-[750px] text-right">₱ {{ number_format($policy->amount, 2) }}</div>
                        <div class="absolute top-[460px] left-[750px] text-right">₱ {{ number_format($policy->amount, 2) }}</div>
                        <div class="absolute top-[505px] left-[780px] text-right">₱ {{ number_format($policy->amount, 2) }}</div>
                    </div>
                </div>
            </div>

            <!-- TAB 2 CONTAINER: SERVICE INVOICE -->
            <div id="tab-invoice-container" class="relative w-full max-w-[580px] mx-auto bg-white shadow-2xl overflow-hidden print:shadow-none print:w-full" style="display: none;">
                <img src="{{ asset('images/invoice.jpg') }}" alt="Service Invoice Template" class="w-full h-auto block print:hidden">
                
                <div class="absolute inset-0 text-[10px] font-bold uppercase tracking-tight data-container" style="font-family: 'Times New Roman', Times, serif !important;">
                    <div class="absolute top-[210px] left-[615px]">{{ \Carbon\Carbon::parse($policy->created_at)->format('m / d / Y') }}</div>

                    <div class="absolute top-[245px] left-[150px]">{{ $policy->assured }}</div>
                    <div class="absolute top-[275px] left-[130px] text-[9px] address-sub">{{ $policy->address }}</div>

                    <div class="absolute top-[305px] left-[140px] text-[9px]">{{ $policy->assured }}</div>
                    <div class="absolute top-[305px] left-[680px]">{{ $policy->tin ?? 'N/A' }}</div>

                    <div class="absolute top-[430px] left-[260px]">{{ $policy->policy_no }}</div>

                    <div class="absolute top-[375px] left-[720px] text-right">₱ {{ number_format($policy->amount, 2) }}</div>
                    <div class="absolute top-[810px] left-[650px] text-right">₱ {{ number_format($policy->amount, 2) }}</div>

                    <div class="absolute top-[920px] left-[110px] uppercase">{{ $policy->agent }}</div>
                </div>
            </div>

        </div>
    </div>

    <style>
        @media print {
            @page { size: letter portrait; margin: 0; }
            body, html { 
                margin: 0 !important; 
                padding: 0 !important; 
                background: none !important; 
                color: black !important; 
                font-family: 'Times New Roman', Times, serif !important; 
            }
            nav, header, footer, .no-print, .print\:hidden { display: none !important; }
            .shadow-2xl { box-shadow: none !important; }

            /* Pilitin maging 18px ang font-size sa loob ng print mode */
            .data-container, .data-container div {
                font-size: 16px !important;
            }
            .address-sub {
                font-size: 14px !important; /* Para sa address para hindi sumobra ang laki sa linya */
            }

            body.printing-coc #tab-coc-container {
                display: block !important;
            }
            body.printing-coc #tab-invoice-container {
                display: none !important;
            }

            body.printing-invoice #tab-coc-container {
                display: none !important;
            }
            body.printing-invoice #tab-invoice-container {
                display: block !important;
                width: 8.5in !important;
                height: 10.5in !important;
                position: relative !important;
            }

            #page-coc, #page-policy {
                background-image: none !important;
                box-shadow: none;
                width: 8.5in !important;
                height: 10.5in !important;
                position: relative !important;
                display: block !important;
                font-family: 'Times New Roman', Times, serif !important;
            }

            #page-coc {
                page-break-after: always !important;
                break-after: page !important;
            }

            #page-coc img, #page-policy img, #tab-invoice-container img {
                display: none !important;
            }
        }
    </style>

    <script>
        function switchTab(type) {
            const cocContainer = document.getElementById('tab-coc-container');
            const invoiceContainer = document.getElementById('tab-invoice-container');
            const cocBtn = document.getElementById('tab-coc-btn');
            const invoiceBtn = document.getElementById('tab-invoice-btn');

            if (type === 'coc') {
                cocContainer.style.display = 'block';
                invoiceContainer.style.display = 'none';
                
                cocBtn.className = 'px-4 py-2 text-xs font-semibold rounded-md bg-[#1f6feb] text-white transition cursor-pointer';
                invoiceBtn.className = 'px-4 py-2 text-xs font-semibold rounded-md text-gray-300 hover:text-white transition cursor-pointer';
            } else {
                invoiceContainer.style.display = 'block';
                cocContainer.style.display = 'none';
                
                invoiceBtn.className = 'px-4 py-2 text-xs font-semibold rounded-md bg-[#1f6feb] text-white transition cursor-pointer';
                cocBtn.className = 'px-4 py-2 text-xs font-semibold rounded-md text-gray-300 hover:text-white transition cursor-pointer';
            }
        }

        window.addEventListener('beforeprint', function() {
            const isInvoiceVisible = document.getElementById('tab-invoice-container').style.display !== 'none';
            if (isInvoiceVisible) {
                document.body.classList.add('printing-invoice');
                document.body.classList.remove('printing-coc');
            } else {
                document.body.classList.add('printing-coc');
                document.body.classList.remove('printing-invoice');
            }
        });
    </script>
</x-app-layout>