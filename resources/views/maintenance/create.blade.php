<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add New Maintenance Log') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('maintenance.store') }}" class="p-6">
                    @csrf
                     <input type="hidden" name="type" value="Corrective">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Equipment -->
                        <div>
                            <label for="equipment_id" class="block font-medium text-sm text-gray-700">Equipment Tag Number</label>
                            <select id="equipment_id" name="equipment_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="">-- Select Equipment --</option>
                                @foreach($equipment as $item)
                                <option value="{{ $item->id }}">{{ $item->tag_number }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Assigned Technician -->
                        <div>
                            <label for="user_id" class="block font-medium text-sm text-gray-700">Assigned Technician</label>
                            <select id="user_id" name="user_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="">-- Select Technician --</option>
                                @foreach($technicians as $tech)
                                <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date Reported -->
                        <div>
                            <label for="date_reported" class="block font-medium text-sm text-gray-700">Date Reported</label>
                            <input type="date" id="date_reported" name="date_reported" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block font-medium text-sm text-gray-700">Status</label>
                            <select id="status" name="status" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                                @foreach($statuses as $status)
                                <option value="{{ $status }}">{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Issue Description -->
                        <div class="md:col-span-2">
                            <label for="issue_description" class="block font-medium text-sm text-gray-700">Issue Description</label>
                            <textarea id="issue_description" name="issue_description" rows="4" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required></textarea>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('maintenance.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            Save Log
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>