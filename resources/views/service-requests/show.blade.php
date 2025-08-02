<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Service Request: {{ $serviceRequest->title }}
            </h2>
            <span class="font-mono text-sm text-gray-500">#SR-A-{{ str_pad($serviceRequest->id, 4, '0', STR_PAD_LEFT) }}</span>
        </div>
    </x-slot>

    @php
        // A ticket is "closed" if it's verified OR if its status is Rejected.
        $isClosed = !empty($serviceRequest->client_verifier_name) || $serviceRequest->status == 'Rejected';
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Back to List Link -->
            <div class="mb-4">
                <a href="{{ route('service-requests.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-semibold">
                    ← Back to Service Request List
                </a>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Left Column: Request Details & History -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Request Details Card -->
                    <div class="bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b"><h3 class="text-lg font-semibold">Request Details</h3></div>
                        <div class="p-6 space-y-4 text-sm">
                            <!-- ... all your request details fields are correct here ... -->
                            <div><h4 class="font-medium text-gray-500">Requesting Office / Dept.</h4><p>{{ $serviceRequest->requesting_office }}</p></div>
                            <div><h4 class="font-medium text-gray-500">Submitted By</h4><p>{{ $serviceRequest->requester->name ?? 'N/A' }} on {{ $serviceRequest->created_at->format('M d, Y') }}</p></div>
                            <div><h4 class="font-medium text-gray-500">Problem Encountered / Description</h4><p class="whitespace-pre-wrap">{{ $serviceRequest->description }}</p></div>
                            @if($serviceRequest->equipment_details)<div><h4 class="font-medium text-gray-500">Affected Equipment</h4><p class="whitespace-pre-wrap">{{ $serviceRequest->equipment_details }}</p></div>@endif
                        </div>
                    </div>

                    <!-- History Card -->
                    <div class="bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b"><h3 class="text-lg font-semibold">Ticket History</h3></div>
                        <div class="p-6 space-y-4">
                            @forelse($history as $log)
                                <div class="text-sm border-l-2 pl-4"><p><span class="font-semibold">{{ $log->user->name ?? 'System' }}</span> {{ $log->description }}</p><p class="text-xs text-gray-400">{{ $log->created_at->format('M d, Y - h:i A') }}</p></div>
                            @empty
                                <p class="text-sm text-gray-500">No history for this ticket yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Right Column: Actions -->
                <div class="lg:col-span-1 space-y-6">

                    <!-- Technician's Report (for Custodian view) -->
                    @if(Auth::id() === $serviceRequest->requester_id && ($serviceRequest->action_taken || $serviceRequest->recommendation))
                        <div class="bg-white p-6 shadow-sm sm:rounded-lg"> ... </div>
                    @endif
                    
                    <!-- Technician Actions Panel -->
                    @if(in_array(Auth::user()->role, ['Admin', 'Custodian/Technician']))
                        <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                            <h3 class="text-lg font-semibold mb-4">Technician Actions</h3>
                            
                            <form method="POST" action="{{ route('service-requests.update', $serviceRequest->id) }}"
                                x-data="{ status: '{{ old('status', $serviceRequest->status) }}' }">
                                @csrf
                                @method('PUT')
                                <div class="space-y-4">
                                    <!-- Status Dropdown -->
                                    <div>
                                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                        <select name="status" id="status" x-model="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" {{ $isClosed ? 'disabled' : '' }}>
                                            <option value="Submitted">Submitted</option>
                                            <option value="In Review">In Review</option>
                                            <option value="In Progress">In Progress</option>
                                            <option value="On Hold">On Hold</option>
                                            <option value="Completed">Completed (Work is Done)</option>
                                            <option value="Rejected">Rejected</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Conditional Rejection Reason -->
                                    <div x-show="status === 'Rejected'" x-cloak>
                                        <label for="rejection_reason" class="block text-sm font-medium text-red-700">Rejection Reason*</label>
                                        <textarea name="rejection_reason" id="rejection_reason" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500" :required="status === 'Rejected'">{{ old('rejection_reason', $serviceRequest->rejection_reason) }}</textarea>
                                    </div>
                                    
                                    <!-- Assign To, Classification, Action Taken, Recommendation with FULL STYLING -->
                                    <div>
                                        <label for="technician_id" class="block text-sm font-medium text-gray-700">Assign To</label>
                                        @if($isClosed && $serviceRequest->technician_id)
                                            <input type="hidden" name="technician_id" value="{{ $serviceRequest->technician_id }}">
                                        @endif
                                        <select name="technician_id" id="technician_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-100" {{ $isClosed ? 'disabled' : '' }} @if(Auth::user()->role !== 'Admin') disabled @endif>
                                            <option value="">-- Unassigned --</option>
                                            @foreach($technicians as $technician)
                                                <option value="{{ $technician->id }}" @selected($serviceRequest->technician_id == $technician->id)>{{ $technician->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="classification" class="block text-sm font-medium text-gray-700">Classification</label>
                                        <select name="classification" id="classification" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" {{ $isClosed ? 'disabled' : '' }}>
                                            <option value="Unclassified" @selected($serviceRequest->classification == 'Unclassified')>Unclassified</option>
                                            <option value="Simple" @selected($serviceRequest->classification == 'Simple')>Simple</option>
                                            <option value="Complex" @selected($serviceRequest->classification == 'Complex')>Complex</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="action_taken" class="block text-sm font-medium text-gray-700">Action Taken</label>
                                        <textarea name="action_taken" id="action_taken" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" {{ $isClosed ? 'disabled' : '' }}>{{ $serviceRequest->action_taken }}</textarea>
                                    </div>
                                    <div>
                                        <label for="recommendation" class="block text-sm font-medium text-gray-700">Recommendation</label>
                                        <textarea name="recommendation" id="recommendation" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" {{ $isClosed ? 'disabled' : '' }}>{{ $serviceRequest->recommendation }}</textarea>
                                    </div>

                                    @if(!$isClosed)
                                    <div class="pt-2 text-right">
                                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                            Save Changes
                                        </button>
                                    </div>
                                    @endif
                                </div>
                            </form>
                        </div>
                    @endif

                    @if($serviceRequest->status === 'Completed' && !$isClosed && (Auth::id() === $serviceRequest->requester_id || Auth::user()->role === 'Admin'))
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-6 shadow-sm sm:rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Client Verification</h3>
                            <p class="text-sm text-gray-600 mb-4">The technician has marked this task as complete. Please verify the service and close this ticket.</p>
                            <form method="POST" action="{{ route('service-requests.verify', $serviceRequest->id) }}">
                                @csrf
                                <div class="space-y-4">
                                    <div>
                                        <label for="status_after_service" class="block text-sm font-medium text-gray-700">Status of Equipment After Service*</label>
                                        <input type="text" name="status_after_service" id="status_after_service" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    </div>
                                    <div>
                                        <label for="client_verifier_name" class="block text-sm font-medium text-gray-700">Your Name (as Verifier)*</label>
                                        <input type="text" name="client_verifier_name" id="client_verifier_name" value="{{ Auth::user()->name }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    </div>
                                    <div class="pt-2 text-right">
                                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                            Verify & Close Ticket
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @endif

                    <!-- Custodian/Client Verification Panel (This should be outside the Technician panel) -->
                    @if($serviceRequest->status === 'Completed' && !$isClosed && (Auth::id() === $serviceRequest->requester_id || Auth::user()->role === 'Admin'))
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-6 shadow-sm sm:rounded-lg">
                            <!-- ... Verification form ... -->
                        </div>
                    @endif
                    
                    <!-- Final Status Message (Corrected Logic) -->
                    @if($isClosed)
                        <div class="{{ $serviceRequest->status == 'Rejected' ? 'bg-red-100 border-red-500 text-red-700' : 'bg-green-100 border-green-500 text-green-700' }} border-l-4 p-4" role="alert">
                            <p class="font-bold">{{ $serviceRequest->status == 'Rejected' ? 'Ticket Rejected' : 'Ticket Verified & Closed' }}</p>
                            <p>{{ $serviceRequest->status == 'Rejected' ? 'Reason: ' . ($serviceRequest->rejection_reason ?? 'No reason provided.') : "Verified by {$serviceRequest->client_verifier_name}." }} No further actions can be taken.</p>
                        </div>
                    @endif
                </div>
        </div>
    </div>
</x-app-layout>