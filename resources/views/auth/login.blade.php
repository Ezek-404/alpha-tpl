<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<!-- Ginagamit na natin ang 'font-github' na nilagay natin sa tailwind.config.js -->
<body class="font-github antialiased bg-[#0d1117] overflow-hidden">
    <!-- Main wrapper -->
    <div class="h-screen w-screen flex flex-col justify-center items-center px-4">
        
        <!-- BAGONG LOGO: Pinalitan ang GitHub SVG ng iyong custom logo image -->
        <div class="mb-4">
            <img src="{{ asset('logo.png') }}" alt="ALPHA Logo" class="h-16 w-auto">
        </div>

        <!-- BAGONG PAMAGAT: Inupdate mula sa "Sign in to GitHub" -->
        <h1 class="text-[#f0f6fc] text-xl font-light mb-4 tracking-tight">Sign in to ALPHA CTPL</h1>

        <!-- Login Box Container -->
        <div class="w-full max-w-[340px] bg-[#161b22] border border-[#30363d] rounded-md p-5 shadow-md">
            
            <!-- Session Status Errors -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Username or Email Address -->
                <div>
                    <label for="email" class="block text-xs font-normal text-[#f0f6fc] mb-2">Username or email address</label>
                    <input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" 
                        class="block w-full px-3 py-1.5 bg-[#0d1117] border border-[#30363d] rounded-md text-[#f0f6fc] placeholder-[#8b949e] focus:border-[#58a6ff] focus:ring-1 focus:ring-[#58a6ff] text-sm outline-none transition duration-150" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs text-[#ff7b72]" />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <div class="flex justify-between items-center mb-2">
                        <label for="password" class="text-xs font-normal text-[#f0f6fc]">Password</label>
                    </div>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                        class="block w-full px-3 py-1.5 bg-[#0d1117] border border-[#30363d] rounded-md text-[#f0f6fc] placeholder-[#8b949e] focus:border-[#58a6ff] focus:ring-1 focus:ring-[#58a6ff] text-sm outline-none transition duration-150" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs text-[#ff7b72]" />
                </div>

                <!-- Remember Me -->
                <div class="block mt-4">
                    <label for="remember_me" class="inline-flex items-center cursor-pointer">
                        <input id="remember_me" type="checkbox" name="remember" class="rounded bg-[#0d1117] border-[#30363d] text-[#238636] focus:ring-0 focus:ring-offset-0 w-4 h-4">
                        <span class="ms-2 text-xs text-[#8b949e]">Remember me</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="mt-4">
                    <button type="submit" class="w-full flex justify-center py-1.5 px-3 border border-transparent rounded-md text-sm font-medium text-white bg-[#238636] hover:bg-[#2ea44f] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#238636] transition duration-150 cursor-pointer">
                        Sign in
                    </button>
                </div>
            </form>
        </div>

        <!-- Create Account Section outside the box (Inupdate din ang text) -->
        <div class="w-full max-w-[340px] mt-4 border border-[#30363d] rounded-md p-4 text-center text-xs text-[#f0f6fc]">
            New to ALPHA CTPL? 
            <a href="{{ route('register') }}" class="text-[#58a6ff] hover:underline">Create an account</a>.
        </div>
    </div>
</body>
</html>