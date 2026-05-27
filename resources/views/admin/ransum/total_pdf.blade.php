<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Total Ransum – {{ $upload->vessel_name ?? $upload->original_filename }}</title>
    <style>
        @page { size: A4 landscape; margin: 15px; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #000; line-height: 1.3; background: white; padding: 15px; }
        table { width: 100%; border-collapse: collapse; font-size: 11px; }
        th, td { border: 1px solid black; padding: 4px; }
    </style>
</head>
<body>
    {!! $htmlContent !!}
</body>
</html>
