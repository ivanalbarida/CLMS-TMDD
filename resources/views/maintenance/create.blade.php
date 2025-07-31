<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Report Issue (Corrective)</h2>
    </x-slot>
    <div class="py-12" x-data="{ selectedLab: '' }">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('maintenance.store') }}" x-data="{ status: '{{ old('status', 'Pending') }}' }">
                    @csrf
                    <input type="hidden" name="type" value="Corrective">

                    @if ($errors->any())
                        <div class="p-4 mx-6 mt-6 bg-red-100 border-l-4 border-red-500 text-red-700">
                            <h3 class="font-bold">Error</h3>
                            <ul class="mt-2 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <div class="p-6 space-y-6">
                        <!-- Step 1: Select a Lab -->
                        <div>
                            <label for="lab_selector" class="block font-medium text-sm text-gray-700">1. Select a Lab</label>
                            <select id="lab_selector" x-model="selectedLab" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">-- Select Lab --</option>
                                @foreach($labs as $lab)
                                    <option value="{{ $lab->id }}">{{ $lab->lab_name }} - {{ $lab->building_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Step 2: Select Equipment -->
                        <div x-show="selectedLab" x-cloak>
                            <label class="block font-medium text-sm text-gray-700">2. Select Equipment</label>
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
                            <h3 class="block font-medium text-sm text-gray-700">3. Provide Log Details</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                                <div>
                                    <label for="category" class="block font-medium text-sm text-gray-700">Category*</label>
                                    <select id="category" name="category" required class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                        <option value="">-- Select a Category --</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category }}">{{ $category }}</option>
                                        @endforeach
                                    </select>
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

                                <!-- Status -->
                                <div>
                                    <label for="status" class="block font-medium text-sm text-gray-700">Status</label>
                                    <select id="status" name="status" required class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" x-model="status">
                                        @foreach($statuses as $status)
                                        <option value="{{ $status }}">{{ $status }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Date Reported (hidden but required) -->
                                <input type="hidden" name="date_reported" value="{{ now()->toDateString() }}">
                            </div>

                            <!-- Issue Description -->
                            <div>
                                <label for="issue_description" class="block font-medium text-sm text-gray-700">Issue Description</label>
                                <textarea id="issue_description" name="issue_description" rows="4" required class="block mt-1 w-full border-gray-300 rounded-md shadow-sm"></textarea>
                            </div>

                            <div x-show="status === 'Completed'" x-cloak class="space-y-6">
                                <div>
                                    <label for="action_taken" class="block font-medium text-sm text-gray-700">Action Taken*</label>
                                    <textarea id="action_taken" name="action_taken" rows="4" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" :required="status === 'Completed'">{{ old('action_taken') }}</textarea>
                                </div>
                                <div>
                                    <label for="date_completed" class="block font-medium text-sm text-gray-700">Date Completed*</label>
                                    <input type="date" id="date_completed" name="date_completed" value="{{ old('date_completed') }}" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" :required="status === 'Completed'">
                                </div>
                            </div>
                            
                        </div>

                    </div>

                    <div class="p-6 bg-gray-50 flex justify-end items-center">
                        <a href="{{ route('maintenance.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Save Log
                    </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>