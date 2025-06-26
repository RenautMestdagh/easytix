<x-layouts.app title="Ticket Scanner">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="container mx-auto px-2 pb-4">
        <div class="max-w-xl mx-auto text-gray-800 dark:text-gray-100">
            <h1 class="text-2xl font-bold mb-4 px-2">Ticket Scanner</h1>

            <div id="event-selection-section" class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 mb-4">
                <x-ui.forms.group label="Select Event">
                    <x-ui.forms.select
                        class="rounded-xl shadow-md focus:ring-2 focus:ring-indigo-500 px-5 w-full"
                        id="event-select"
                    >
                        <option value="">-- Select an Event --</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}">{{ $event->name }} - {{ $event->date->format('M j, Y') }}</option>
                        @endforeach
                    </x-ui.forms.select>
                    <div id="event-selection-error" class="text-red-500 text-sm hidden">Please select an event first</div>
                </x-ui.forms.group>

                <div class="flex gap-2 mt-4">
                    <x-ui.button
                        id="start-scan-btn"
                        variant="primary"
                        class="flex-1 py-3"
                    >
                        Start Scanner
                    </x-ui.button>
                </div>
            </div>

            <div id="scanner-section" class="hidden">
                <div class="flex justify-between items-center mb-5">
                    <h2 class="text-lg font-semibold">Scanning: <span id="current-event-name"></span></h2>
                </div>
                <div id="qr-reader" class="mb-2 rounded-lg overflow-hidden"></div>
                <x-ui.button
                    id="switch-mode-btn"
                    variant="secondary"
                    class="w-full mt-5"
                    size="sm"
                >
                    Switch to Manual Entry
                </x-ui.button>
                <div id="qr-reader-results" class="hidden flex px-2 flex-col gap-5">
                    <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-lg">
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

            <div id="manual-scan-section" class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 hidden">
                <div class="flex justify-between items-center mb-3">
                    <h2 class="text-xl font-semibold">Manual Entry</h2>
                    <x-ui.button
                        id="switch-to-scanner-btn"
                        variant="secondary"
                        size="sm"
                    >
                        Switch to Scanner
                    </x-ui.button>
                </div>
                <form id="manual-scan-form" class="flex flex-col gap-2">
                    @csrf
                    <input type="hidden" name="event_id" id="manual-event-id">
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
