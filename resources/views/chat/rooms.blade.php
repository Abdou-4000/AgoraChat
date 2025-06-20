<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $room->name }}
            @if($room->type === 'public')
                <span class="text-sm text-yellow-600 dark:text-yellow-400 ml-2">💰 1 credit per message</span>
            @endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Chat Area -->
                <div class="lg:col-span-3">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <!-- Messages Container -->
                        <div id="messages" class="h-96 overflow-y-auto p-6 space-y-3">
                            @foreach($messages as $message)
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="h-8 w-8 bg-gray-300 rounded-full flex items-center justify-center">
                                            <span class="text-xs font-bold text-gray-600">
                                                {{ strtoupper(substr($message->user->name, 0, 1)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <div class="bg-gray-100 dark:bg-gray-700 rounded-lg px-4 py-2">
                                            <div class="font-semibold text-sm text-gray-900 dark:text-gray-100">
                                                {{ $message->user->name }}
                                            </div>
                                            <div class="text-gray-800 dark:text-gray-200">
                                                {{ $message->content }}
                                            </div>
                                            <div class="text-xs text-gray-500 mt-1">
                                                {{ $message->created_at->format('M j, g:i A') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Message Input -->
                        <div class="border-t border-gray-200 dark:border-gray-700 p-6">
                            @if($room->type === 'public' && auth()->user()->credits < 1)
                                <div class="bg-red-50 dark:bg-red-900 border border-red-200 dark:border-red-700 rounded-lg p-4">
                                    <p class="text-red-800 dark:text-red-200">
                                        ❌ Need credits to message here. 
                                        <a href="{{ route('debates.index') }}" class="underline hover:no-underline">
                                            Vote on debates to earn credits!
                                        </a>
                                    </p>
                                </div>
                            @else
                                <form id="message-form" class="flex space-x-3">
                                    @csrf
                                    <input type="text" 
                                           id="message-input" 
                                           class="flex-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" 
                                           placeholder="Type your message..." 
                                           maxlength="500">
                                    <button type="submit" 
                                            class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                        Send
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="font-semibold text-lg text-gray-900 dark:text-gray-100 mb-4">Quick Actions</h3>
                        <div class="space-y-3">
                            <a href="{{ route('chat.index') }}" 
                               class="block bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 p-3 rounded-lg text-gray-800 dark:text-gray-200 transition">
                                📋 All Rooms
                            </a>
                            <a href="{{ route('debates.index') }}" 
                               class="block bg-green-100 dark:bg-green-800 hover:bg-green-200 dark:hover:bg-green-700 p-3 rounded-lg text-gray-800 dark:text-gray-200 transition">
                                🗳️ Vote & Earn Credits
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('message-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const input = document.getElementById('message-input');
        const message = input.value.trim();
        
        if (message) {
            fetch(`{{ route('chat.message', $room) }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ content: message })
            })
            .then(response => response.json())
            .then(data => {
                input.value = '';
                // Message will appear via WebSocket
            });
        }
    });
    </script>
</x-app-layout>