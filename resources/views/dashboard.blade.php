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
                <a href="{{ route('equipment.index') }}" class="block hover:shadow-lg transition">
                    <div class="bg-white p-6 rounded-lg shadow h-full">
                        <h4 class="text-gray-500 text-sm font-medium">Total Equipment</h4>
                        <p class="text-3xl font-bold text-gray-700">{{ $stats['total_equipment'] }}</p>
                    </div>
                </a>
                <a href="{{ route('equipment.index', ['status' => 'For Repair']) }}" class="block hover:shadow-lg transition">
                    <div class="bg-white p-6 rounded-lg shadow h-full">
                        <h4 class="text-gray-500 text-sm font-medium">For Repair</h4>
                        <p class="text-3xl font-bold text-yellow-600">{{ $stats['for_repair'] }}</p>
                    </div>
                </a>
                <a href="{{ route('pm-checklist.index') }}" class="block hover:shadow-lg transition">
                    <div class="bg-white p-6 rounded-lg shadow h-full">
                        <h4 class="text-gray-500 text-sm font-medium">Today's Checklist Items</h4>
                        <p class="text-3xl font-bold text-blue-600">{{ $stats['pending_pm'] }}</p>
                    </div>
                </a>
                <a href="{{ route('service-requests.index') }}" class="block hover:shadow-lg transition">
                    <div class="bg-white p-6 rounded-lg shadow h-full">
                        <h4 class="text-gray-500 text-sm font-medium">Open Service Requests</h4>
                        <p class="text-3xl font-bold text-purple-600">{{ $stats['open_service_requests'] }}</p>
                    </div>
                </a>
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

                    <!-- Open Preventive Maintenance Widget -->
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
                    <livewire:announcements-widget />
                </div>

            </div>
        </div>
    </div>
</x-app-layout>