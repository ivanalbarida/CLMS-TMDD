<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Report: {{ $lab->lab_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { -webkit-print-color-adjust: exact; }
            .no-print { display: none; }
            table { page-break-inside: auto; }
            tr    { page-break-inside: avoid; page-break-after: auto; }
            thead { display: table-header-group; }
            tfoot { display: table-footer-group; }
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <div class="max-w-4xl mx-auto bg-white p-8 my-8 rounded-lg shadow">
        
        <!-- Report Header -->
        <div class="flex justify-between items-start border-b pb-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold">Lab Detail Report</h1>
                <h2 class="text-xl font-semibold text-gray-700">{{ $lab->lab_name }} ({{ $lab->building_name }})</h2>
                <p class="text-sm text-gray-500">History Period: {{ $startDate->format('M d, Y') }} to {{ $endDate->format('M d, Y') }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500">Generated on: {{ now()->format('M d, Y') }}</p>
                <button onclick="window.print()" class="no-print mt-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm font-medium">Print Report</button>
            </div>
        </div>

        <!-- Section 1: Assigned Software -->
        <div class="mb-8">
            <h3 class="text-lg font-bold mb-2 border-b pb-1">Required Software Profile: {{ $lab->softwareProfile->name ?? 'None Assigned' }}</h3>
            <ul class="list-disc list-inside text-sm space-y-1 pl-2">
                @forelse ($lab->softwareProfile->softwareItems ?? [] as $item)
                    <li><span class="font-semibold">{{ $item->name }}</span> {{ $item->version }}</li>
                @empty
                    <li>No software items in this profile.</li>
                @endforelse
            </ul>
        </div>

        <!-- Section 2: Equipment & Components -->
        <div class="mb-8">
            <h3 class="text-lg font-bold mb-2 border-b pb-1">Equipment Component List</h3>
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-2 py-2 text-left font-semibold">Tag No.</th>
                        <th class="px-2 py-2 text-left font-semibold">Status</th>
                        <th class="px-2 py-2 text-left font-semibold">Processor</th>
                        <th class="px-2 py-2 text-left font-semibold">Monitor</th>
                        <th class="px-2 py-2 text-left font-semibold">OS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($lab->equipment as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-2 py-2 font-semibold">{{ $item->tag_number }}</td>
                        <td class="px-2 py-2">{{ $item->status }}</td>
                        <td class="px-2 py-2">{{ $item->components->firstWhere('type', 'Processor')->description ?? 'N/A' }}</td>
                        <td class="px-2 py-2">{{ $item->components->firstWhere('type', 'Monitor')->description ?? 'N/A' }}</td>
                        <td class="px-2 py-2">{{ $item->components->firstWhere('type', 'OS')->description ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Section 3: Recent Maintenance History -->
        <div>
            <h3 class="text-lg font-bold mb-2 border-b pb-1">Maintenance History</h3>
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-2 py-2 text-left font-semibold">Date</th>
                        <th class="px-2 py-2 text-left font-semibold">Equipment</th>
                        <th class="px-2 py-2 text-left font-semibold">User</th>
                        <th class="px-2 py-2 text-left font-semibold" style="width: 40%;">Description</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($history as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="px-2 py-2 whitespace-nowrap">{{ $log->created_at->format('Y-m-d') }}</td>
                        <td class="px-2 py-2 font-semibold">{{ $log->subject->tag_number ?? 'N/A' }}</td>
                        <td class="px-2 py-2">{{ $log->user->name ?? 'System' }}</td>
                        <td class="px-2 py-2">{{ $log->description }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="py-4 text-center text-gray-500">No maintenance history in the selected period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</body>
</html>