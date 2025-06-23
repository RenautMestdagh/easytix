@if(auth()->check() && session()->has('original_user_id'))
    <div class="z-50 bg-orange-400 bg-opacity-100 dark:bg-yellow-600 text-black dark:text-white p-2 shadow-md w-full px-2">
        <div class="container mx-auto flex items-center justify-between gap-2">
            <div class="flex items-center space-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9z" clip-rule="evenodd" />
                </svg>
                <span>
                You are currently signed in as {{ auth()->user()->name }}.
            </span>
            </div>
            <form method="POST" action="{{ route('login-as.use') }}">
                @csrf
                <button
                    type="submit"
                    class="px-3 py-1 bg-white dark:bg-zinc-800 text-black dark:text-white rounded hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors duration-300 ease-in-out flex items-center space-x-1 hover:cursor-pointer"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>Switch Back</span>
                </button>
            </form>
        </div>
    </div>
@endif
