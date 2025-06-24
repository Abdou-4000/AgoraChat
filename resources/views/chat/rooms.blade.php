<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center">
            <a href="{{ route('chat.index') }}" class="mr-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
            </a>
            {{ $room->name }}
            @if($room->type === 'public')
                <span class="text-sm text-yellow-600 dark:text-yellow-400 ml-2">💰 1 credit per message</span>
            @elseif($room->type === 'elite')
                <span class="text-sm text-purple-500 dark:text-purple-400 ml-2">✨ Elite Room</span>
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
                            @forelse($messages as $message)
                                <div class="flex items-start space-x-3 @if($message->user_id === auth()->id()) justify-end @endif">
                                    @if($message->user_id !== auth()->id())
                                        <div class="flex-shrink-0">
                                            <div class="h-8 w-8 bg-indigo-500 rounded-full flex items-center justify-center">
                                                <span class="text-xs font-bold text-white">
                                                    {{ strtoupper(substr($message->user->name, 0, 1)) }}
                                                </span>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="flex-1 @if($message->user_id === auth()->id()) max-w-[80%] @else max-w-[90%] @endif">
                                        <div class="@if($message->user_id === auth()->id()) bg-indigo-100 dark:bg-indigo-900 ml-auto @else bg-gray-100 dark:bg-gray-700 @endif rounded-lg px-4 py-2">
                                        @if($message->user_id === auth()->id())
                                            <div class="font-semibold text-sm text-indigo-800 dark:text-indigo-200 text-right mb-1">
                                                You
                                            </div>
                                        @else
                                            <div class="font-semibold text-sm text-gray-900 dark:text-gray-100 flex items-center">
                                                {{ $message->user->name }}
                                                <a href="{{ route('messages.conversation', $message->user) }}" 
                                                class="ml-2 text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                                    </svg>
                                                    Message
                                                </a>
                                            </div>
                                        @endif                                            <div class="@if($message->user_id === auth()->id()) text-indigo-800 dark:text-indigo-200 @else text-gray-800 dark:text-gray-200 @endif">
                                                {{ $message->content }}
                                            </div>
                                            <div class="text-xs text-gray-500 mt-1 @if($message->user_id === auth()->id()) text-right @endif">
                                                {{ $message->created_at->format('M j, g:i A') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8">
                                    <p class="text-gray-500 dark:text-gray-400">No messages yet. Be the first to start the conversation!</p>
                                </div>
                            @endforelse
                        </div>

                        <!-- Message Input -->
                        <div class="border-t border-gray-200 dark:border-gray-700 p-6">
                            @if($room->type === 'public' && auth()->user()->credits < 1)
                                <div class="bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-800 rounded-lg p-4">
                                    <p class="text-red-800 dark:text-red-200 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Need credits to message here. 
                                        <a href="{{ route('debates.index') }}" class="ml-1 underline hover:no-underline">
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
                                            class="inline-flex items-center px-4 py-2 bg-indigo-600 dark:bg-indigo-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                        </svg>
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
                        <!-- Room Info -->
                        <div class="mb-6">
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-gray-100 mb-2">Room Info</h3>
                            <div class="text-sm text-gray-700 dark:text-gray-300 space-y-2">
                                <p><span class="font-medium">Created by:</span> {{ $room->creator->name }}</p>
                                <p><span class="font-medium">Created:</span> {{ $room->created_at->format('M j, Y') }}</p>
                                <p>
                                    <span class="font-medium">Type:</span> 
                                    @if($room->type === 'public')
                                        <span class="text-blue-600 dark:text-blue-400">Public</span>
                                    @elseif($room->type === 'elite')
                                        <span class="text-purple-600 dark:text-purple-400">Elite</span>
                                    @else
                                        <span class="text-green-600 dark:text-green-400">Private</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- Active Users -->
                        <h3 class="font-semibold text-lg text-gray-900 dark:text-gray-100 mb-4 mt-6">Recent Conversations</h3>
                        <div class="space-y-3 mb-6 max-h-48 overflow-y-auto">
                            @foreach(auth()->user()->recentConversations() as $user)
                                <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-700 rounded-lg p-2">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 bg-indigo-500 rounded-full flex items-center justify-center mr-3">
                                            <span class="text-xs font-bold text-white">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <span class="text-sm font-medium">{{ $user->name }}</span>
                                            @if(isset($unreadCounts[$user->id]) && $unreadCounts[$user->id] > 0)
                                                <span class="ml-1 bg-red-100 text-red-800 text-xs font-medium px-1.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">
                                                    {{ $unreadCounts[$user->id] }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <a href="{{ route('messages.conversation', $user) }}" 
                                    class="text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                        </svg>
                                        Message
                                    </a>
                                </div>
                            @endforeach

                            @if(count(auth()->user()->recentConversations()) === 0)
                                <div class="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <p class="text-sm text-gray-500">No recent conversations</p>
                                </div>
                            @endif
                        </div>

                        <!-- Quick Actions -->
                        <h3 class="font-semibold text-lg text-gray-900 dark:text-gray-100 mb-4">Quick Actions</h3>
                        <div class="space-y-2">
                            <a href="{{ route('chat.index') }}" class="flex items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                                All Rooms
                            </a>
                            <a href="{{ route('messages.index') }}" class="flex items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                </svg>
                                Direct Messages
                            </a>
                            <a href="{{ route('debates.index') }}" class="flex items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a2 2 0 01-2-2v-6a2 2 0 012-2h10" />
                                </svg>
                                Earn Credits
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get DOM elements
        const messagesContainer = document.getElementById('messages');
        const messageForm = document.getElementById('message-form');
        const messageInput = document.getElementById('message-input');
        
        // Function to scroll to bottom of messages
        function scrollToBottom() {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
        
        // Scroll to bottom on initial load
        scrollToBottom();
        
        // Listen for form submissions
        messageForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const message = messageInput.value.trim();
            
            if (message) {
                // Immediately add message to UI (optimistic update)
                const tempId = 'temp-' + Date.now();
                const messageDiv = document.createElement('div');
                messageDiv.id = tempId;
                messageDiv.className = 'flex items-start space-x-3 justify-end';
                messageDiv.innerHTML = `
                    <div class="flex-1 max-w-[80%]">
                        <div class="bg-indigo-100 dark:bg-indigo-900 ml-auto rounded-lg px-4 py-2">
                        <div class="font-semibold text-sm text-indigo-800 dark:text-indigo-200 text-right mb-1">
                            You
                        </div>
                            <div class="text-indigo-800 dark:text-indigo-200">
                                ${message}
                            </div>
                            <div class="text-xs text-gray-500 mt-1 text-right flex items-center justify-end">
                                <span>Just now</span>
                                <span class="ml-2 sending-indicator">
                                    <svg class="animate-spin h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </div>
                `;
                
                messagesContainer.appendChild(messageDiv);
                scrollToBottom();
                messageInput.value = '';
                
                // Actually send the message
                fetch(`{{ route('chat.message', $room) }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Socket-ID': window.Echo.socketId()
                    },
                    body: JSON.stringify({ content: message })
                })
                .then(response => response.json())
                .then(data => {
                    // Replace the spinning indicator with a checkmark
                    const indicator = document.querySelector(`#${tempId} .sending-indicator`);
                    if (indicator) {
                        indicator.innerHTML = `<svg class="h-3 w-3 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>`;
                    }
                })
                .catch(error => {
                    // Show error indicator
                    const indicator = document.querySelector(`#${tempId} .sending-indicator`);
                    if (indicator) {
                        indicator.innerHTML = `<svg class="h-3 w-3 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>`;
                    }
                });
            }
        });
        window.Echo.connector.pusher.connection.bind('message', function(data) {
            console.log('Raw Pusher message for chat room:', data);
        });
        // Listen for new messages via WebSockets
        window.Echo.channel(`chat-room.{{ $room->id }}`)
            .listen('MessageSent', (e) => {
                console.log('Chat message received:', e);
                
                try {
                    // Skip messages from ourselves (we already show them via optimistic UI)
                    if (e.user_id === {{ auth()->id() }}) {
                        return;
                    }
                    
                    // Create message element for other users' messages
                    const messageDiv = document.createElement('div');
                    messageDiv.className = 'flex items-start space-x-3';
                    messageDiv.dataset.messageId = e.id || '';
                    
                    messageDiv.innerHTML = `
                        <div class="flex-shrink-0">
                            <div class="h-8 w-8 bg-indigo-500 rounded-full flex items-center justify-center">
                                <span class="text-xs font-bold text-white">
                                    ${e.user_name ? e.user_name.charAt(0).toUpperCase() : '?'}
                                </span>
                            </div>
                        </div>
                        <div class="flex-1 max-w-[90%]">
                            <div class="bg-gray-100 dark:bg-gray-700 rounded-lg px-4 py-2">
                                <div class="font-semibold text-sm text-gray-900 dark:text-gray-100 flex items-center">
                                    ${e.user_name || 'User'}
                                    <a href="/messages/${e.user_id}" 
                                    class="ml-2 text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                        </svg>
                                        Message
                                    </a>
                                </div>
                                <div class="text-gray-800 dark:text-gray-200">
                                    ${e.content || ''}
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    ${e.created_at || 'Just now'}
                                </div>
                            </div>
                        </div>
                    `;
                    
                    messagesContainer.appendChild(messageDiv);
                    scrollToBottom();
                } catch (error) {
                    console.error('Error processing message:', error);
                }
            });
    });
    </script>
</x-app-layout>