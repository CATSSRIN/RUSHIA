<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>RUSHIA - Login</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('icon/icon.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-[#F8FAFC] min-h-screen flex items-center justify-center p-4 md:p-6 lg:p-8">
        <!-- Main Card Container -->
        <div class="w-full max-w-5xl bg-white rounded-3xl shadow-[0_15px_50px_rgba(76,75,99,0.08)] border border-gray-100/70 overflow-hidden flex flex-col md:flex-row min-h-[600px]">
            
            <!-- Left Side: Product Intro (Teal / Mint Gradient) -->
            <div class="w-full md:w-[45%] bg-gradient-to-br from-[#248370] to-[#1a6152] p-8 md:p-12 text-white flex flex-col justify-center relative overflow-hidden shrink-0">
                <!-- Abstract Circles Background Decor -->
                <div class="absolute -left-10 -bottom-10 w-48 h-48 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/5 rounded-full blur-xl pointer-events-none"></div>
                <div class="absolute left-12 top-24 w-20 h-20 border border-white/10 rounded-full pointer-events-none"></div>
                <div class="absolute right-12 top-1/3 w-3 h-3 bg-[#62C3AF]/40 rounded-full pointer-events-none"></div>

                <!-- Intro Description -->
                <div class="relative z-10 my-auto py-12 space-y-6">
                    <div class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold bg-[#62C3AF]/20 text-[#98C3AF] border border-[#62C3AF]/30 tracking-wider uppercase">
                        Middleware System
                    </div>
                    <h2 class="text-3xl font-extrabold text-white tracking-tight leading-tight">
                        Smart Inventory Starts Here.
                    </h2>
                    <p class="text-[13px] text-[#E2F2EF]/90 font-medium leading-relaxed">
                        <strong class="text-white block mb-1">RUSHIA (Rapid Upload & Smart Handling for Inventory Automation)</strong>
                        adalah sistem middleware berbasis web yang mengotomatisasi manajemen inventaris dengan mempercepat ekstraksi data spreadsheet ke database, menerbitkan dokumen operasional (PO, DO, Invoice) secara otomatis melalui pencocokan data vendor, serta menyediakan fitur audit trail untuk keamanan data.
                    </p>
                </div>

            </div>

            <!-- Right Side: Login Form -->
            <div class="w-full md:w-[55%] p-8 md:p-12 lg:p-16 flex flex-col justify-center bg-white">
                
                <!-- Logo & Heading -->
                <div class="mb-8 text-center md:text-left">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-[#248370]/10 border border-[#248370]/10 mb-4">
                        <svg class="w-6 h-6 text-[#248370]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-950">Hello! Welcome back</h3>
                    <p class="text-sm text-gray-400 mt-1 font-medium">Please sign in to continue</p>
                </div>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <!-- Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1.5">{{ __('Email Address') }}</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                            </span>
                            <input 
                                id="email" 
                                class="block w-full pl-10 pr-4 py-3 text-sm text-gray-800 border border-gray-200 rounded-xl focus:ring-4 focus:ring-[#62C3AF]/20 focus:border-[#248370] focus:outline-none transition duration-150" 
                                type="email" 
                                name="email" 
                                :value="old('email')" 
                                placeholder="name@domain.com"
                                required 
                                autofocus 
                                autocomplete="username" 
                            />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                    </div>

                    <!-- Password -->
                    <div x-data="{ show: false }">
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-xs font-bold text-gray-700 uppercase tracking-wide">{{ __('Password') }}</label>
                            @if (Route::has('password.request'))
                                <a class="text-xs font-bold text-[#248370] hover:text-[#1a6152] transition focus:outline-none" href="{{ route('password.request') }}">
                                    {{ __('Forgot Password?') }}
                                </a>
                            @endif
                        </div>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </span>
                            <input 
                                id="password" 
                                class="block w-full pl-10 pr-10 py-3 text-sm text-gray-800 border border-gray-200 rounded-xl focus:ring-4 focus:ring-[#62C3AF]/20 focus:border-[#248370] focus:outline-none transition duration-150"
                                :type="show ? 'text' : 'password'"
                                name="password"
                                placeholder="••••••••"
                                required 
                                autocomplete="current-password" 
                            />
                            <button 
                                type="button" 
                                @click="show = !show" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="!show">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="show" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input id="remember_me" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-[#248370] focus:ring-[#62C3AF]" name="remember">
                        <label for="remember_me" class="ms-2 text-xs font-bold text-gray-500 cursor-pointer uppercase tracking-wider">{{ __('Remember me') }}</label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full py-3 px-4 bg-[#248370] hover:bg-[#1a6152] text-white text-sm font-bold rounded-xl transition duration-150 shadow-md shadow-[#248370]/15 flex items-center justify-center gap-2 focus:outline-none focus:ring-4 focus:ring-[#62C3AF]/30">
                        {{ __('Login') }}
                    </button>

                </form>
            </div>
        </div>
    </body>
</html>
