<div>
    {{-- order summary --}}
    <div class="bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg p-8 mb-8">
        <h1 class="mb-6 text-2xl">Order summary</h1>
        <div>
            @foreach($orderTickets as $orderTicket)
                <div class="border-b dark:border-gray-700 last:border-b-gray-800 dark:last:border-b-gray-200">
                    <div class="flex flex-wrap items-center mb-2 mt-2 text-gray-800 dark:text-gray-200">
                        <h3 class="font-bold text-lg w-full sm:w-auto sm:flex-1">
                            <span class="font-bold tabular-nums">{{ $orderTicket->amount }}</span>
                            <span class="mx-2">x</span>
                            {{ $orderTicket->name }}
                        </h3>

                        <div class="flex items-center justify-between w-full sm:w-auto mt-2 sm:mt-0">
                            <p class="text-gray-500 dark:text-gray-400 sm:mr-6 md:mr-12 lg:mr-24">
                                €{{ number_format($orderTicket->price_cents / 100, 2) }}
                            </p>

                            <div class="flex items-center">
                            <span class="px-4 py-2 text-gray-800 dark:text-gray-200 tabular-nums">
                                €{{ number_format($orderTicket->price_cents * $orderTicket->amount / 100, 2)  }}
                            </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            <div class="flex flex-wrap items-center mb-2 mt-2 text-gray-800 dark:text-gray-200">
                <h3 class="font-bold text-lg w-full sm:w-auto sm:flex-1">
                    Total Amount
                </h3>

                <div class="flex items-center justify-between w-full sm:w-auto mt-2 sm:mt-0">
                    <div class="flex items-center">
                        <span class="px-4 py-2 text-gray-800 dark:text-gray-200 tabular-nums font-bold">
                            €{{ number_format($orderTotal / 100, 2) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg p-8">
        <h1 class="mb-6 text-2xl">Personal Information</h1>
        <p class="text-sm text-gray-500 mb-4">* Indicates required field</p>
        <div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <x-ui.forms.group label="First name *" for="first_name" error="first_name">
                    <x-ui.forms.input
                        name="first_name"
                        id="first_name"
                        wire:model.blur="first_name"
                        autocomplete="given-name"
                        required
                    />
                </x-ui.forms.group>

                <x-ui.forms.group label="Last name *" for="last_name" error="last_name">
                    <x-ui.forms.input
                        name="last_name"
                        id="last_name"
                        wire:model.blur="last_name"
                        autocomplete="family-name"
                        required
                    />
                </x-ui.forms.group>
            </div>

            <x-ui.forms.group label="Email *" for="email" error="email" class="mb-4">
                <x-ui.forms.input
                    type="email"
                    name="email"
                    id="email"
                    wire:model.blur="email"
                    autocomplete="email"
                    required
                />
            </x-ui.forms.group>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <x-ui.forms.group label="Phone number *" for="phone" error="phone">
                    <x-ui.forms.input
                        type="tel"
                        name="phone"
                        id="phone"
                        wire:model.blur="phone"
                        autocomplete="tel"
                        required
                    />
                </x-ui.forms.group>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <x-ui.forms.group label="Date of birth" for="date_of_birth" error="date_of_birth" class="mb-4">
                    <x-ui.forms.input
                        type="date"
                        name="date_of_birth"
                        id="date_of_birth"
                        wire:model.blur="date_of_birth"
                        autocomplete="bday"
                    />
                </x-ui.forms.group>
                <x-ui.forms.group label="Gender" for="gender" class="mb-4">
                    <x-ui.forms.select name="gender" id="gender" wire:model.blur="gender" class="w-full">
                        <option value="">Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                        <option value="prefer not to say">Prefer not to say</option>
                    </x-ui.forms.select>
                </x-ui.forms.group>
            </div>

            <x-ui.forms.group label="Address" for="address" error="address" class="mb-4">
                <x-ui.forms.input
                    name="address"
                    id="address"
                    wire:model.blur="address"
                    autocomplete="address-line1"
                />
            </x-ui.forms.group>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <x-ui.forms.group label="City" for="city" error="city">
                    <x-ui.forms.input
                        name="city"
                        id="city"
                        wire:model.blur="city"
                        autocomplete="address-level2"
                    />
                </x-ui.forms.group>

                <x-ui.forms.group label="Country" for="country" error="country">
                    <x-ui.forms.select name="country" id="country" wire:model.blur="country" class="w-full">
                        <option value="">Select an option</option>
                        <!-- Country options remain the same -->
                    </x-ui.forms.select>
                </x-ui.forms.group>
            </div>

            <div class="flex justify-between">
                <div class="mt-6">
                    <x-ui.button
                        variant="secondary"
                        wire:click="backToTickets"
                    >
                        Back to tickets
                    </x-ui.button>
                </div>

                <div class="mt-6">
                    <x-ui.button
                        variant="primary"
                        wire:click="proceedToPayment"
                    >
                        Proceed to payment
                    </x-ui.button>
                </div>
            </div>
        </div>
    </div>
</div>
