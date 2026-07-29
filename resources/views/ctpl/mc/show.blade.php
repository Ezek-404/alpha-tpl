<x-app-layout>
    <div class="py-6 bg-[#0d1117] min-h-screen text-black">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Action Buttons (Hindi makakasama sa Print) -->
            <div class="mb-6 flex justify-between items-center print:hidden">
                <h2 class="text-xl font-bold tracking-tight text-[#f0f6fc]">COC & Invoice Result</h2>
                <div class="space-x-2">
                    <button onclick="window.print()" class="bg-[#21262d] hover:bg-[#30363d] text-[#f0f6fc] border border-[#30363d] px-4 py-2 rounded-lg text-xs font-semibold transition">
                        Print COC
                    </button>
                    <a href="/ctpl-issuance" class="bg-[#238636] hover:bg-[#2ea043] text-white px-4 py-2 rounded-lg text-xs font-semibold transition">
                        Issue Another Policy
                    </a>
                </div>
            </div>

            <!-- COC Form Container na may Background Image -->
            <div class="relative w-full max-w-[800px] mx-auto bg-white shadow-2xl overflow-hidden print:shadow-none print:w-full">
                
                <!-- Background Image ng COC -->
                <img src="{{ asset('images/coc_mc.png') }}" alt="COC Template" class="w-full h-auto block">

                <!-- MGA NAKALAGAY NA DATA (Absolute Positioning para eksakto sa mga linya) -->
                <div class="absolute inset-0 text-[11px] font-mono font-bold uppercase tracking-tight">
                    
                    <!-- NO. / POLICY NO. -->
                    <div class="absolute top-[236px] left-[700px] text-red-600">{{ $policy->coc_no }}</div>
                    <div class="absolute top-[315px] left-[780px]">{{ $policy->policy_no }}</div>

                    <!-- NAME AND ADDRESS OF INSURED -->
                    <div class="absolute top-[365px] left-[55px] max-w-[350px] leading-tight">
                        {{ $policy->assured }}<br>
                        <span class="text-[9px] font-normal text-gray-700">{{ $policy->address }}</span>
                    </div>

                    <!-- DATES & PERIOD OF INSURANCE -->
                    <div class="absolute top-[430px] left-[550px]">{{ \Carbon\Carbon::parse($policy->created_at)->format('m/d/Y') }}</div>
                    <div class="absolute top-[430px] left-[685px]">{{ $policy->policy_no }}</div>
                    <div class="absolute top-[535px] left-[565px]">{{ \Carbon\Carbon::parse($policy->created_at)->format('F d, Y') }}</div>
                    <div class="absolute top-[535px] left-[770px]">{{ \Carbon\Carbon::parse($policy->created_at)->addYear()->format('F d, Y') }}</div>

                    <!-- SCHEDULED VEHICLE (Year Model, Make, Type of Body, Color, MV File) -->
                    <div class="absolute top-[633px] left-[55px]">{{ $policy->year_model ?? '' }}</div>
                    <div class="absolute top-[633px] left-[210px]">{{ $policy->make }}</div>
                    <div class="absolute top-[633px] left-[420px]">{{ $policy->denomination }}</div>
                    <div class="absolute top-[633px] left-[620px]">{{ $policy->color }}</div>
                    <div class="absolute top-[633px] left-[790px]">{{ $policy->mv_file }}</div>

                    <!-- PLATE NO, CHASSIS NO, ENGINE NO -->
                    <div class="absolute top-[695px] left-[55px]">{{ $policy->plate_no }}</div>
                    <div class="absolute top-[695px] left-[210px]">{{ $policy->chassis_no }}</div>
                    <div class="absolute top-[695px] left-[420px]">{{ $policy->engine_no }}</div>

                    <!-- PREMIUM PAID -->
                    <div class="absolute top-[825px] left-[620px] text-right">₱ {{ number_format($policy->amount, 2) }}</div>

                </div>
            </div>

            <!-- Service Invoice Section sa ibaba -->
            <div class="mt-8 bg-[#161b22] border border-[#30363d] rounded-xl p-5 shadow-xl text-[#f0f6fc] print:hidden">
                <h3 class="text-sm font-semibold border-b border-[#30363d] pb-2 text-[#2ea043] mb-3">Service Invoice Details</h3>
                <div class="grid grid-cols-2 gap-4 text-xs">
                    <div><span class="text-gray-400">Agent:</span> <span class="uppercase font-bold">{{ $policy->agent }}</span></div>
                    <div><span class="text-gray-400">Date Issued:</span> <span>{{ \Carbon\Carbon::parse($policy->created_at)->format('F d, Y h:i A') }}</span></div>
                    <div><span class="text-gray-400">Amount Paid:</span> <span class="text-green-400 font-bold">₱ {{ number_format($policy->amount, 2) }}</span></div>
                    <div><span class="text-gray-400">Status:</span> <span class="text-green-500 font-bold uppercase">Paid</span></div>
                </div>
            </div>

        </div>
    </div>

    <!-- CSS para sa tuwing magpi-print para lumabas lang ay ang mismong COC -->
    @push('styles')
    <style>
        @media print {
            body { background: white !important; color: black !important; }
            nav, header, footer, .print\:hidden { display: none !important; }
        }
    </style>
    @endpush
</x-app-layout>