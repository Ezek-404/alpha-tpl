<x-app-layout>
    <div class="py-6 sm:py-12">
        <!-- Responsive Container: dynamic padding sa mobile vs desktop -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            <!-- Profile Information Card -->
            <div class="p-4 sm:p-8 bg-[#161b22] border border-[#30363d] shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- Update Password Card (Kung meron man sa file mo) -->
            @if(view()->exists('profile.partials.update-password-form'))
            <div class="p-4 sm:p-8 bg-[#161b22] border border-[#30363d] shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
            @endif

            <!-- Delete User Card -->
            <div class="p-4 sm:p-8 bg-[#161b22] border border-[#30363d] shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>