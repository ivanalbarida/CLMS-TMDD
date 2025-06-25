<!-- File: resources/views/equipment/index.blade.php (The NEW version) -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Equipment by Location') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <!-- Add/Import buttons can stay here -->
                    <div class="flex items-center space-x-4 mb-6">
            <!-- ADD CLASSES TO THIS LINK -->
            <a href="{{ route('equipment.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                Add New Equipment
            </a>

            <!-- ADD CLASSES TO THIS LINK -->
            <a href="{{ route('import.show') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500">
                Import from CSV
            </a>
        </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($labs as $lab)
                <a href="{{ route('equipment.showByLab', $lab->id) }}" class="block p-6 bg-white overflow-hidden shadow-sm sm:rounded-lg hover:bg-gray-50 transition">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="font-semibold text-lg text-gray-900">{{ $lab->lab_name }}</h3>
                            <p class="text-sm text-gray-600">{{ $lab->building_name }}</p>
                        </div>
                        <div class="text-2xl font-bold text-gray-800">
                            {{ $lab->equipment_count }}
                        </div>
                    </div>
                </a>
                @empty
                <div class="col-span-full p-6 bg-white text-center text-gray-500 shadow-sm sm:rounded-lg">
                    No labs found. Please add a lab first.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>