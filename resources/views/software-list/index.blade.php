<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Official Software List by Lab') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="space-y-8">
                
                @forelse ($labsWithSoftware as $lab)
                    <!-- Card for each Lab -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        
                        <!-- Lab Header Section -->
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-2xl font-bold text-gray-800">{{ $lab->lab_name }} ({{ $lab->building_name }})</h3>
                            @if($lab->softwareProfile)
                                <p class="mt-1 text-sm text-gray-600">
                                    Assigned Profile: <span class="font-semibold">{{ $lab->softwareProfile->name }}</span>
                                </p>
                            @endif
                        </div>

                        <!-- Software List Section -->
                        <div class="p-6">
                            <ul class="list-disc list-inside space-y-3">
                                @forelse ($lab->softwareProfile->softwareItems ?? [] as $item)
                                    <li>
                                        <span class="font-semibold text-gray-800">{{ $item->name }} {{ $item->version }}</span>
                                        @if($item->license_details)
                                            <p class="text-xs text-gray-500 pl-6 mt-1 bg-gray-50 p-2 rounded-md">{{ $item->license_details }}</p>
                                        @endif
                                    </li>
                                @empty
                                    <p class="text-sm text-gray-500">No software has been assigned to this lab's profile yet.</p>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                @empty
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-center text-gray-500">
                            No labs have software profiles assigned yet. An administrator can assign them under "Configuration" -> "Manage Labs".
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>