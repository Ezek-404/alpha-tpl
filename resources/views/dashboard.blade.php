<x-app-layout>
    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- RESPONSIVE GRID: 1 column sa Mobile, 2 sa Tablet, 4 sa Desktop -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                
                @foreach([
                    'MC' => 'Motorcycle', 
                    'PC' => 'Private Car', 
                    'TC' => 'Tricycle', 
                    'CV' => 'Commercial Vehicle'
                ] as $key => $label)
                
                <!-- Card Component -->
                <div class="bg-[#161b22] border border-[#30363d] rounded-xl p-4 sm:p-5 shadow-lg transition duration-200 hover:border-gray-500">
                    
                    <!-- Card Header -->
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg sm:text-xl font-bold text-[#f0f6fc] tracking-tight">
                            {{ $key }}
                        </h3>
                        <span class="text-[10px] sm:text-xs font-semibold px-2 py-0.5 rounded bg-[#21262d] text-gray-400 border border-[#30363d] whitespace-nowrap">
                            {{ $label }}
                        </span>
                    </div>
                    
                    <!-- Card Body / Stats -->
                    <div class="space-y-3">
                        <!-- Available Row -->
                        <div class="flex justify-between items-center bg-[#238636]/10 p-3 rounded-lg border border-[#238636]/20">
                            <span class="text-xs sm:text-sm font-medium text-gray-400">Available</span>
                            <span class="text-lg sm:text-xl font-extrabold text-[#3fb950]">
                                {{ number_format($data[$key]['available']) }}
                            </span>
                        </div>
                        
                        <!-- Used Row -->
                        <div class="flex justify-between items-center bg-[#da3633]/10 p-3 rounded-lg border border-[#da3633]/20">
                            <span class="text-xs sm:text-sm font-medium text-gray-400">Used</span>
                            <span class="text-lg sm:text-xl font-extrabold text-[#f85149]">
                                {{ number_format($data[$key]['used']) }}
                            </span>
                        </div>
                    </div>

                </div>
                @endforeach

            </div>

        </div>
    </div>
</x-app-layout>