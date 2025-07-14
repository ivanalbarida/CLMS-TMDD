<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Generate Maintenance Report') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form method="GET" action="{{ route('reports.generate') }}" class="p-6">
                    <h3 class="text-lg font-medium text-gray-900">Report Filters</h3>
                    <p class="mt-1 text-sm text-gray-600">Select criteria to generate your report. Date range is based on when the task was reported.</p>

                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Start Date -->
                        <div>
                            <label for="start_date" class="block font-medium text-sm text-gray-700">Start Date*</label>
                            <input type="date" name="start_date" id="start_date" required class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <!-- End Date -->
                        <div>
                            <label for="end_date" class="block font-medium text-sm text-gray-700">End Date*</label>
                            <input type="date" name="end_date" id="end_date" required class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <!-- Maintenance Type -->
                        <div>
                            <label for="type" class="block font-medium text-sm text-gray-700">Maintenance Type</label>
                            <select name="type" id="type" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">All Types</option>
                                <option value="Corrective">Corrective</option>
                                <option value="Preventive">Preventive</option>
                            </select>
                        </div>
                        <!-- Status -->
                        <div>
                            <label for="status" class="block font-medium text-sm text-gray-700">Status</label>
                            <select name="status" id="status" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">All Statuses</option>
                                <option value="Pending">Pending</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Completed">Completed</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end mt-6">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"">
                            Generate Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>