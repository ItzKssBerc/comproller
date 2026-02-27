<style>
    @page {
        size: A4;
        margin: 15mm;
    }

    body {
        font-family: 'DejaVu Sans', sans-serif;
        font-size: 10pt;
        color: #1a1a1a;
        line-height: 1.4;
        margin: 0;
        padding: 0;
        background: white;
    }

    .page-break {
        page-break-after: always;
    }

    .container {
        width: 100%;
        height: 100%;
    }

    /* Header Styles */
    .header {
        border-bottom: 1px solid #e5e7eb;
        padding-bottom: 16px;
        margin-bottom: 24px;
    }

    .header h1 {
        font-size: 18pt;
        font-weight: bold;
        margin: 0;
        color: #000;
    }

    .header p {
        margin: 4px 0 0 0;
        color: #4b5563;
        font-size: 10pt;
    }

    /* Info Grid (Simulating 3 columns with table) */
    .info-table {
        width: 100%;
        margin-bottom: 24px;
    }

    .info-table td {
        width: 33.33%;
        vertical-align: top;
    }

    /* Table Styles */
    .data-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    .data-table th {
        background-color: #f3f4f6;
        border: 1px solid #e5e7eb;
        padding: 8px;
        text-align: left;
        font-weight: bold;
        font-size: 9pt;
        text-transform: uppercase;
    }

    .data-table td {
        border: 1px solid #e5e7eb;
        padding: 8px;
        font-size: 9pt;
    }

    .text-right {
        text-align: right;
    }

    .text-left {
        text-align: left;
    }

    .text-center {
        text-align: center;
    }

    .font-bold {
        font-weight: bold;
    }

    .font-semibold {
        font-weight: bold;
    }

    /* Summary Section (ALWAYS BOTTOM) */
    .summary-wrapper {
        margin-top: 40px;
        border-top: 1px solid #e5e7eb;
        padding-top: 16px;
        width: 100%;
    }

    .summary-table {
        width: 100%;
    }

    .summary-table td {
        width: 50%;
        vertical-align: top;
    }

    .text-lg {
        font-size: 12pt;
    }

    .mt-2 {
        margin-top: 8px;
    }

    .copy-badge {
        display: inline-block;
        padding: 2px 8px;
        background: #f3f4f6;
        color: #4b5563;
        font-size: 8pt;
        font-weight: bold;
        border-radius: 4px;
        margin-top: 4px;
    }
</style>