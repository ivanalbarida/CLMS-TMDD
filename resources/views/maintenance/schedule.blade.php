<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Schedule Preventive Maintenance (PM)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('maintenance.store') }}" class="p-6">
                    @csrf
                    <!-- Hidden fields -->
                    <input type="hidden" name="type" value="Preventive">
                    <input type="hidden" name="status" value="Pending">
                    <input type="hidden" name="date_reported" value="{{ now()->toDateString() }}">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Equipment Dropdown -->
                        <div>
                            <label for="equipment_id" class="block font-medium text-sm text-gray-700">Equipment</label>
                            <select id="equipment_id" name="equipment_id" required class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">-- Select Equipment --</option>
                                @foreach($equipment as $item)
                                    <option value="{{ $item->id }}">{{ $item->tag_number }} ({{ $item->lab->lab_name }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Scheduled For Date -->
                        <div>
                            <label for="scheduled_for" class="block font-medium text-sm text-gray-700">Scheduled For Date</label>
                            <input type="date" id="scheduled_for" name="scheduled_for" required class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        </div>

                        <!-- Assigned Technician Dropdown -->
                        <div class="md:col-span-2">
                            <label for="user_id" class="block font-medium text-sm text-gray-700">Assign To</label>
                            <select id="user_id" name="user_id" required class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">-- Select Technician --</option>
                                @foreach($technicians as $tech)
                                    <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Issue Description (for PM Tasks) -->
                        <div class="md:col-span-2">
                            <label for="issue_description" class="block font-medium text-sm text-gray-700">PM Tasks / Notes</label>
                            <textarea id="issue_description" name="issue_description" rows="4" required class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                        </div>
                    </div>

                    <!-- THIS IS THE CORRECTED BUTTONS SECTION -->
                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('maintenance.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Schedule PM
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>