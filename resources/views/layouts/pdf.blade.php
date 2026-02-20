<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Details</title>
    <style>
        /* General Body Style */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
        }

        /* Page Setup for A4 */
        @page {
            size: A4;
            margin: 10mm;
        }

        /* Header Style (Company Logo and Name) */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #007bff;
            padding: 10px;
        }

        .header img {
            height: 50px;
        }

        .company-name {
            font-size: 22px;
            font-weight: bold;
            color: #007bff;
        }

        /* Card Style */
        .card {
            border: 1px solid #ddd;
            margin-bottom: 20px;
            padding: 15px;
            background-color: #fff;
            border-radius: 5px;
        }

        .card-header {
            background-color: #007bff;
            color: #fff;
            padding: 10px;
            font-weight: bold;
            text-align: center;
            font-size: 18px;
        }

        .card-body {
            padding: 10px;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 14px;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f7f7f7;
            font-weight: bold;
        }

        td {
            vertical-align: middle;
        }

        /* Fixed Width for Purchase Information Table */
        .fixed-width-table th, .fixed-width-table td {
            width: 20%;
        }

        /* Total and Summary Row Styling */
        .summary-row th {
            text-align: right;
        }

        .summary-row td {
            text-align: center;
            font-weight: bold;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }

        .section-title {
            font-size: 16px;
            margin-top: 20px;
            font-weight: bold;
            color: #333;
        }

        .notes-section {
            font-size: 14px;
            color: #555;
        }
    </style>
</head>
<body>

    @yield('content')
    <div class="footer">
        <p>Generated on {{ \Carbon\Carbon::now()->format('F j, Y') }} | Purchase Invoice</p>
    </div>
</body>
</html>
