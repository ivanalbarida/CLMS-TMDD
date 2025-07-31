<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PM Compliance Report for {{ $lab->lab_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { -webkit-print-color-adjust: exact; }
            .no-print { display: none; }
            table { page-break-inside: auto; }
            tr    { page-break-inside: avoid; page-break-after: auto; }
            thead { display: table-header-group; }
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <div class="max-w-4xl mx-auto bg-white p-8 my-8 rounded-lg shadow">
        
        <!-- Report Header -->
        <div class="flex justify-between items-start border-b pb-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold">PM Compliance Report</h1>
                <h2 class="text-xl font-semibold text-gray-700">{{ $lab->lab_name }} ({{ $lab->building_name }})</h2>
                <p class="text-sm text-gray-500">Period: {{ $startDate->format('M d, Y') }} to {{ $endDate->format('M d, Y') }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500">Generated on: {{ now()->format('M d, Y') }}</p>
                <button onclick="window.print()" class="no-print mt-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm font-medium">Print Report</button>
            </div>
        </div>

        <!-- Report Content -->
        <div>
            <h3 class="text-lg font-bold mb-4">List of Missed Tasks</h3>
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-2 py-2 text-left font-semibold">Date Missed</th>
                        <th class="px-2 py-2 text-left font-semibold">Frequency</th>
                        <th class="px-2 py-2 text-left font-semibold" style="width: 50%;">Task Description</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($missedTasks as $missed)
                    <tr>
                        <td class="px-2 py-2 whitespace-nowrap">{{ $missed['date']->format('M d, Y') }}</td>
                        <td class="px-2 py-2">{{ $missed['task']->frequency }}</td>
                        <td class="px-2 py-2">{{ $missed['task']->task_description }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="py-4 text-center text-green-600 font-semibold">
                            Congratulations! No tasks were missed in this period for this lab.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</body>
</html>