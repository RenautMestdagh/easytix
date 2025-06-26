document.addEventListener('DOMContentLoaded', async function() {
    try {
        // Try to use the module version first
        const { Html5Qrcode } = await import('html5-qrcode');
        initializeScanner(Html5Qrcode);
    } catch (error) {
        console.error('Error loading scanner module:', error);
        // Fallback to CDN if needed
        try {
            await loadScript('https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js');
            initializeScanner(window.Html5Qrcode);
        } catch (cdnError) {
            console.error('Error loading scanner from CDN:', cdnError);
            alert("Could not load the QR scanner. Please try again or contact support.");
        }
    }
});

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

// Main scanner initialization logic (reused for both module and CDN versions)
function initializeScanner(Html5QrcodeClass) {
    // Get DOM elements
    const qrReader = document.getElementById('qr-reader');
    const qrReaderResults = document.getElementById('qr-reader-results');
    const scanResultContent = document.getElementById('scan-result-content');
    const scanStatus = document.getElementById('scan-status');
    const scanAnotherBtn = document.getElementById('scan-another-btn');
    const manualScanForm = document.getElementById('manual-scan-form');

    // Initialize scanner
    const html5QrCode = new Html5QrcodeClass("qr-reader");
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
            body: JSON.stringify({ ticket_code: ticketCode })
        })
            .then(response => response.json())
            .then(data => {
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
