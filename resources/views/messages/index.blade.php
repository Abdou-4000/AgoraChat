<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Direct Messages') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if($conversationUsers->count() > 0)
                        <div class="grid gap-4">
                            @foreach($conversationUsers as $user)
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div class="h-10 w-10 bg-indigo-500 rounded-full flex items-center justify-center">
                                                <span class="text-lg font-bold text-white">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <h3 class="font-semibold">{{ $user->name }}</h3>
                                                @if(isset($unreadCounts[$user->id]) && $unreadCounts[$user->id] > 0)
                                                    <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">
                                                        {{ $unreadCounts[$user->id] }} unread
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <a href="{{ route('messages.conversation', $user) }}" 
                                           class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest">
                                            Message
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-500 dark:text-gray-400 mb-4">No conversations yet.</p>
                            <p class="text-gray-500 dark:text-gray-400">Start a new conversation by visiting a user's profile.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>