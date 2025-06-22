<x-layouts.app title="Ticket Scanner">
    <div class="container mx-auto px-4 pb-8">
        <div class="max-w-3xl mx-auto text-gray-800 dark:text-gray-100">
            <h1 class="text-2xl font-bold mb-6">Ticket Scanner</h1>

            <div class="shadow-md mb-6">
                <div id="qr-reader" class="mb-4 rounded-lg border"></div>
                <div id="qr-reader-results" class="hidden">
                    <div class="p-4 bg-gray-100 dark:bg-gray-700 rounded-lg mb-4">
                        <h3 class="font-semibold text-lg mb-2">Scan Result</h3>
                        <p id="scan-result-content" ></p>
                    </div>
                    <div class="flex justify-between">
                        <x-ui.button
                            id="scan-another-btn"
                            variant="secondary"
                        >
                            Scan Another Ticket
                        </x-ui.button>
                        <div id="scan-status" class="px-4 py-2 rounded font-semibold"></div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Manual Entry</h2>
                <form id="manual-scan-form" class="flex gap-2">
                    @csrf
                    <input type="text" name="ticket_code" id="ticket_code"
                           class="flex-1 px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Enter ticket code manually">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors duration-300 ease-in-out">
                        Submit
                    </button>
                </form>
                <div id="manual-scan-result" class="mt-4 hidden"></div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const qrReader = document.getElementById('qr-reader');
            const qrReaderResults = document.getElementById('qr-reader-results');
            const scanResultContent = document.getElementById('scan-result-content');
            const scanStatus = document.getElementById('scan-status');
            const scanAnotherBtn = document.getElementById('scan-another-btn');
            const manualScanForm = document.getElementById('manual-scan-form');
            const manualScanResult = document.getElementById('manual-scan-result');

            // Initialize QR scanner
            const html5QrCode = new Html5Qrcode("qr-reader");
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

            // Handle scan result (both QR and manual)
            function handleScanResult(ticketCode) {
                scanResultContent.textContent = ticketCode;
                qrReader.classList.add('hidden');
                qrReaderResults.classList.remove('hidden');

                // Send to server for validation
                fetch('/scan-ticket', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ ticket_code: ticketCode })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            scanStatus.textContent = "✅ Valid Ticket";
                            scanStatus.className = "px-4 py-2 bg-green-100 text-green-800 rounded font-semibold";

                            // Show ticket info (you can customize this part)
                            const ticketInfo = `
                        <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                            <h3 class="font-bold text-lg text-green-800">Ticket Scanned Successfully</h3>
                            <p>Event: ${data.ticket.ticketType.event.name}</p>
                            <p>Ticket Type: ${data.ticket.ticketType.name}</p>
                            <p class="text-sm text-gray-500 mt-2">Scanned by: ${data.scanned_by}</p>
                        </div>
                    `;
                            scanResultContent.insertAdjacentHTML('afterend', ticketInfo);
                        } else {
                            scanStatus.textContent = `❌ ${data.message}`;
                            scanStatus.className = "px-4 py-2 bg-red-100 text-red-800 rounded font-semibold";
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
        });
    </script>

</x-layouts.app>
