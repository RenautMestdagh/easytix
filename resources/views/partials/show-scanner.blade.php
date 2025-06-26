<x-layouts.app title="Ticket Scanner">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="container mx-auto px-2 pb-4">
        <div class="max-w-md mx-auto text-gray-800 dark:text-gray-100">
            <h1 class="text-2xl font-bold mb-4 px-2">Ticket Scanner</h1>

            <div class="mb-4">
                <div id="qr-reader" class="mb-2 rounded-lg overflow-hidden"></div>
                <div id="qr-reader-results" class="hidden px-2">
                    <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-lg mb-3">
                        <h3 class="font-semibold text-lg mb-1">Scan Result</h3>
                        <p id="scan-result-content" class="font-mono text-sm break-all"></p>
                    </div>
                    <div id="scan-status" class="px-3 py-2 mb-3 rounded font-semibold text-center"></div>
                    <x-ui.button
                        id="scan-another-btn"
                        variant="primary"
                        class="w-full py-3 text-lg"
                    >
                        Scan Another Ticket
                    </x-ui.button>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4">
                <h2 class="text-xl font-semibold mb-3">Manual Entry</h2>
                <form id="manual-scan-form" class="flex flex-col gap-2">
                    @csrf
                    <input type="text" name="ticket_code" id="ticket_code"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-lg"
                           placeholder="Enter ticket code"
                           autocomplete="off"
                           autocorrect="off"
                           autocapitalize="off"
                           spellcheck="false">
                    <button type="submit" class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-lg">
                        Submit
                    </button>
                </form>
            </div>
        </div>
    </div>

    @vite(['resources/js/scanner.js'])

    <style>
        #qr-reader {
            width: 100% !important;
        }

        #qr-reader video {
            width: 100% !important;
            height: auto !important;
        }

        #qr-reader__dashboard {
            padding: 0.5rem !important;
        }

        #qr-reader__dashboard_section_swaplink {
            display: none !important;
        }

        @media (max-width: 640px) {
            .container {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }

            #qr-reader__dashboard button {
                font-size: 0.9rem !important;
                padding: 0.4rem 0.6rem !important;
            }
        }
    </style>
</x-layouts.app>
