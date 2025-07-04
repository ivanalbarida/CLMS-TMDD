<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Checklist Item') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('software-checklist.update', $softwareChecklist->id) }}" class="p-6">
                    @csrf
                    @method('PUT') <!-- Tells Laravel this is an UPDATE request -->

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Program Name -->
                        <div>
                            <label for="program_name" class="block font-medium text-sm text-gray-700">Program Name</label>
                            <input type="text" id="program_name" name="program_name" value="{{ old('program_name', $softwareChecklist->program_name) }}" required autofocus class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                        </div>

                        <!-- Year and Semester -->
                        <div>
                            <label for="year_and_sem" class="block font-medium text-sm text-gray-700">Year & Semester</label>
                            <input type="text" id="year_and_sem" name="year_and_sem" value="{{ old('year_and_sem', $softwareChecklist->year_and_sem) }}" required class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                        </div>

                        <!-- Software Name -->
                        <div>
                            <label for="software_name" class="block font-medium text-sm text-gray-700">Software Name</label>
                            <input type="text" id="software_name" name="software_name" value="{{ old('software_name', $softwareChecklist->software_name) }}" required class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                        </div>

                        <!-- Version -->
                        <div>
                            <label for="version" class="block font-medium text-sm text-gray-700">Version (Optional)</label>
                            <input type="text" id="version" name="version" value="{{ old('version', $softwareChecklist->version) }}" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                        </div>

                        <!-- Notes -->
                        <div class="md:col-span-2">
                            <label for="notes" class="block font-medium text-sm text-gray-700">Notes (Optional)</label>
                            <textarea id="notes" name="notes" rows="3" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('notes', $softwareChecklist->notes) }}</textarea>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('software-checklist.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            Update Item
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>