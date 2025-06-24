<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a2 2 0 01-2-2v-6a2 2 0 012-2h10" />
            </svg>
            {{ __('Chat Rooms') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- Search Box -->
                    <div class="mb-6">
                        <form method="GET" action="{{ route('chat.index') }}" class="flex space-x-2">
                            <input type="text" name="search" placeholder="Search rooms..." value="{{ request('search') }}" 
                                   class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 dark:bg-indigo-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                Search
                            </button>
                        </form>
                    </div>

                    <!-- Room Type Filter -->
                    <div class="mb-6 flex flex-wrap gap-2">
                        <a href="{{ route('chat.index') }}" class="text-sm {{ !request('type') ? 'bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200' : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200' }} px-3 py-1 rounded-full">
                            All Rooms
                        </a>
                        <a href="{{ route('chat.index', ['type' => 'public']) }}" class="text-sm {{ request('type') === 'public' ? 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200' : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200' }} px-3 py-1 rounded-full">
                            Public Rooms
                        </a>
                        @if(auth()->user()->tier === 'elite')
                            <a href="{{ route('chat.index', ['type' => 'elite']) }}" class="text-sm {{ request('type') === 'elite' ? 'bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200' : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200' }} px-3 py-1 rounded-full">
                                Elite Rooms
                            </a>
                        @endif
                    </div>

                    <!-- Room Listings -->
                    <div class="grid gap-6">
                        @forelse($rooms as $room)
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="text-lg font-semibold">{{ $room->name }}</h3>
                                        <div class="flex items-center space-x-2 mt-2">
                                            @if($room->type === 'public')
                                                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                                                    Public (1 credit/message)
                                                </span>
                                            @elseif($room->type === 'elite')
                                                <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-purple-900 dark:text-purple-300">
                                                    Elite Only
                                                </span>
                                            @else
                                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">
                                                    Private
                                                </span>
                                            @endif
                                            <span class="text-sm text-gray-500 dark:text-gray-400 flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                                Created by {{ $room->creator->name }}
                                            </span>
                                            <span class="text-sm text-gray-500 dark:text-gray-400 flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ $room->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                    </div>
                                    <div>
                                        @if($room->type === 'elite' && auth()->user()->tier !== 'elite')
                                            <div class="flex flex-col items-end">
                                                <span class="text-gray-400 text-sm mb-1">🔒 Elite Required</span>
                                                <a href="{{ route('debates.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                    Earn more credits →
                                                </a>
                                            </div>
                                        @else
                                            <a href="{{ route('chat.show', $room) }}" 
                                               class="inline-flex items-center px-4 py-2 bg-indigo-600 dark:bg-indigo-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                                Join Chat
                                            </a>
                                        @endif                                        
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                <p class="text-gray-500 dark:text-gray-400">No chat rooms available yet.</p>
                                @if(auth()->user()->canCreatePublicRoom())
                                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-2">Be the first to create one!</p>
                                @endif
                            </div>
                        @endforelse
                    </div>

                    <!-- Room Creation Form -->
                    @if(auth()->user()->canCreatePublicRoom())
                        <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
                            <h4 class="text-lg font-semibold mb-4 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Create New Room
                            </h4>
                            <form action="{{ route('chat.store') }}" method="POST" class="flex flex-col sm:flex-row gap-4">
                                @csrf
                                <input type="text" 
                                       name="name" 
                                       placeholder="Room name..." 
                                       class="flex-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" 
                                       required>
                                <select name="type" 
                                        class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="public">Public (1 credit/msg)</option>
                                    @if(auth()->user()->tier === 'elite')
                                        <option value="elite">Elite Only</option>
                                    @endif
                                </select>
                                <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 bg-indigo-600 dark:bg-indigo-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    Create Room
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6 text-center">
                            <div class="bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                                <p class="text-yellow-800 dark:text-yellow-200">
                                    🏆 Reach <strong>Contributor</strong> tier (50+ credits) to create rooms! 
                                    <a href="{{ route('debates.index') }}" class="text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300 underline">
                                        Vote on debates to earn credits.
                                    </a>
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>