<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl ...">{{ __('Software Checklist') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-end mb-4">
                <a href="{{ route('software-checklist.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    Add Checklist Item
                </a>
            </div>

            <div class="space-y-6">
                @forelse ($checklistItems as $programName => $semesters)
                    <!-- Alpine.js Component Wrapper: x-data defines the component's state -->
                    <div x-data="{ open: false }" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        
                        <!-- Clickable Header to toggle the 'open' state -->
                        <div @click="open = !open" class="p-4 bg-gray-50 border-b cursor-pointer hover:bg-gray-100 flex justify-between items-center">
                            <h3 class="text-xl font-bold text-gray-800">{{ $programName }}</h3>
                            <!-- Chevron icon that rotates based on the 'open' state -->
                            <svg class="w-6 h-6 transform transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>

                        <!-- Collapsible Content: x-show makes this div visible only when 'open' is true -->
                        <div x-show="open" x-cloak class="p-6 space-y-6 border-t" style="display: none;">
                            @foreach ($semesters as $yearAndSem => $items)
                                <div>
                                    <h4 class="font-semibold text-lg">{{ $yearAndSem }}</h4>
                                    <ul class="mt-2 list-disc list-inside space-y-1">
                                        @foreach ($items as $item)
                                            <li class="flex justify-between items-center">
                                            <span>
                                                <strong>{{ $item->software_name }}</strong> {{ $item->version }} 
                                                @if($item->notes) <em class="text-gray-500 text-sm">- {{ $item->notes }}</em> @endif
                                            </span>
                                            <span class="text-xs"> 
                                                (Last updated by {{ $item->user->name }})
                                                <a href="{{ route('software-checklist.edit', $item->id) }}" class="text-indigo-600 hover:text-indigo-900 ml-4">Edit</a>
                                                <form action="{{ route('software-checklist.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900 ml-2">Delete</button>
                                                </form>
                                            </span>
                                        </li>   
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500">No checklist items have been created yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>