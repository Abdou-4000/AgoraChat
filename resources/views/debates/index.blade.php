<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            🗳️ {{ __('Debates - Earn Credits!') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="grid gap-6">
                        @forelse($debates as $debate)
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                <h3 class="text-lg font-bold mb-4">{{ $debate->title }}</h3>
                                
                                <div class="grid md:grid-cols-2 gap-4 mb-4">
                                    <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg">
                                        <div class="font-semibold text-blue-800 dark:text-blue-200 mb-2">Side A</div>
                                        <p class="text-blue-700 dark:text-blue-300">{{ $debate->side_a }}</p>
                                    </div>
                                    <div class="bg-red-50 dark:bg-red-900 p-4 rounded-lg">
                                        <div class="font-semibold text-red-800 dark:text-red-200 mb-2">Side B</div>
                                        <p class="text-red-700 dark:text-red-300">{{ $debate->side_b }}</p>
                                    </div>
                                </div>
                                
                                @php $votes = $debate->getVoteCounts(); @endphp
                                <div class="mb-4">
                                    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-2">
                                        <span>Side A: {{ $votes['a'] }} votes</span>
                                        <span>Side B: {{ $votes['b'] }} votes</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-600">
                                        @php 
                                            $total = $votes['a'] + $votes['b'];
                                            $percentA = $total > 0 ? ($votes['a'] / $total) * 100 : 50;
                                        @endphp
                                        <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $percentA }}%"></div>
                                    </div>
                                </div>
                                
                                @if(!$debate->votes()->where('user_id', auth()->id())->exists())
                                    <form method="POST" action="{{ route('debates.vote', $debate) }}" class="flex gap-2">
                                        @csrf
                                        <button name="vote" value="a" 
                                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">
                                            Vote Side A (+2 credits)
                                        </button>
                                        <button name="vote" value="b" 
                                                class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition">
                                            Vote Side B (+2 credits)
                                        </button>
                                    </form>
                                @else
                                    <div class="bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-200 p-3 rounded-lg text-center">
                                        ✅ You voted! Thanks for participating.
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <p class="text-gray-500 dark:text-gray-400">No active debates yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>