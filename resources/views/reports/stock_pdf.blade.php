<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Inventario (Stock)</title>
    <style>
        @page {
            margin: 200px 30px 80px 30px;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #333;
        }

        /* Encabezado fijo en cada página */
        header {
            position: fixed;
            top: -180px;
            left: 0;
            right: 0;
            height: 160px;
        }

        /* Footer fijo en cada página */
        footer {
            position: fixed;
            bottom: -60px;
            left: 0;
            right: 0;
            height: 40px;
        }

        /* Tabla de info superior (department / rif) */
        .header-info {
            width: 100%;
            border: none;
            margin-bottom: 5px;
        }

        .header-info td {
            border: none;
            padding: 0;
            font-size: 11px;
        }

        .header-info .left {
            text-align: left;
            font-weight: bold;
        }

        .header-info .right {
            text-align: right;
        }

        /* Títulos centrados */
        .header-titles {
            text-align: center;
            margin-top: 10px;
        }

        .header-titles .title-report {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 6px;
        }

        .header-titles .subtitle-report {
            font-size: 12px;
            margin-bottom: 6px;
        }

        .header-titles .report-type {
            font-size: 12px;
            font-weight: bold;
            color: #1e3660;
        }

        .header-titles .subtitle-report {
            font-size: 11px;
        }

        /* Tabla de datos */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #444;
            padding: 5px 6px;
            vertical-align: middle;
        }

        .data-table th {
            background-color: #1e3660;
            color: #fff;
            font-size: 10px;
            text-align: center;
            font-weight: bold;
        }

        .data-table td {
            font-size: 9.5px;
        }

        .data-table td.text-center {
            text-align: center;
        }

        .text-danger {
            color: #dc3545;
            font-weight: bold;
        }

        .text-success {
            color: #198754;
            font-weight: bold;
        }

        /* Footer */
        .footer-table {
            width: 100%;
            border: none;
        }

        .footer-table td {
            border: none;
            padding: 0;
            font-size: 9px;
            color: #555;
        }

        .footer-table .left {
            text-align: left;
        }

        .footer-table .right {
            text-align: right;
        }
    </style>
</head>
<body>

    {{-- ENCABEZADO --}}
    <header>
        <table class="header-info">
            <tr>
                <td class="left">{{ $general->department ?? 'Departamento no configurado' }}</td>
                <td class="right">{{ $general->rif ?? 'RIF no configurado' }}</td>
            </tr>
        </table>

        <br><br>

        <div class="header-titles">
            {{-- 1. Título del reporte 1 (grande, negrita) --}}
            <div class="title-report">{{ $general->title_report_1 ?? 'Título no configurado' }}</div>

            {{-- 2. Subtítulo del reporte 1 (mediano) --}}
            <div class="subtitle-report">{{ $general->subtitle_report_1 ?? 'Subtítulo no configurado' }}</div>

            {{-- 3. Tipo de reporte seleccionado (mediano) --}}
            <div class="report-type">{{ $reportTitle }}</div>
        </div>
    </header>

    {{-- FOOTER --}}
    <footer>
        <table class="footer-table">
            <tr>
                <td class="left">{{ $general->footer ?? '' }}</td>
                <td class="right">{{ $fecha }}</td>
            </tr>
        </table>
    </footer>

    {{-- CONTENIDO --}}
    <main>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Equipo</th>
                    <th>SKU</th>
                    <th>Categoría</th>
                    <th>Marca/Modelo</th>
                    <th>Unidad</th>
                    <th>Stock</th>
                    <th>Almacén</th>
                    <th>Umbral Mínimo</th>
                    <th>Fecha Último Cambio</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $item)
                    <tr>
                        <td>{{ $item->equipment_name }}</td>
                        <td>{{ $item->sku }}</td>
                        <td>{{ $item->category_name ?? 'N/A' }}</td>
                        <td>{{ ($item->brand ?? 'N/A') . ' - ' . ($item->model ?? 'N/A') }}</td>
                        <td>{{ $item->unit_name ?? 'N/A' }}</td>
                        <td class="text-center {{ $item->stock <= $item->umbral ? 'text-danger' : 'text-success' }}">
                            {{ $item->stock }}
                        </td>
                        <td>{{ $item->store_name ?? 'N/A' }}</td>
                        <td class="text-center">{{ $item->umbral }}</td>
                        <td>
                            {{ $item->last_change ? \Carbon\Carbon::parse($item->last_change)->format('d/m/Y H:i') : '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center">No se encontraron registros</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </main>

</body>
</html>