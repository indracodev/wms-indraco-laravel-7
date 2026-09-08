@extends('layouts.app')

@section('title', 'Pengaturan Sistem')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold font-display text-white">Pengaturan WMS Indraco</h2>
            <p class="text-sm text-slate-400">Konfigurasi Aturan Bisnis Gudang & Parameter Sistem</p>
        </div>
    </div>

    <div class="glass-panel p-6 rounded-2xl shadow-xl space-y-6">
        <div class="border-b border-slate-700/60 pb-4">
            <h3 class="font-bold text-white text-base">Aturan Bisnis Gudang (Warehouse Business Rules)</h3>
            <p class="text-xs text-slate-400 mt-1">Konfigurasi batasan fisik lokasi dan kapasitas penyimpanan</p>
        </div>

        <div class="space-y-4 text-xs">
            <div class="p-4 bg-slate-800/60 rounded-xl border border-slate-700 flex items-center justify-between">
                <div>
                    <div class="font-bold text-white text-sm">Maksimal Palet per Slot BIN</div>
                    <div class="text-slate-400 mt-0.5">Aturan ketat kapasitas maksimal palet yang diizinkan berada pada 1 BIN slot</div>
                </div>
                <div class="font-bold font-mono text-amber-400 text-lg bg-slate-900 px-3 py-1 rounded border border-slate-700">
                    2 PALET / BIN
                </div>
            </div>

            <div class="p-4 bg-slate-800/60 rounded-xl border border-slate-700 flex items-center justify-between">
                <div>
                    <div class="font-bold text-white text-sm">Mode Transaksi Database Lock</div>
                    <div class="text-slate-400 mt-0.5">Penguncian baris DB (lockForUpdate) untuk mencegah race condition pada akses BIN bersamaan</div>
                </div>
                <div class="font-bold font-mono text-emerald-400 text-sm bg-slate-900 px-3 py-1 rounded border border-slate-700">
                    STRICT ENABLED
                </div>
            </div>

            <div class="p-4 bg-slate-800/60 rounded-xl border border-slate-700 flex items-center justify-between">
                <div>
                    <div class="font-bold text-white text-sm">Audit Observer System</div>
                    <div class="text-slate-400 mt-0.5">Pencatatan riwayat audit otomatis setiap perubahan data Eloquent</div>
                </div>
                <div class="font-bold font-mono text-indigo-400 text-sm bg-slate-900 px-3 py-1 rounded border border-slate-700">
                    ACTIVE
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
