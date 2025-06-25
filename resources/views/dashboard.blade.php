<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <!-- Main Content Column (Announcements etc.) -->
                <div class="md:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold border-b pb-2 mb-4">Announcements</h3>
                        <p>Welcome to the Computer Lab Management System!</p>
                        <!-- This is where announcements would go -->
                    </div>
                </div>

                <!-- Right Sidebar Column -->
                <div class="space-y-6">

                    <!-- Stats Overview Widget -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-semibold border-b pb-2 mb-4">Stats Overview</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span>Total Equipment</span>
                                    <span class="font-bold text-xl">{{ $stats['total_equipment'] }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span>Working</span>
                                    <span class="font-bold text-xl text-green-600">{{ $stats['working_equipment'] }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span>For Repair</span>
                                    <span class="font-bold text-xl text-yellow-600">{{ $stats['for_repair'] }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span>Pending Maintenance</span>
                                    <span class="font-bold text-xl text-red-600">{{ $stats['pending_maintenance'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activities Widget -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-semibold border-b pb-2 mb-4">Recent Activities</h3>
                            <div class="space-y-4">
                                @forelse($recentActivities as $activity)
                                    <div class="text-sm">
                                        <p class="font-semibold">
                                            <a href="{{ route('equipment.show', $activity->equipment->id) }}" class="text-indigo-600 hover:underline">
                                                {{ $activity->equipment->tag_number }}
                                            </a>
                                            reported as <span class="font-bold">{{ $activity->status }}</span>
                                        </p>
                                        <p class="text-gray-500 text-xs">
                                            By {{ $activity->user->name }} on {{ \Carbon\Carbon::parse($activity->date_reported)->format('M d, Y') }}
                                        </p>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500">No recent activities.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>