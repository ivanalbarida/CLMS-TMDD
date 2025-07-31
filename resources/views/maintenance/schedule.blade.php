<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Schedule Preventive Maintenance (PM)</h2>
    </x-slot>

    <div class="py-12" x-data="{ selectedLab: '' }">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('maintenance.store') }}">
                    @csrf
                    <input type="hidden" name="type" value="Preventive">
                    <input type="hidden" name="status" value="Pending">
                    <input type="hidden" name="date_reported" value="{{ now()->toDateString() }}">
                    
                    <div class="p-6 space-y-6">
                        <!-- Step 1: Select a Lab -->
                        <div>
                            <label for="lab_selector" class="block font-medium text-sm text-gray-700">1. Select a Lab to view its equipment</label>
                            <select id="lab_selector" x-model="selectedLab" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">-- Select Lab --</option>
                                @foreach($labs as $lab)
                                    <option value="{{ $lab->id }}">{{ $lab->lab_name }} - {{ $lab->building_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Step 2: Select Equipment -->
                        <div x-show="selectedLab" x-cloak>
                            <label class="block font-medium text-sm text-gray-700">2. Select Equipment for this PM task</label>
                            <div class="mt-2 border rounded-md max-h-60 overflow-y-auto p-4">
                                @foreach($labs as $lab)
                                    <div x-show="selectedLab == {{ $lab->id }}" class="space-y-2">
                                        <label class="flex items-center font-bold">
                                            <input type="checkbox" @click="document.querySelectorAll('.pc-checkbox-{{ $lab->id }}').forEach(el => el.checked = $event.target.checked)" class="rounded">
                                            <span class="ml-2">Select All in this Lab</span>
                                        </label>
                                        @foreach($lab->equipment as $pc)
                                            <label class="flex items-center ml-6">
                                                <input type="checkbox" name="equipment_ids[]" value="{{ $pc->id }}" class="rounded pc-checkbox-{{ $lab->id }}">
                                                <span class="ml-2">{{ $pc->tag_number }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Step 3: Fill in Details -->
                        <div class="border-t pt-6 space-y-6">
                            <h3 class="block font-medium text-lg text-gray-800">3. Provide PM Details</h3>
                            
                            <!-- Scheduled For Date -->
                            <div>
                                <label for="scheduled_for" class="block font-medium text-sm text-gray-700">Scheduled For Date</label>
                                <input type="date" id="scheduled_for" name="scheduled_for" required class="block mt-1 w-full md:w-1/2 border-gray-300 rounded-md shadow-sm">
                            </div>
                            
                            <!-- Assigned Technician -->
                            <div>
                                <label for="user_id" class="block font-medium text-sm text-gray-700">Assigned Technician</label>
                                
                                @if(Auth::user()->role === 'Admin')
                                    <!-- ADMINS see a full dropdown to assign to anyone -->
                                    <select id="user_id" name="user_id" required class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                        <option value="">-- Select Technician --</option>
                                        @foreach($technicians as $tech)
                                            <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <!-- NON-ADMINS see their own name (read-only) and a hidden input -->
                                    <input type="hidden" name="user_id" value="{{ Auth::id() }}">
                                    <input type="text" value="{{ Auth::user()->name }}" disabled class="block mt-1 w-full border-gray-300 rounded-md shadow-sm bg-gray-100">
                                @endif
                            </div>

                            <!-- PM Tasks / Notes -->
                            <div>
                                <label for="issue_description" class="block font-medium text-sm text-gray-700">PM Tasks / Notes (e.g., "Quarterly dust cleaning, check all fans")</label>
                                <textarea id="issue_description" name="issue_description" rows="4" required class="block mt-1 w-full border-gray-300 rounded-md shadow-sm"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 bg-gray-50 flex justify-end">
                        <a href="{{ route('maintenance.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4 self-center">Cancel</a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500">
                            Schedule PM
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>