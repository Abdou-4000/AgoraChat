<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center">
            <a href="{{ route('messages.index') }}" class="mr-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
            </a>
            <span>{{ __('Conversation with') }} {{ $otherUser->name }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <!-- Messages Container -->
                <div id="messages-container" class="h-96 overflow-y-auto p-6 space-y-4">
                    @foreach($messages as $message)
                        <div class="flex @if($message->sender_id == auth()->id()) justify-end @endif" data-message-id="{{ $message->id }}">
                            <div class="max-w-[70%] @if($message->sender_id == auth()->id()) bg-indigo-100 dark:bg-indigo-900 @else bg-gray-100 dark:bg-gray-700 @endif rounded-lg px-4 py-2">
                                <div class="text-sm @if($message->sender_id == auth()->id()) text-indigo-800 dark:text-indigo-200 @else text-gray-800 dark:text-gray-200 @endif">
                                    {{ $message->content }}
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $message->created_at->format('M j, g:i A') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Message Input -->
                <div class="border-t border-gray-200 dark:border-gray-700 p-4">
                    <form id="message-form" class="flex space-x-3">
                        @csrf
                        <input type="text" id="message-input" class="flex-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" placeholder="Type your message..." maxlength="500">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 dark:bg-indigo-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            Send
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {

        if (window.Pusher) {
            window.Pusher.logToConsole = true;
        }
        const userId = {{ auth()->id() }};
        const receiverId = {{ $otherUser->id }};
        const messagesContainer = document.getElementById('messages-container');
        const messageForm = document.getElementById('message-form');
        const messageInput = document.getElementById('message-input');
        
        if (!window.Echo || !window.Pusher) {
            console.error('Echo or Pusher is not properly loaded');
            return;
        }

        setInterval(function() {
            if (window.Echo && window.Echo.socketId()) {
                console.log('Current socket ID:', window.Echo.socketId());
            } else {
                console.log('No socket ID available yet');
            }
        }, 5000);

        if (typeof Pusher === 'undefined') {
            console.error('Pusher is not loaded!');
            return;
        }
        

        // Function to get the highest message ID currently displayed
        function getLastMessageId() {
            const messages = messagesContainer.querySelectorAll('[data-message-id]');
            if (messages.length === 0) return 0;
            
            let maxId = 0;
            messages.forEach(msg => {
                const id = parseInt(msg.dataset.messageId || 0);
                if (id > maxId) maxId = id;
            });
            return maxId;
        }

        const statusIndicator = document.createElement('div');
        statusIndicator.className = 'fixed bottom-4 right-4 px-3 py-1 rounded-full text-xs font-medium z-50 transition-opacity duration-300';
        statusIndicator.innerHTML = 'Connecting to WebSocket...';
        statusIndicator.style.backgroundColor = '#FEF3C7'; // Yellow
        statusIndicator.style.color = '#92400E';
        document.body.appendChild(statusIndicator);
        
        // Connection status handling
        window.Echo.connector.pusher.connection.bind('connecting', function() {
            console.log('WebSocket connecting...');
            statusIndicator.style.opacity = '1';
            statusIndicator.style.backgroundColor = '#FEF3C7'; // Yellow
            statusIndicator.style.color = '#92400E';
            statusIndicator.innerHTML = 'Connecting...';
        });
        
        window.Echo.connector.pusher.connection.bind('connected', function() {
            console.log('WebSocket connected!');
            statusIndicator.style.backgroundColor = '#DEF7EC'; // Green
            statusIndicator.style.color = '#057A55';
            statusIndicator.innerHTML = 'Connected';
            setTimeout(() => { statusIndicator.style.opacity = '0'; }, 3000);
        });
        
        window.Echo.connector.pusher.connection.bind('disconnected', function() {
            console.log('WebSocket disconnected!');
            statusIndicator.style.opacity = '1';
            statusIndicator.style.backgroundColor = '#FDE8E8'; // Red
            statusIndicator.style.color = '#E02424';
            statusIndicator.innerHTML = 'Disconnected - Trying to reconnect...';
        });

        window.Echo.connector.pusher.connection.bind('error', function(error) {
            console.error('Pusher connection error:', error);
            statusIndicator.style.opacity = '1';
            statusIndicator.style.backgroundColor = '#FDE8E8';
            statusIndicator.innerHTML = `Connection Error: ${error.data?.code || 'Unknown'}`;
        });

        // Log all state changes
        window.Echo.connector.pusher.connection.bind('state_change', function(states) {
            console.log('Pusher state change:', states.previous, '->', states.current);
        });
        
        // Scroll to bottom of messages container
        function scrollToBottom() {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
        
        // Scroll on initial load
        scrollToBottom();
        
        messageInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                messageForm.dispatchEvent(new Event('submit'));

            }
        });

        // Submit message form
        messageForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (e.target.tagName.toLowerCase() === 'form') {
                e.stopPropagation();
                //messageForm.dispatchEvent(new Event('submit'));

            }

            const message = messageInput.value.trim();
            
            if (message) {
                // Immediately add the message to UI (optimistic update)
                const tempId = 'temp-' + Date.now();
                const messageElement = document.createElement('div');
                messageElement.id = tempId;
                messageElement.classList.add('flex', 'justify-end');
                messageElement.innerHTML = `
                    <div class="max-w-[70%] bg-indigo-100 dark:bg-indigo-900 rounded-lg px-4 py-2">
                        <div class="text-sm text-indigo-800 dark:text-indigo-200">
                            ${message}
                        </div>
                        <div class="text-xs text-gray-500 mt-1 flex items-center justify-end">
                            <span>Just now</span>
                            <span class="ml-2 sending-indicator">
                                <svg class="animate-spin h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                        </div>
                    </div>
                `;
                
                messagesContainer.appendChild(messageElement);
                setTimeout(scrollToBottom, 50);
                messageInput.value = '';
                
                // Send the message
                fetch(`{{ route('messages.send', $otherUser) }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Socket-ID': window.Echo.socketId()  // Add socket ID to prevent echo
                    },
                    body: JSON.stringify({ content: message, temp_id: tempId })
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Message sent successfully:', data);
                    
                    // Update temp message with real ID
                    const messageElement = document.getElementById(tempId);
                    if (messageElement) {
                        messageElement.dataset.messageId = data.id;
                        
                        // Replace spinner with checkmark
                        const indicator = messageElement.querySelector('.sending-indicator');
                        if (indicator) {
                            indicator.innerHTML = `<svg class="h-3 w-3 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>`;
                        }
                    }
                })
                .catch(error => {
                    console.error('Error sending message:', error);
                    
                    // Show error indicator
                    const indicator = document.querySelector(`#${tempId} .sending-indicator`);
                    if (indicator) {
                        indicator.innerHTML = `<svg class="h-3 w-3 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>`;
                    }
                });
            }
        });
        
        // WebSocket listener for incoming messages
        console.log(`Attempting to subscribe to channel: private-user.${userId}`);
        window.Echo.private(`user.${userId}`)

            .listen('.DirectMessageSent', (e) => {
                console.log('WebSocket message received(dotted):', e);
                
                // Only process messages from the current conversation partner
                if (e.sender_id == receiverId) {
                    // Check if we already have this message
                    if (!document.querySelector(`[data-message-id="${e.id}"]`)) {
                        const messageElement = document.createElement('div');
                        messageElement.classList.add('flex');
                        messageElement.dataset.messageId = e.id;
                        
                        messageElement.innerHTML = `
                            <div class="max-w-[70%] bg-gray-100 dark:bg-gray-700 rounded-lg px-4 py-2">
                                <div class="text-sm text-gray-800 dark:text-gray-200">
                                    ${e.content}
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    ${e.created_at || 'Just now'}
                                </div>
                            </div>
                        `;
                        
                        messagesContainer.appendChild(messageElement);
                        setTimeout(scrollToBottom, 50);
                    }
                }
            });
            window.Echo.connector.pusher.connection.bind('message', function(data) {
                console.log('Raw Pusher message received:', data);
            });
        // Poll for latest messages (fallback if WebSockets fail)
        
    });
    </script>
</x-app-layout>