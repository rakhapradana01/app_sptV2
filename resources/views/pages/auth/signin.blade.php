@extends('layouts.fullscreen-layout')

@section('title', 'Masuk')

@section('content')
<div class="min-h-screen w-full flex flex-col lg:flex-row bg-white font-inter text-[#1F2937] overflow-x-hidden selection:bg-[#C8A45D]/20 selection:text-[#142B4A]">

    {{-- ============================================================ --}}
    {{-- PANEL KIRI — IDENTITAS INSTANSI (Desktop: 54% Layar)          --}}
    {{-- ============================================================ --}}
    <div class="relative hidden lg:flex lg:w-[54%] xl:w-[55%] flex-col justify-between bg-[#102544] text-white p-10 xl:p-16 overflow-hidden border-r border-[#C8A45D]/20">
        
        {{-- Elemen Visual Pendukung: Pola Geometris Abstrak & Garis Arsitektural Halus --}}
        <div class="pointer-events-none absolute inset-0 -z-0 overflow-hidden opacity-[0.06]">
            <svg class="h-full w-full" viewBox="0 0 800 800" fill="none" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="gov-grid" width="48" height="48" patternUnits="userSpaceOnUse">
                        <path d="M 48 0 L 0 0 0 48" fill="none" stroke="#FFFFFF" stroke-width="0.8" />
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#gov-grid)" />
                {{-- Garis Arsitektural Dinamis --}}
                <circle cx="400" cy="400" r="280" stroke="#C8A45D" stroke-width="1.5" stroke-dasharray="6 6" />
                <circle cx="400" cy="400" r="360" stroke="#FFFFFF" stroke-width="1" />
                <path d="M 0 800 L 800 0" stroke="#C8A45D" stroke-width="1" />
                <path d="M 0 400 L 800 400" stroke="#FFFFFF" stroke-width="0.7" />
            </svg>
        </div>

        {{-- Aksen Pancaran Cahaya Sangat Lembut di Sudut --}}
        <div class="pointer-events-none absolute -top-40 -left-40 h-96 w-96 rounded-full bg-[#142B4A] opacity-60 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-32 -right-32 h-80 w-80 rounded-full bg-[#C8A45D]/10 blur-3xl"></div>

        {{-- BAGIAN ATAS: Logo Resmi & Hierarki Instansi --}}
        <div class="relative z-10">
            <div class="flex items-start gap-4">
                <div class="shrink-0 p-2 rounded-xl bg-white/5 border border-white/10 backdrop-blur-xs">
                    <img src="{{ asset('images/logo.png') }}" 
                         alt="Lambang Pemerintah Provinsi Kalimantan Selatan" 
                         class="h-16 w-auto object-contain filter drop-shadow-md" />
                </div>
                <div class="pt-1">
                    <p class="text-xs xl:text-[13px] font-semibold tracking-widest text-[#C8A45D] uppercase">
                        Pemerintah Provinsi Kalimantan Selatan
                    </p>
                    <p class="text-sm xl:text-base font-medium tracking-wide text-white/95 uppercase mt-0.5">
                        Badan Pengelola Keuangan dan Aset Daerah
                    </p>
                    {{-- Garis Aksen Emas Tipis --}}
                    <div class="w-12 h-[2px] bg-[#C8A45D] mt-3 rounded-full"></div>
                </div>
            </div>
        </div>

        {{-- BAGIAN TENGAH: Nama Aplikasi & Deskripsi Platform --}}
        <div class="relative z-10 my-auto py-12 max-w-xl">
            {{-- Micro Badge Resmi --}}
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/5 border border-white/10 text-xs text-gray-300 font-medium mb-6">
                <span class="w-1.5 h-1.5 rounded-full bg-[#C8A45D]"></span>
                <span class="tracking-wide">Portal Administrasi Resmi</span>
            </div>

            <h1 class="text-3xl sm:text-4xl xl:text-5xl font-bold text-white tracking-tight leading-[1.18]">
                SISTEM INFORMASI<br>
                <span class="text-white/90 font-light">PERJALANAN DINAS</span>
            </h1>

            <p class="mt-6 text-sm xl:text-base text-gray-300/90 leading-relaxed max-w-lg font-normal">
                Platform terintegrasi untuk pengelolaan administrasi perjalanan dinas yang tertib, efektif, dan akuntabel.
            </p>
        </div>

        {{-- BAGIAN BAWAH: Footer Identitas --}}
        <div class="relative z-10 pt-6 border-t border-white/10">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between text-xs text-gray-400 gap-2">
                <span class="font-medium text-gray-300">BPKAD Provinsi Kalimantan Selatan</span>
                <span class="text-gray-400">&copy; {{ date('Y') }} Hak Cipta Dilindungi</span>
            </div>
        </div>
    </div>


    {{-- ============================================================ --}}
    {{-- PANEL KANAN — FORM LOGIN (Desktop: 46% Layar, Mobile: Full)  --}}
    {{-- ============================================================ --}}
    <div class="w-full lg:w-[46%] xl:w-[45%] flex flex-col justify-between min-h-screen bg-[#F7F8FA] lg:bg-white p-6 sm:p-10 lg:p-12 xl:p-16">
        
        {{-- Spacer atas untuk menjaga keseimbangan layout --}}
        <div class="hidden lg:block"></div>

        {{-- Container Form (Maksimal 380–420px Sesuai Spesifikasi) --}}
        <div class="w-full max-w-[400px] mx-auto my-auto py-6">

            {{-- HEADER MOBILE: Ditampilkan hanya di perangkat layar kecil (< lg) --}}
            <div class="lg:hidden mb-8 text-center flex flex-col items-center">
                <div class="w-16 h-16 mb-3 p-1.5 rounded-2xl bg-white shadow-sm border border-[#E5E7EB] flex items-center justify-center">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo Pemprov Kalsel" class="h-12 w-auto object-contain" />
                </div>
                <span class="text-[11px] font-bold tracking-wider text-[#C8A45D] uppercase">
                    Pemerintah Provinsi Kalimantan Selatan
                </span>
                <span class="text-xs font-semibold text-[#142B4A] uppercase mt-0.5">
                    BPKAD &bull; Sistem Informasi Perjalanan Dinas
                </span>
                <div class="w-10 h-[1.5px] bg-[#C8A45D] my-3 rounded-full"></div>
            </div>

            {{-- HEADER FORM: Selamat Datang --}}
            <div class="mb-7">
                <h2 class="text-2xl sm:text-3xl font-bold text-[#142B4A] tracking-tight">
                    Selamat Datang
                </h2>
                <p class="text-sm text-[#6B7280] mt-1.5">
                    Masuk ke akun Anda untuk melanjutkan.
                </p>
                {{-- Aksen Garis Emas Tipis --}}
                <div class="w-8 h-[2px] bg-[#C8A45D] mt-3.5 rounded-full"></div>
            </div>

            {{-- NOTIFIKASI ERROR AUTENTIKASI / SESSION --}}
            @if (session()->has('error'))
                <div class="mb-6 rounded-lg bg-red-50 border border-red-200 p-3.5 flex items-start gap-3 text-sm text-red-800 transition-all">
                    <svg class="w-5 h-5 text-red-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div class="flex-1 text-xs sm:text-sm font-medium leading-snug">
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            @if (session()->has('success'))
                <div class="mb-6 rounded-lg bg-emerald-50 border border-emerald-200 p-3.5 flex items-start gap-3 text-sm text-emerald-800 transition-all">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <div class="flex-1 text-xs sm:text-sm font-medium leading-snug">
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            {{-- FORM LOGIN RESMI --}}
            <form action="{{ route('login.authenticated') }}" method="POST" 
                  x-data="{ isSubmitting: false }" 
                  @submit="isSubmitting = true" 
                  class="space-y-5">
                @csrf

                {{-- FIELD: USERNAME --}}
                <div>
                    <label for="username" class="block text-xs font-semibold uppercase tracking-wider text-[#1F2937] mb-2">
                        Username <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" 
                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <input type="text" 
                               id="username" 
                               name="username" 
                               value="{{ old('username') }}" 
                               required 
                               autofocus
                               placeholder="Masukkan username Anda"
                               autocomplete="username"
                               class="w-full h-12 pl-11 pr-4 rounded-lg border {{ $errors->has('username') ? 'border-red-400 bg-red-50/20' : 'border-[#E5E7EB] bg-white' }} text-sm text-[#1F2937] placeholder-[#9CA3AF] transition-all duration-150 focus:outline-none focus:border-[#142B4A] focus:ring-2 focus:ring-[#142B4A]/10 shadow-xs" />
                    </div>

                    @if ($errors->has('username'))
                        <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <span>{{ $errors->first('username') }}</span>
                        </p>
                    @endif
                </div>

                {{-- FIELD: PASSWORD DENGAN TOGGLE LIHAT/SEMBUNYIKAN --}}
                <div>
                    <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-[#1F2937] mb-2">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative" x-data="{ showPassword: false }">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" 
                                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input :type="showPassword ? 'text' : 'password'" 
                               id="password" 
                               name="password" 
                               required
                               placeholder="Masukkan password Anda"
                               autocomplete="current-password"
                               class="w-full h-12 pl-11 pr-11 rounded-lg border {{ $errors->has('password') ? 'border-red-400 bg-red-50/20' : 'border-[#E5E7EB] bg-white' }} text-sm text-[#1F2937] placeholder-[#9CA3AF] transition-all duration-150 focus:outline-none focus:border-[#142B4A] focus:ring-2 focus:ring-[#142B4A]/10 shadow-xs" />
                        
                        <button type="button" 
                                @click="showPassword = !showPassword"
                                tabindex="-1"
                                :title="showPassword ? 'Sembunyikan password' : 'Lihat password'"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none transition-colors">
                            {{-- Ikon Mata Terbuka --}}
                            <svg x-show="!showPassword" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" 
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" 
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            {{-- Ikon Mata Tertutup --}}
                            <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" 
                                      d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                            </svg>
                        </button>
                    </div>

                    @if ($errors->has('password'))
                        <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <span>{{ $errors->first('password') }}</span>
                        </p>
                    @endif
                </div>

                {{-- TOMBOL MASUK --}}
                <div class="pt-2">
                    <button type="submit"
                            :disabled="isSubmitting"
                            :class="isSubmitting ? 'opacity-80 cursor-wait' : 'hover:bg-[#0D1B2A] active:scale-[0.99]'"
                            class="w-full h-12 bg-[#142B4A] text-white text-sm font-semibold tracking-wider uppercase rounded-lg shadow-sm hover:shadow transition-all duration-150 flex items-center justify-center gap-2 focus:outline-none focus:ring-2 focus:ring-[#142B4A] focus:ring-offset-2">
                        <span x-show="!isSubmitting" class="flex items-center gap-2">
                            <span>MASUK</span>
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </span>
                        <span x-show="isSubmitting" x-cloak class="flex items-center gap-2">
                            <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Memproses...</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>

        {{-- FOOTER MOBILE (< lg) --}}
        <div class="lg:hidden text-center pt-6 pb-2 text-xs text-[#6B7280]">
            &copy; {{ date('Y') }} BPKAD Provinsi Kalimantan Selatan
        </div>

        {{-- Spacer bawah desktop --}}
        <div class="hidden lg:block"></div>
    </div>

</div>
@endsection