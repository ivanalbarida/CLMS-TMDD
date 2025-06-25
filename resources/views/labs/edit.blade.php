<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Lab') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('labs.update', $lab->id) }}">
                        @csrf <!-- Security Token -->
                        @method('PUT') <!-- Specify that this is an UPDATE operation -->

                        <!-- Lab Name -->
                        <div>
                            <label for="lab_name">Lab Name / Room #</label>
                            <input id="lab_name" class="block mt-1 w-full" type="text" name="lab_name" value="{{ old('lab_name', $lab->lab_name) }}" required autofocus />
                        </div>

                        <!-- Building Name -->
                        <div class="mt-4">
                            <label for="building_name">Building Name</label>
                            <input id="building_name" class="block mt-1 w-full" type="text" name="building_name" value="{{ old('building_name', $lab->building_name) }}" required />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Update Lab
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>