<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Create Service Request') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('service-requests.store') }}" class="p-6">
                    @csrf
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700">
                            <h3 class="font-bold">Error</h3>
                            <ul class="mt-2 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="space-y-6">
                        <div>
                            <label for="title" class="block font-medium text-sm text-gray-700">Request Title*</label>
                            <input type="text" name="title" id="title" required class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="requesting_office" class="block font-medium text-sm text-gray-700">Requesting Office/Dept*</label>
                                <input type="text" name="requesting_office" id="requesting_office" required class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                            <div>
                                <label for="request_type" class="block font-medium text-sm text-gray-700">Request Type*</label>
                                <select name="request_type" id="request_type" required class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="Procurement">Procurement (New Equipment)</option>
                                    <option value="Repair">Repair</option>
                                    <option value="Condemnation">Condemnation (Disposal)</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label for="equipment_details" class="block font-medium text-sm text-gray-700">Equipment / Property Tag / Serial No. (If applicable)</label>
                            <textarea name="equipment_details" id="equipment_details" rows="2" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm"></textarea>
                        </div>
                        <div>
                            <label for="description" class="block font-medium text-sm text-gray-700">Problem Encountered / Detailed Description*</label>
                            <textarea name="description" id="description" rows="5" required class="block mt-1 w-full border-gray-300 rounded-md shadow-sm"></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end items-center mt-6 pt-6 border-t">
                        <a href="{{ route('service-requests.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">Submit Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>