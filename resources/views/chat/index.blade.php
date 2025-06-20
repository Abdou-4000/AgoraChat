<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Chat Rooms') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
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
                                            <span class="text-sm text-gray-500">
                                                Created by {{ $room->creator->name }}
                                            </span>
                                        </div>
                                    </div>
                                    <div>
                                        @if($room->type === 'elite' && auth()->user()->tier !== 'elite')
                                            <span class="text-gray-400 text-sm">🔒 Elite Required</span>
                                        @else
                                            <a href="{{ route('chat.show', $room) }}" 
                                               class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                                Join Chat
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <p class="text-gray-500 dark:text-gray-400">No chat rooms available yet.</p>
                                @if(auth()->user()->canCreatePublicRoom())
                                    <p class="text-sm text-gray-400 mt-2">Be the first to create one!</p>
                                @endif
                            </div>
                        @endforelse
                    </div>

                    @if(auth()->user()->canCreatePublicRoom())
                        <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
                            <h4 class="text-lg font-semibold mb-4">Create New Room</h4>
                            <form action="{{ route('chat.store') }}" method="POST" class="flex gap-4">
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
                                        class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    Create
                                </button>
            </form>
                        </div>
                    @else
                        <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6 text-center">
                            <p class="text-gray-500 dark:text-gray-400">
                                🏆 Reach <strong>Contributor</strong> tier (50+ credits) to create rooms! 
                                <a href="{{ route('debates.index') }}" class="text-blue-600 hover:text-blue-500 underline">
                                    Vote on debates to earn credits.
                                </a>
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>