<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-github antialiased bg-[#0d1117] overflow-hidden">
    <!-- Pinaka-wrapper na sakto sa viewport (100vh / h-screen) para walang scrollbar -->
    <div class="h-screen w-screen flex flex-col justify-center items-center px-4">
        
        <!-- GitHub Logo Icon -->
        <div class="mb-4 text-[#f0f6fc]">
            <svg height="44" aria-hidden="true" viewBox="0 0 16 16" version="1.1" width="44" data-view-component="true" class="fill-current">
                <path d="M8 0c4.42 0 8 3.58 8 8a8.013 8.013 0 0 1-5.45 7.59c-.4.08-.55-.17-.55-.38 0-.27.01-1.13.01-2.2 0-.75-.25-1.23-.54-1.48 1.78-.2 3.65-.88 3.65-3.95 0-.88-.31-1.59-.82-2.15.08-.2.36-1.02-.08-2.12 0 0-.67-.22-2.2.82A7.48 7.48 0 0 0 8 2.84c-.68.006-1.36.09-2 .27-1.53-1.03-2.2-.82-2.2-.82-.44 1.1-.16 1.92-.08 2.12-.51.56-.82 1.28-.82 2.15 0 3.06 1.86 3.75 3.64 3.95-.23.2-.44.55-.51 1.07-.46.21-1.61.55-2.33-.66-.15-.24-.6-.83-1.23-.82-.67.01-.27.38.01.53.34.19.73.9.82 1.13.16.45.68 1.35 3.12.88.01.47.01.84.01.93 0 .22-.15.47-.55.38A8.006 8.006 0 0 1 0 8c0-4.42 3.58-8 8-8Z"></path>
            </svg>
        </div>

        <h1 class="text-[#f0f6fc] text-xl font-light mb-4 tracking-tight">Sign in to GitHub-TPL</h1>

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
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-[11px] text-[#58a6ff] hover:underline">
                                Forgot password?
                            </a>
                        @endif
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

        <!-- Create Account Section outside the box -->
        <div class="w-full max-w-[340px] mt-4 border border-[#30363d] rounded-md p-4 text-center text-xs text-[#f0f6fc]">
            New to GitHub-TPL? 
            <a href="{{ route('register') }}" class="text-[#58a6ff] hover:underline">Create an account</a>.
        </div>
    </div>
</body>
</html>