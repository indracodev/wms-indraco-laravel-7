<!-- GLOBAL CAMERA BARCODE SCANNER MODAL -->
<div id="cameraModal" class="fixed inset-0 z-50 items-center justify-center p-4 bg-slate-950/85 backdrop-blur-md hidden" style="display: none;">
    <div class="glass-panel w-full max-w-md p-5 sm:p-6 rounded-2xl shadow-2xl space-y-3.5 border border-indigo-500/40 text-slate-100">
        <!-- Modal Header -->
        <div class="flex items-center justify-between border-b border-slate-700/80 pb-2.5">
            <div class="text-indigo-400">
                <h3 class="font-bold text-white text-base">Scan Barcode via Kamera</h3>
            </div>
            <button type="button" onclick="closeCameraScanner()" class="p-1.5 text-slate-400 hover:text-white rounded-lg hover:bg-slate-800 cursor-pointer">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Camera Switcher Dropdown (Tampil jika kamera lebih dari satu) -->
        <div id="camera-select-container" class="space-y-1" style="display: none;">
            <label for="camera-select" class="block text-[11px] font-semibold text-slate-300">Pilih Kamera:</label>
            <select id="camera-select" onchange="switchCamera(this.value)" class="w-full py-2 px-3 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:ring-2 focus:ring-indigo-500">
                <!-- Diisi secara dinamis oleh JavaScript -->
            </select>
        </div>

        <!-- Camera Viewfinder -->
        <div class="space-y-2">
            <p class="text-xs text-slate-300 text-center">Arahkan kamera ke kode barcode (BIN, Palet, Box, atau SKU Produk)</p>
            <div id="camera-reader-container" class="relative overflow-hidden rounded-2xl border-2 border-indigo-500/50 bg-slate-900 min-h-[220px] flex items-center justify-center">
                <div id="camera-reader" class="w-full"></div>
            </div>
            <div id="camera-status" class="text-[11px] text-center text-indigo-300 font-mono">Menyiapkan kamera...</div>
        </div>

        <!-- Actions -->
        <div class="pt-1 grid grid-cols-2 gap-2">
            <button type="button" onclick="promptManualInputFromCameraModal()" class="py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs rounded-xl shadow flex items-center justify-center cursor-pointer">
                <span>Input Code Manual</span>
            </button>
            <button type="button" onclick="closeCameraScanner()" class="py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs rounded-xl border border-slate-700 cursor-pointer">
                Batal / Tutup
            </button>
        </div>
    </div>
</div>

<!-- HTML5 QRCode Scanner CDN -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    let html5QrCode = null;
    let cameraTargetCallback = null;
    let availableCameras = [];
    let currentCameraId = null;

    const qrScanConfig = { 
        fps: 10, 
        qrbox: { width: 250, height: 150 },
        aspectRatio: 1.333334
    };

    function onScanSuccess(decodedText) {
        playBeepSound();
        const statusEl = document.getElementById('camera-status');
        if (statusEl) {
            statusEl.innerHTML = `<span class="text-emerald-400 font-bold">Terdeteksi: ${decodedText}</span>`;
        }

        if (typeof cameraTargetCallback === 'string') {
            const el = document.querySelector(`[name="${cameraTargetCallback}"]`) || document.getElementById(cameraTargetCallback);
            if (el) {
                el.value = decodedText;
                el.dispatchEvent(new Event('input', { bubbles: true }));
                el.dispatchEvent(new Event('change', { bubbles: true }));
            }
        } else if (typeof cameraTargetCallback === 'function') {
            cameraTargetCallback(decodedText);
        }

        setTimeout(() => {
            closeCameraScanner();
        }, 300);
    }

    function openCameraScanner(targetInputNameOrFn) {
        cameraTargetCallback = targetInputNameOrFn;
        const modal = document.getElementById('cameraModal');
        const statusEl = document.getElementById('camera-status');
        const selectContainer = document.getElementById('camera-select-container');
        const cameraSelect = document.getElementById('camera-select');
        modal.style.display = 'flex';

        if (!html5QrCode) {
            html5QrCode = new Html5Qrcode("camera-reader");
        }

        statusEl.innerText = "Mendeteksi daftar kamera perangkat...";

        // Ambil daftar kamera yang tersedia pada smartphone/perangkat
        Html5Qrcode.getCameras().then(devices => {
            if (devices && devices.length) {
                availableCameras = devices;
                cameraSelect.innerHTML = '';

                // Isi dropdown kamera
                devices.forEach((device, index) => {
                    const opt = document.createElement('option');
                    opt.value = device.id;
                    opt.text = device.label || `Kamera ${index + 1}`;
                    cameraSelect.appendChild(opt);
                });

                // Tampilkan dropdown jika ada lebih dari 1 kamera
                if (devices.length > 1) {
                    selectContainer.style.display = 'block';
                } else {
                    selectContainer.style.display = 'none';
                }

                // Prioritaskan kamera belakang (environment/back) secara cerdas
                let selectedDevice = devices.find(d => /back|belakang|rear|environment/i.test(d.label)) || devices[devices.length - 1];
                currentCameraId = selectedDevice.id;
                cameraSelect.value = currentCameraId;

                startScanning(currentCameraId);
            } else {
                // Fallback jika getCameras tidak mengembalikan list
                startScanningFallback();
            }
        }).catch(err => {
            console.warn("Gagal enumerasi getCameras(), menggunakan facingMode fallback:", err);
            startScanningFallback();
        });
    }

    function startScanning(cameraId) {
        const statusEl = document.getElementById('camera-status');
        statusEl.innerText = "Mengakses kamera...";

        html5QrCode.start(
            cameraId,
            qrScanConfig,
            onScanSuccess,
            (errorMessage) => { /* scanning */ }
        ).then(() => {
            statusEl.innerText = "Arahkan kamera ke barcode...";
        }).catch(err => {
            console.error(err);
            statusEl.innerHTML = `<span class="text-rose-400 font-semibold">Gagal membuka kamera yang dipilih.</span>`;
        });
    }

    function startScanningFallback() {
        const statusEl = document.getElementById('camera-status');
        statusEl.innerText = "Mengakses kamera...";

        html5QrCode.start(
            { facingMode: "environment" },
            qrScanConfig,
            onScanSuccess,
            (errorMessage) => { /* scanning */ }
        ).then(() => {
            statusEl.innerText = "Arahkan kamera ke barcode...";
        }).catch(err => {
            console.error(err);
            statusEl.innerHTML = `<span class="text-rose-400 font-semibold">Izin kamera diperlukan atau kamera tidak ditemukan.</span>`;
        });
    }

    function switchCamera(newCameraId) {
        if (!newCameraId || newCameraId === currentCameraId) return;
        const statusEl = document.getElementById('camera-status');
        statusEl.innerText = "Beralih kamera...";

        if (html5QrCode && html5QrCode.isScanning) {
            html5QrCode.stop().then(() => {
                currentCameraId = newCameraId;
                startScanning(newCameraId);
            }).catch(err => {
                console.error("Gagal menghentikan kamera sebelumnya:", err);
                currentCameraId = newCameraId;
                startScanning(newCameraId);
            });
        } else {
            currentCameraId = newCameraId;
            startScanning(newCameraId);
        }
    }

    function closeCameraScanner() {
        const modal = document.getElementById('cameraModal');
        if (html5QrCode && html5QrCode.isScanning) {
            html5QrCode.stop().then(() => {
                modal.style.display = 'none';
            }).catch(err => {
                modal.style.display = 'none';
            });
        } else {
            modal.style.display = 'none';
        }
    }

    function promptManualInputFromCameraModal() {
        const code = prompt("Masukkan Kode Barcode Manual (contoh: PLT-0001, B.10.2, PRD-99812):");
        if (code) {
            playBeepSound();
            if (typeof cameraTargetCallback === 'string') {
                const el = document.querySelector(`[name="${cameraTargetCallback}"]`) || document.getElementById(cameraTargetCallback);
                if (el) {
                    el.value = code;
                    el.dispatchEvent(new Event('input', { bubbles: true }));
                    el.dispatchEvent(new Event('change', { bubbles: true }));
                }
            } else if (typeof cameraTargetCallback === 'function') {
                cameraTargetCallback(code);
            }
            closeCameraScanner();
        }
    }

    function playBeepSound() {
        try {
            const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            osc.type = "sine";
            osc.frequency.value = 1200;
            gain.gain.setValueAtTime(0.1, audioCtx.currentTime);
            osc.start();
            osc.stop(audioCtx.currentTime + 0.12);
        } catch (e) {}
    }
</script>
