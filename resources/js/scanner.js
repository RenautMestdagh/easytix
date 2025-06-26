document.addEventListener('DOMContentLoaded', async function() {
    // DOM elements
    const eventSelect = document.getElementById('event-select');
    const eventSelectionSection = document.getElementById('event-selection-section');
    const startScanBtn = document.getElementById('start-scan-btn');
    const scannerSection = document.getElementById('scanner-section');
    const manualScanSection = document.getElementById('manual-scan-section');
    const switchModeBtn = document.getElementById('switch-mode-btn');
    const switchToScannerBtn = document.getElementById('switch-to-scanner-btn');
    const currentEventName = document.getElementById('current-event-name');
    const manualEventId = document.getElementById('manual-event-id');
    const eventSelectionError = document.getElementById('event-selection-error');

    const { Html5Qrcode } = await import('html5-qrcode');

    let html5QrCode = null;
    let currentEventId = null;
    let selectedEventName = '';

    startScanBtn.style.opacity = 0.5;
    startScanBtn.disabled = true;

    // Event selection handling
    eventSelect.addEventListener('change', function() {
        currentEventId = this.value;
        manualEventId.value = currentEventId;

        if (currentEventId) {
            startScanBtn.style.opacity = null;
            startScanBtn.disabled = false;
            eventSelectionError.classList.add('hidden');
            selectedEventName = eventSelect.options[eventSelect.selectedIndex].text;
        } else {
            document.getElementById('event-selection-error').classList.remove('hidden');
            startScanBtn.disabled = true;
        }
    });

    // Start scanner button
    startScanBtn.addEventListener('click', function() {
        if (!currentEventId) {
            eventSelectionError.classList.remove('hidden');
            return;
        }

        eventSelectionSection.classList.add('hidden');
        scannerSection.classList.remove('hidden');
        currentEventName.textContent = selectedEventName;
        initializeScanner();
    });

    // Switch between scanner and manual entry
    switchModeBtn.addEventListener('click', function() {
        scannerSection.classList.add('hidden');
        manualScanSection.classList.remove('hidden');
        stopScanner();
    });

    switchToScannerBtn.addEventListener('click', function() {
        manualScanSection.classList.add('hidden');
        scannerSection.classList.remove('hidden');
        initializeScanner();
    });

    function stopScanner() {
        if (html5QrCode && html5QrCode.isScanning) {
            html5QrCode.stop().then(() => {
                console.log("Scanner stopped successfully");
            }).catch(err => {
                console.error("Error stopping scanner:", err);
            });
        }
    }

    // Clean up scanner when navigating away
    function cleanupScanner() {
        if (html5QrCode) {
            try {
                if (html5QrCode.isScanning) {
                    html5QrCode.stop().then(() => {
                        console.log("Scanner stopped on navigation");
                    }).catch(err => {
                        console.error("Error stopping scanner on navigation:", err);
                    });
                }

                // Clear the scanner instance
                const clearResult = html5QrCode.clear();
                if (clearResult && typeof clearResult.catch === 'function') {
                    clearResult.catch(err => console.error("Error clearing scanner on navigation:", err));
                }
            } catch (err) {
                console.error("Error cleaning up scanner:", err);
            }
            html5QrCode = null;
        }
    }

    // Listen for Livewire navigation events
    document.addEventListener('livewire:navigating', cleanupScanner);

    // Listen for standard page unload events (fallback)
    window.addEventListener('beforeunload', cleanupScanner);
    window.addEventListener('pagehide', cleanupScanner);

    // Listen for visibility change (when tab becomes hidden)
    document.addEventListener('visibilitychange', function() {
        if (document.hidden && html5QrCode && html5QrCode.isScanning) {
            console.log("Page hidden, stopping scanner");
            stopScanner();
        }
    });

    async function initializeScanner() {
        // If scanner is already initialized and running, return
        if (html5QrCode && html5QrCode.isScanning) {
            return;
        }

        try {
            // Try to use the module version first
            setupScanner(Html5Qrcode);
        } catch (error) {
            console.error('Error loading scanner module:', error);
            // Fallback to CDN if needed
            try {
                await loadScript('https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js');
                setupScanner(window.Html5Qrcode);
            } catch (cdnError) {
                console.error('Error loading scanner from CDN:', cdnError);
                alert("Could not load the QR scanner. Please try again or contact support.");
            }
        }
    }

    // Helper function for CDN fallback
    function loadScript(src) {
        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = src;
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    // Main scanner setup logic
    function setupScanner(Html5QrcodeClass) {
        // Get DOM elements
        const qrReader = document.getElementById('qr-reader');
        const qrReaderResults = document.getElementById('qr-reader-results');
        const scanResultContent = document.getElementById('scan-result-content');
        const scanStatus = document.getElementById('scan-status');
        const scanAnotherBtn = document.getElementById('scan-another-btn');
        const manualScanForm = document.getElementById('manual-scan-form');

        // Clear previous scanner instance if exists - FIXED
        if (html5QrCode) {
            try {
                const clearResult = html5QrCode.clear();
                // Only call .catch() if clearResult is a Promise
                if (clearResult && typeof clearResult.catch === 'function') {
                    clearResult.catch(err => console.error("Error clearing previous scanner:", err));
                }
            } catch (err) {
                console.error("Error clearing previous scanner:", err);
            }
        }

        // Initialize scanner
        html5QrCode = new Html5QrcodeClass("qr-reader");
        const config = {
            fps: 10,
            qrbox: { width: 250, height: 250 }
        };

        // Success callback
        function onScanSuccess(decodedText, decodedResult) {
            html5QrCode.stop().then(() => {
                handleScanResult(decodedText);
            }).catch(err => {
                console.error("Error stopping scanner:", err);
            });
        }

        // Start scanner
        html5QrCode.start(
            { facingMode: "environment" },
            config,
            onScanSuccess
        ).catch(err => {
            console.error("Error starting scanner:", err);
            alert("Could not initialize the QR scanner. Please check camera permissions.");
        });

        // Handle scan result
        function handleScanResult(ticketCode) {
            scanResultContent.textContent = ticketCode;
            qrReader.classList.add('hidden');
            switchModeBtn.classList.remove('inline-flex');
            switchModeBtn.classList.add('hidden');
            qrReaderResults.classList.remove('hidden');
            scanStatus.textContent = "Processing...";
            scanStatus.className = "px-4 py-2 bg-blue-100 text-blue-800 rounded font-semibold";

            // Clear previous results
            const existingResult = document.getElementById('ticket-details');
            if (existingResult) existingResult.remove();

            // Send to server for validation
            fetch('/scan-ticket', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    ticket_code: ticketCode,
                    event_id: currentEventId
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        scanStatus.textContent = "❌ Ticket Not Found";
                        scanStatus.className = "px-4 py-2 bg-red-100 text-red-800 rounded font-semibold";

                        // Vibrate for error feedback
                        if (window.navigator.vibrate) {
                            window.navigator.vibrate([100,50,100,50,100]);
                        }
                        return;
                    }

                    const ticket = data.ticket;
                    let ticketInfo = `
                <div id="ticket-details" class="mt-4 p-4 rounded-lg border ${ticket.scanned ? 'bg-yellow-50 border-yellow-200' : 'bg-green-50 border-green-200'}">
                    <h3 class="font-bold text-lg ${ticket.scanned ? 'text-yellow-800' : 'text-green-800'}">
                        ${ticket.scanned ? '⚠️ Previously Scanned Ticket' : '✅ Valid Ticket'}
                    </h3>
                    <div class="mt-2 space-y-2 text-gray-700">
                        <p><span class="font-semibold">Event:</span> ${ticket.event.name}</p>
                        <p><span class="font-semibold">Date:</span> ${ticket.event.date}</p>
                        <p><span class="font-semibold">Organization:</span> ${ticket.event.organization}</p>
                        <p><span class="font-semibold">Ticket Type:</span> ${ticket.ticket_type.name} (${ticket.ticket_type.price})</p>
                    `;

                    if (ticket.scanned) {
                        ticketInfo += `
                        <div class="mt-3 pt-3 border-t border-yellow-200">
                            <p class="text-sm"><span class="font-semibold">Scanned at:</span> ${ticket.scanned_at}</p>
                            <p class="text-sm"><span class="font-semibold">Scanned by:</span> ${ticket.scanned_by}</p>
                        </div>
                    `;
                    }

                    ticketInfo += `</div></div>`;

                    scanStatus.style.display = "none";
                    scanResultContent.insertAdjacentHTML('afterend', ticketInfo);

                    // Vibrate on mobile for feedback
                    if (window.navigator.vibrate) {
                        window.navigator.vibrate(ticket.scanned ? [100,50,100] : 200);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    scanStatus.textContent = "❌ Error processing ticket";
                    scanStatus.className = "px-4 py-2 bg-red-100 text-red-800 rounded font-semibold";
                });
        }

        // Scan another ticket
        scanAnotherBtn.addEventListener('click', function() {
            qrReaderResults.classList.add('hidden');
            qrReader.classList.remove('hidden');
            switchModeBtn.classList.remove('hidden');
            switchModeBtn.classList.add('inline-flex');
            html5QrCode.start(
                { facingMode: "environment" },
                config,
                onScanSuccess
            ).catch(err => {
                console.error("Error restarting scanner:", err);
            });
        });

        // Manual form submission
        manualScanForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const ticketCode = document.getElementById('ticket_code').value.trim();
            if (ticketCode) {
                handleScanResult(ticketCode);
                document.getElementById('ticket_code').value = '';
            }
        });
    }
});
