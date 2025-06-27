<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ ucfirst($status) }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.4.1/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-6">
    <div class="bg-white shadow-xl rounded-2xl p-10 max-w-md text-center">
        @if ($status === 'accepted')

            <h1 class="text-2xl font-semibold text-green-600 mb-4">Invoice Accepted</h1>
            <p class="text-gray-600">Thank you! The invoice has been accepted successfully.</p>
        @elseif ($status === 'rejected')

            <h1 class="text-2xl font-semibold text-red-600 mb-4">Invoice Rejected</h1>
            <p class="text-gray-600">The invoice has been rejected. Please contact support for more information.</p>
        @else
            <h1 class="text-xl font-bold text-gray-700">Unknown status.</h1>
        @endif
    </div>
</body>
</html>
