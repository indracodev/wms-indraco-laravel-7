<?php

$file = file_get_contents('resources/views/transactions/inbound.blade.php');

// Let's check where x-data starts
$pos = strpos($file, 'inboundV2Workflow');
echo "inboundV2Workflow found at char $pos\n";

// Let's test if there is any JS error in browser:
// What does openMobilePalletModal() do?
// Look at:
// openMobilePalletModal() {
//     this.showMobilePalletModal = true;
//     this.mobileSearchQuery = '';
//     this.mobileFilterType = '';
//     this.searchPalletsMobile();
//     this.$nextTick(() => {
//         const input = document.getElementById('mobile_pallet_search_input');
//         if (input) input.focus();
//     });
// },

// Look at the trigger button in Step 1:
// <div class="md:hidden pt-1.5">
//     <label class="block text-[11px] font-semibold text-slate-400 mb-1">Atau Cari & Pilih Palet Terdaftar:</label>
//     <button type="button" 
//             @click="openMobilePalletModal()" 
// ...
