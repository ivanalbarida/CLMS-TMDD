<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Labs & Equipment') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Action Buttons Container -->
            <div class="flex items-center space-x-4 mb-6">
                @if(Auth::user()->role == 'Admin')
                    <a href="{{ route('labs.create') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-500 active:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Add New Lab
                    </a>
                @endif
                <a href="{{ route('equipment.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    Add New Equipment
                </a>
                <a href="{{ route('import.show') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500">
                    Import from CSV
                </a>
            </div>

            <!-- Grid container for the lab cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($labs as $lab)
                    <!-- Main card container is a DIV with flexbox column layout -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex flex-col">
                        
                        <!-- This top part is a link that fills the available vertical space -->
                        <a href="{{ route('equipment.showByLab', $lab->id) }}" class="block p-6 hover:bg-gray-50 transition flex-grow">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-semibold text-lg text-gray-900">{{ $lab->lab_name }}</h3>
                                    <p class="text-sm text-gray-600">{{ $lab->building_name }}</p>
                                </div>
                                <div class="text-2xl font-bold text-gray-800">
                                    {{ $lab->equipment_count }}
                                </div>
                            </div>
                        </a>
                        
                        <!-- Admin-only Edit/Delete actions for the lab, at the bottom of the card -->
                        @if(Auth::user()->role == 'Admin')
                        <div class="border-t px-6 py-3 bg-gray-50 text-right space-x-4">
                            <a href="{{ route('labs.edit', $lab->id) }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">Edit Lab</a>
                            <form action="{{ route('labs.destroy', $lab->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure? Deleting a lab will also delete all associated equipment!')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm text-red-600 hover:text-red-900 font-medium">Delete Lab</button>
                            </form>
                        </div>
                        @endif
                    </div>
                @empty
                    <div class="col-span-full p-6 bg-white text-center text-gray-500 shadow-sm sm:rounded-lg">
                        No labs found. Please <a href="{{ route('labs.create') }}" class="text-indigo-600 underline">add a lab</a> to get started.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>