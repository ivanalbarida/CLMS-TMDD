<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Maintenance Log (ID: {{ $maintenance->id }})
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ selectedLab: '{{ $maintenance->equipment->first()->lab_id ?? '' }}' }">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('maintenance.update', $maintenance->id) }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="type" value="{{ $maintenance->type }}">
                    
                    <div class="p-6 space-y-6">
                        <!-- Step 1: Lab Selector -->
                        <div>
                            <label for="lab_selector" class="block font-medium text-sm text-gray-700">1. Select a Lab</label>
                            <select id="lab_selector" x-model="selectedLab" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">-- Select Lab --</option>
                                @foreach($labs as $lab)
                                    <option value="{{ $lab->id }}">{{ $lab->lab_name }} - {{ $lab->building_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Step 2: Equipment Selector -->
                        <div x-show="selectedLab" x-cloak>
                            <label class="block font-medium text-sm text-gray-700">2. Select Equipment</label>
                            <div class="mt-2 border rounded-md max-h-60 overflow-y-auto p-4">
                                @foreach($labs as $lab)
                                    <div x-show="selectedLab == {{ $lab->id }}" class="space-y-2">
                                        @foreach($lab->equipment as $pc)
                                            <label class="flex items-center ml-6">
                                                <input type="checkbox" name="equipment_ids[]" value="{{ $pc->id }}" class="rounded"
                                                    @if(in_array($pc->id, $assignedEquipmentIds)) checked @endif
                                                >
                                                <span class="ml-2">{{ $pc->tag_number }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Step 3: Log Details (This is the new, complete section) -->
                        <div class="border-t pt-6 space-y-6">
                            <h3 class="block font-medium text-lg text-gray-800">3. Provide Log Details</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Assigned Technician -->
                                <div>
                                    <label for="user_id" class="block font-medium text-sm text-gray-700">Assigned Technician</label>
                                    <select id="user_id" name="user_id" required class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                        @foreach($technicians as $tech)
                                            <option value="{{ $tech->id }}" @selected(old('user_id', $maintenance->user_id) == $tech->id)>{{ $tech->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Status -->
                                <div>
                                    <label for="status" class="block font-medium text-sm text-gray-700">Status</label>
                                    <select id="status" name="status" required class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                        @foreach($statuses as $status)
                                            <option value="{{ $status }}" @selected(old('status', $maintenance->status) == $status)>{{ $status }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Issue Description -->
                            <div>
                                <label for="issue_description" class="block font-medium text-sm text-gray-700">Issue Description / PM Tasks</label>
                                <textarea id="issue_description" name="issue_description" rows="4" required class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('issue_description', $maintenance->issue_description) }}</textarea>
                            </div>

                            <!-- Action Taken -->
                            <div>
                                <label for="action_taken" class="block font-medium text-sm text-gray-700">Action Taken</label>
                                <textarea id="action_taken" name="action_taken" rows="4" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('action_taken', $maintenance->action_taken) }}</textarea>
                            </div>

                            <!-- Date Completed -->
                            <div>
                                <label for="date_completed" class="block font-medium text-sm text-gray-700">Date Completed</label>
                                <input type="date" id="date_completed" name="date_completed" value="{{ old('date_completed', $maintenance->date_completed) }}" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                        </div>
                    </div>

                    <div class="p-6 bg-gray-50 flex justify-end">
                        <a href="{{ route('maintenance.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4 self-center">Cancel</a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            Update Log
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>