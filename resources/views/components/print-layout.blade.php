<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name', 'Laravel') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { 
                -webkit-print-color-adjust: exact; 
            }
            
            /* This is a more forceful way to hide elements */
            .no-print {
                display: none !important;
            }

            /* This is the key for the page layout */
            .print-container {
                max-width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
                box-shadow: none !important;
                border: none !important;
            }

            /* This ensures the content div also uses full width */
            .print-content {
                max-width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            
            /* ADD THIS NEW RULE FOR THE TABLE */
            table {
                width: 100% !important;
                table-layout: fixed; /* Helps prevent overflow */
                overflow: hidden !important;
            }
            /* END OF NEW RULE */
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-100">
    <main>
        {{ $slot }}
    </main>
</body>
</html>