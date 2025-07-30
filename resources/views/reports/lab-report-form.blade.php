<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Generate Report for {{ $lab->lab_name }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('reports.lab', $lab->id) }}" target="_blank" class="p-6">
                    @csrf
                    <h3 class="text-lg font-medium text-gray-900">Select Date Range for History</h3>
                    <p class="mt-1 text-sm text-gray-600">Choose the time period for the maintenance activity log to be included in the report.</p>

                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="start_date" class="block font-medium text-sm text-gray-700">Start Date*</label>
                            <input type="date" name="start_date" id="start_date" required class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label for="end_date" class="block font-medium text-sm text-gray-700">End Date*</label>
                            <input type="date" name="end_date" id="end_date" required class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                    </div>
                    <div class="flex justify-end mt-6">
                        <a href="{{ url()->previous() }}" class="text-sm text-gray-600 self-center mr-4">Cancel</a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            Generate Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>