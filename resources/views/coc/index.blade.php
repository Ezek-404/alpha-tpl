<x-app-layout>
    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Header Section inside Content -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-[#f0f6fc]">COC Management</h1>
                    <p class="text-xs sm:text-sm text-gray-400 mt-1">Manage and monitor Certificate of Cover allocations.</p>
                </div>
                <!-- Action Button -->
                <button class="bg-[#238636] hover:bg-[#2ea043] text-white px-4 py-2 rounded-lg text-sm font-semibold shadow transition duration-150">
                    + Add New COC
                </button>
            </div>

            <!-- Table Card Container -->
            <div class="bg-[#161b22] border border-[#30363d] rounded-xl shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-[#30363d] bg-[#161b22]">
                                <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-400">COC Number</th>
                                <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-400">Vehicle Type</th>
                                <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-400">Status</th>
                                <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-400">Date Added</th>
                                <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-400 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#30363d]">
                            @foreach($cocs as $coc)
                            <tr class="hover:bg-[#21262d]/50 transition duration-150">
                                <td class="px-6 py-4 text-sm font-medium text-[#f0f6fc]">
                                    {{ $coc['coc_no'] }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-300">
                                    {{ $coc['type'] }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if($coc['status'] === 'Available')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-[#238636]/10 text-[#3fb950] border border-[#238636]/20">
                                            Available
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-[#da3633]/10 text-[#f85149] border border-[#da3633]/20">
                                            Used
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-400">
                                    {{ $coc['date'] }}
                                </td>
                                <td class="px-6 py-4 text-sm text-right font-medium">
                                    <a href="#" class="text-[#58a6ff] hover:underline mr-3">Edit</a>
                                    <a href="#" class="text-[#f85149] hover:underline">Delete</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>