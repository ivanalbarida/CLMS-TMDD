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
                        <div class="flex justify-between items-center border-b pb-2 mb-4">
                            <h3 class="text-lg font-semibold">Announcements</h3>
                            
                            {{-- This button only shows if the user is an Admin --}}
                            @if(Auth::user()->role == 'Admin')
                                <a href="{{ route('announcements.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                    New Announcement
                                </a>
                            @endif
                        </div>
                        
                        <div class="space-y-4">
                            @forelse($announcements as $announcement)
                                <div class="border-b pb-4 last:border-b-0">
                                    
                                    <div class="flex justify-between items-start">
                                        <h4 class="font-bold text-lg">{{ $announcement->title }}</h4>

                                        {{-- Admin-only action buttons --}}
                                        @if(Auth::user()->role == 'Admin')
                                            <div class="flex space-x-4 text-sm flex-shrink-0 ml-4">
                                                <a href="{{ route('announcements.edit', $announcement->id) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                                <form action="{{ route('announcements.destroy', $announcement->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this announcement?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- The content part remains the same --}}
                                    <p class="mt-1 text-sm text-gray-700">{{ $announcement->content }}</p>
                                </div>
                            @empty
                                <p>No announcements yet.</p>
                            @endforelse
                        </div>
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
                                        <div class="font-semibold">
                                            @foreach($activity->equipment as $pc)
                                                <a href="{{ route('equipment.show', $pc->id) }}" class="text-indigo-600 hover:underline block">
                                                    {{ $pc->tag_number }} ({{ $pc->lab->lab_name }})
                                                </a>
                                            @endforeach
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">
                                            Reported as <span class="font-bold">{{ $activity->status }}</span> by {{ $activity->user->name }} on {{ \Carbon\Carbon::parse($activity->date_reported)->format('M d, Y') }}
                                        </p>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500">No new issues reported.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-semibold border-b pb-2 mb-4">Upcoming Scheduled PM</h3>
                            <div class="space-y-4">
                                @forelse($upcomingPM as $pm)
                                    <div class="text-sm">
                                        <div class="font-semibold">
                                            @foreach($pm->equipment as $pc)
                                                <a href="{{ route('equipment.show', $pc->id) }}" class="text-indigo-600 hover:underline block">
                                                    {{ $pc->tag_number }} ({{ $pc->lab->lab_name }})
                                                </a>
                                            @endforeach
                                        </div>
                                        <p class="text-gray-500 text-xs">
                                            Scheduled for: <span class="font-bold">{{ \Carbon\Carbon::parse($pm->scheduled_for)->format('M d, Y') }}</span>
                                        </p>
                                        <p class="text-gray-500 text-xs">
                                            Scheduled for: <span class="font-bold">{{ \Carbon\Carbon::parse($pm->scheduled_for)->format('M d, Y') }}</span>
                                        </p>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500">No upcoming preventive maintenance.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>