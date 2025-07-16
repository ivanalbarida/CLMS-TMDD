<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Top Row: Key Metric Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                
                <!-- For Repair Card -->
                <a href="{{ route('equipment.index', ['status' => 'For Repair']) }}" class="block hover:shadow-lg transition">
                    <div class="bg-white p-6 rounded-lg shadow h-full">
                        <h4 class="text-gray-500 text-sm font-medium">For Repair</h4>
                        <p class="text-3xl font-bold text-yellow-600">{{ $stats['for_repair'] }}</p>
                    </div>
                </a>

                <!-- Pending Corrective Card -->
                <a href="{{ route('maintenance.index', ['type' => 'Corrective', 'status' => 'Pending']) }}" class="block hover:shadow-lg transition">
                    <div class="bg-white p-6 rounded-lg shadow h-full">
                        <h4 class="text-gray-500 text-sm font-medium">Pending Corrective</h4>
                        <p class="text-3xl font-bold text-red-600">{{ $stats['pending_corrective'] }}</p>
                    </div>
                </a>

                <!-- Pending PM Today Card -->
                <a href="{{ route('pm-checklist.index') }}" class="block hover:shadow-lg transition">
                    <div class="bg-white p-6 rounded-lg shadow h-full">
                        <h4 class="text-gray-500 text-sm font-medium">Pending PM Today</h4>
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
                            <h3 class="text-lg font-semibold border-b pb-2 mb-4">Open Corrective Maintenance</h3>
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
                                    <div>
                                        <h4 class="font-semibold text-sm truncate">{{ $announcement->title }}</h4>
                                        <p class="text-xs text-gray-500 truncate">{{ $announcement->content }}</p>
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
    </div>
</x-app-layout>