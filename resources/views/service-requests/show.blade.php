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
        // A simple variable to check if the ticket has been fully verified and closed.
        $isVerified = !empty($serviceRequest->client_verifier_name);
    @endphp

    <div class="py-12">
        <!-- This is the main page container -->
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Item 1: Back Link -->
            <div class="mb-4">
                <a href="{{ route('service-requests.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-semibold">
                    ← Back to Service Request List
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Left Column: Request Details & History -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Request Details Card -->
                    <div class="bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b"><h3 class="text-lg font-semibold">Request Details</h3></div>
                        <div class="p-6 space-y-4 text-sm">
                            <div>
                                <h4 class="font-medium text-gray-500">Requesting Office / Dept.</h4>
                                <p class="text-gray-800">{{ $serviceRequest->requesting_office }}</p>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-500">Submitted By</h4>
                                <p class="text-gray-800">{{ $serviceRequest->requester->name ?? 'N/A' }} on {{ $serviceRequest->created_at->format('M d, Y') }}</p>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-500">Problem Encountered / Description</h4>
                                <p class="text-gray-800 whitespace-pre-wrap">{{ $serviceRequest->description }}</p>
                            </div>
                            @if($serviceRequest->equipment_details)
                            <div>
                                <h4 class="font-medium text-gray-500">Affected Equipment</h4>
                                <p class="text-gray-800 whitespace-pre-wrap">{{ $serviceRequest->equipment_details }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- History Card -->
                    <div class="bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b"><h3 class="text-lg font-semibold">Ticket History</h3></div>
                        <div class="p-6 space-y-4">
                            @forelse($history as $log)
                                <div class="text-sm border-l-2 pl-4">
                                    <p><span class="font-semibold">{{ $log->user->name ?? 'System' }}</span> {{ $log->description }}</p>
                                    <p class="text-xs text-gray-400">{{ $log->created_at->format('M d, Y - h:i A') }}</p>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">No history for this ticket yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Right Column: Actions -->
                <div class="space-y-6">

                @if(Auth::id() === $serviceRequest->requester_id && ($serviceRequest->action_taken || $serviceRequest->recommendation))
                    <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                        <h3 class="text-lg font-semibold mb-4">Technician's Report</h3>
                        <div class="space-y-4 text-sm">
                            @if($serviceRequest->action_taken)
                            <div>
                                <h4 class="font-medium text-gray-500">Action Taken</h4>
                                <p class="text-gray-800 whitespace-pre-wrap">{{ $serviceRequest->action_taken }}</p>
                            </div>
                            @endif
                            @if($serviceRequest->recommendation)
                            <div>
                                <h4 class="font-medium text-gray-500">Recommendation</h4>
                                <p class="text-gray-800 whitespace-pre-wrap">{{ $serviceRequest->recommendation }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                @endif
                    
                    <!-- Technician Actions Panel -->
                    @if(in_array(Auth::user()->role, ['Admin', 'Technician']))
                        <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                            <h3 class="text-lg font-semibold mb-4">Technician Actions</h3>
                            
                            <form method="POST" action="{{ route('service-requests.update', $serviceRequest->id) }}"
                                x-data="{ status: '{{ old('status', $serviceRequest->status) }}' }">
                                @csrf
                                @method('PUT')
                                <div class="space-y-4">
                                    <!-- Status -->
                                    <div>
                                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                        <select name="status" id="status" 
                                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                                x-model="status" 
                                                {{ $isVerified ? 'disabled' : '' }}>
                                            <option value="Submitted">Submitted</option>
                                            <option value="In Review">In Review</option>
                                            <option value="In Progress">In Progress</option>
                                            <option value="On Hold">On Hold</option>
                                            <option value="Completed">Completed (Work is Done)</option>
                                            <option value="Rejected">Rejected</option>
                                        </select>
                                    </div>
                                    
                                    <div x-show="status === 'Rejected'" x-cloak>
                                        <label for="rejection_reason" class="block text-sm font-medium text-red-700">Rejection Reason*</label>
                                        <textarea name="rejection_reason" id="rejection_reason" rows="3" 
                                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500"
                                                :required="status === 'Rejected'">{{ old('rejection_reason', $serviceRequest->rejection_reason) }}</textarea>
                                    </div>
                                    
                                    <!-- Assign Technician -->
                                    <div>
                                        <label for="technician_id" class="block text-sm font-medium text-gray-700">Assign To</label>
                                        <!-- ADDED CLASSES HERE -->
                                        <select name="technician_id" id="technician_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" {{ $isVerified ? 'disabled' : '' }}>
                                            <option value="">-- Unassigned --</option>
                                            @foreach($technicians as $technician)
                                                <option value="{{ $technician->id }}" @selected($serviceRequest->technician_id == $technician->id)>{{ $technician->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Classification -->
                                    <div>
                                        <label for="classification" class="block text-sm font-medium text-gray-700">Classification</label>
                                        <!-- ADDED CLASSES HERE -->
                                        <select name="classification" id="classification" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" {{ $isVerified ? 'disabled' : '' }}>
                                            <option value="Unclassified" @selected($serviceRequest->classification == 'Unclassified')>Unclassified</option>
                                            <option value="Simple" @selected($serviceRequest->classification == 'Simple')>Simple</option>
                                            <option value="Complex" @selected($serviceRequest->classification == 'Complex')>Complex</option>
                                        </select>
                                    </div>

                                    <!-- Action Taken -->
                                    <div>
                                        <label for="action_taken" class="block text-sm font-medium text-gray-700">Action Taken</label>
                                        <textarea name="action_taken" id="action_taken" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" {{ $isVerified ? 'disabled' : '' }}>{{ $serviceRequest->action_taken }}</textarea>
                                    </div>

                                    <!-- Recommendation -->
                                    <div>
                                        <label for="recommendation" class="block text-sm font-medium text-gray-700">Recommendation</label>
                                        <textarea name="recommendation" id="recommendation" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" {{ $isVerified ? 'disabled' : '' }}>{{ $serviceRequest->recommendation }}</textarea>
                                    </div>

                                    @if(!$isVerified)
                                    <div class="pt-2 text-right">
                                        <!-- ADDED CLASSES HERE -->
                                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                            Save Changes
                                        </button>
                                    </div>
                                    @endif
                                </div>
                            </form>
                        </div>
                    @endif

                    @if($isVerified || $serviceRequest->status == 'Rejected')
                        @php
                            $finalStatusMessage = $isVerified ? "Ticket Verified & Closed" : "Ticket Rejected";
                            $finalBgColor = $isVerified ? 'bg-green-100 border-green-500 text-green-700' : 'bg-red-100 border-red-500 text-red-700';
                            $finalReason = $isVerified ? "Verified by {$serviceRequest->client_verifier_name}." : "Reason: {$serviceRequest->rejection_reason}";
                        @endphp
                        <div class="{{ $finalBgColor }} border-l-4 p-4" role="alert">
                            <p class="font-bold">{{ $finalStatusMessage }}</p>
                            <p>{{ $finalReason }} No further actions can be taken.</p>
                        </div>
                    @endif

                    <!-- Custodian/Client Verification Panel -->
                    @if($serviceRequest->status === 'Completed' && !$isVerified && (Auth::id() === $serviceRequest->requester_id || Auth::user()->role === 'Admin'))
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
                                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 ...">
                                            Verify & Close Ticket
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @endif
                    
                    <!-- Display this message once the ticket is fully verified and closed -->
                    @if($isVerified)
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
                            <p class="font-bold">Ticket Verified & Closed</p>
                            <p>Verified by {{ $serviceRequest->client_verifier_name }} on {{ $serviceRequest->updated_at->format('M d, Y') }}. No further actions can be taken.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>