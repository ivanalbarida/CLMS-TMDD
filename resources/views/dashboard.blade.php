<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ openModal: false, modalTitle: '', modalContent: '' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Top Row: Key Metric Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">

                <a href="{{ route('equipment.index') }}" class="block hover:shadow-lg transition">
                    <div class="bg-white p-6 rounded-lg shadow h-full">
                        <h4 class="text-gray-500 text-sm font-medium">Total Equipment</h4>
                        <p class="text-3xl font-bold text-gray-700">{{ $stats['total_equipment'] }}</p>
                    </div>
                </a>
                
                <!-- For Repair Card -->
                <a href="{{ route('equipment.index', ['status' => 'For Repair']) }}" class="block hover:shadow-lg transition">
                    <div class="bg-white p-6 rounded-lg shadow h-full">
                        <h4 class="text-gray-500 text-sm font-medium">For Repair</h4>
                        <p class="text-3xl font-bold text-yellow-600">{{ $stats['for_repair'] }}</p>
                    </div>
                </a>

                <!-- Pending PM Today Card -->
                <a href="{{ route('pm-checklist.index') }}" class="block hover:shadow-lg transition">
                    <div class="bg-white p-6 rounded-lg shadow h-full">
                        <h4 class="text-gray-500 text-sm font-medium">Today's Checklist Items</h4>
                        <p class="text-3xl font-bold text-blue-600">{{ $stats['pending_pm'] }}</p>
                    </div>
                </a>

                <!-- Open Service Requests Card (no link yet) -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h4 class="text-gray-500 text-sm font-medium">Open Service Requests</h4>
                    <p class="text-3xl font-bold text-gray-400">N/A</p>
                </div>

            </div>

            <!-- Bottom Section: Main Columns -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Left Column (Wider) -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Open Corrective Maintenance Widget -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <div class="flex justify-between items-center border-b pb-2 mb-4">
                                <h3 class="text-lg font-semibold">Open Corrective Maintenance</h3>
                                <a href="{{ route('maintenance.index', ['type' => 'Corrective']) }}" class="text-xs font-semibold text-indigo-600 hover:underline">View All →</a>
                            </div>
                            <div class="space-y-4">
                                @forelse($openCorrective as $record)
                                    <div class="text-sm">
                                        <p class="font-semibold">
                                            <!-- Compact Equipment List -->
                                            @foreach($record->equipment->take(2) as $pc)
                                                <a href="{{ route('equipment.show', $pc->id) }}" class="text-indigo-600 hover:underline">{{ $pc->tag_number }} ({{ $pc->lab->lab_name }})</a>{{ !$loop->last ? ',' : '' }}
                                            @endforeach
                                            @if($record->equipment->count() > 2)
                                                ... (+{{ $record->equipment->count() - 2 }} more)
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-600 truncate">Issue: {{ $record->issue_description }}</p>
                                        <p class="text-xs text-gray-500">
                                            Status: <span class="font-bold">{{ $record->status }}</span> | Assigned to: {{ $record->user->name ?? 'N/A' }}
                                        </p>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500">No open corrective maintenance logs.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <div class="flex justify-between items-center border-b pb-2 mb-4">
                                <h3 class="text-lg font-semibold">Open Preventive Maintenance</h3>
                                <a href="{{ route('maintenance.index', ['type' => 'Preventive']) }}" class="text-xs font-semibold text-indigo-600 hover:underline">View All →</a>
                            </div>
                            <div class="space-y-4">
                                @forelse($openPreventive as $record)
                                    <div class="text-sm">
                                        <p class="font-semibold">
                                            <!-- Compact Equipment List -->
                                            @foreach($record->equipment->take(2) as $pc)
                                                <a href="{{ route('equipment.show', $pc->id) }}" class="text-indigo-600 hover:underline">{{ $pc->tag_number }} ({{ $pc->lab->lab_name }})</a>{{ !$loop->last ? ',' : '' }}
                                            @endforeach
                                            @if($record->equipment->count() > 2)
                                                ... (+{{ $record->equipment->count() - 2 }} more)
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-600 truncate">Task: {{ $record->issue_description }}</p>
                                        <p class="text-xs text-gray-500">
                                            Status: <span class="font-bold">{{ $record->status }}</span> | Scheduled for: {{ \Carbon\Carbon::parse($record->scheduled_for)->format('M d, Y') }}
                                        </p>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500">No open preventive maintenance tasks.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column (Narrower) -->
                <div class="space-y-6">
                    <!-- Announcements Widget (Smaller) -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <div class="flex justify-between items-center border-b pb-2 mb-4">
                                <h3 class="text-lg font-semibold">Announcements</h3>
                                @if(Auth::user()->role == 'Admin')
                                <a href="{{ route('announcements.create') }}" class="text-xs bg-gray-800 text-white px-2 py-1 rounded hover:bg-gray-700">New</a>
                                @endif
                            </div>
                            <div class="space-y-3">
                                @forelse($announcements as $announcement)
                                    <!-- Main container for one announcement row -->
                                    <div class="p-2 rounded-md hover:bg-gray-50">
                                        <div class="flex justify-between items-start">
                                            
                                            <!-- This div contains the text and is what triggers the modal -->
                                            <div @click="openModal = true; modalTitle = `{{ addslashes($announcement->title) }}`; modalContent = `{{ addslashes($announcement->content) }}`"
                                                class="flex-grow cursor-pointer">
                                                <h4 class="font-semibold text-sm">{{ $announcement->title }}</h4>
                                                <p class="text-xs text-gray-500 line-clamp-2 break-words">{{ $announcement->content }}</p>
                                            </div>

                                            <!-- Admin-only Edit/Delete buttons are now a separate flex item -->
                                            @if(Auth::user()->role == 'Admin')
                                                <div class="text-xs flex-shrink-0 ml-4 space-x-2 pt-1">
                                                    <a href="{{ route('announcements.edit', $announcement->id) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">Edit</a>
                                                    <form action="{{ route('announcements.destroy', $announcement->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900 font-medium">Delete</button>
                                                    </form>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500">No recent announcements.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div x-show="openModal" 
            x-cloak
            @keydown.escape.window="openModal = false"
            class="fixed inset-0 z-50 flex items-center justify-center p-4">

            <!-- Modal Backdrop -->
            <div x-show="openModal" 
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="openModal = false"
                class="absolute inset-0 bg-gray-500 bg-opacity-75">
            </div>

            <!-- Modal Panel -->
            <div x-show="openModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative bg-white rounded-lg shadow-xl w-full max-w-2xl">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-4 border-b">
                    <h3 class="text-xl font-semibold" x-text="modalTitle"></h3>
                    <button @click="openModal = false" class="text-gray-400 hover:text-gray-600">×</button>
                </div>

                <!-- Modal Content -->
                <div class="p-6">
                    <p class="text-base leading-relaxed text-gray-700 whitespace-pre-wrap" x-text="modalContent"></p>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end p-4 border-t">
                    <button @click="openModal = false" type="button" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>