<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Movimientos</title>
    <style>
        @page {
            margin: 200px 30px 80px 30px;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #333;
        }

        header {
            position: fixed;
            top: -180px;
            left: 0;
            right: 0;
            height: 160px;
        }

        footer {
            position: fixed;
            bottom: -60px;
            left: 0;
            right: 0;
            height: 40px;
        }

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

        .badge-compra { color: #198754; font-weight: bold; }
        .badge-salida { color: #dc3545; font-weight: bold; }
        .badge-traslado { color: #856404; font-weight: bold; }
        .badge-ajuste { color: #6c757d; font-weight: bold; }

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

    <header>
        <table class="header-info">
            <tr>
                <td class="left">{{ $general->department ?? 'Departamento no configurado' }}</td>
                <td class="right">{{ $general->rif ?? 'RIF no configurado' }}</td>
            </tr>
        </table>

        <br><br>

        <div class="header-titles">
            <div class="title-report">{{ $general->title_report_3 ?? 'Título no configurado' }}</div>
            <div class="subtitle-report">{{ $general->subtitle_report_3 ?? 'Subtítulo no configurado' }}</div>
            <div class="report-type">{{ $reportTitle }}</div>
        </div>
    </header>

    <footer>
        <table class="footer-table">
            <tr>
                <td class="left">{{ $general->footer ?? '' }}</td>
                <td class="right">{{ $fecha }}</td>
            </tr>
        </table>
    </footer>

    <main>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Fecha Movimiento</th>
                    <th>Tipo Movimiento</th>
                    <th>Descripción</th>
                    <th>Cantidad</th>
                    <th>Observación</th>
                    <th>Realizado por</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $item)
                    @php
                        $tipoId = $item->movement_type;
                        $tipoNombre = match($tipoId) {
                            1 => 'Compra',
                            2 => 'Salida',
                            3 => 'Traslado',
                            4 => 'Ajuste',
                            default => 'Desconocido',
                        };
                        $badgeClass = match($tipoId) {
                            1 => 'badge-compra',
                            2 => 'badge-salida',
                            3 => 'badge-traslado',
                            4 => 'badge-ajuste',
                            default => '',
                        };
                        $descripcion = match($tipoId) {
                            1 => 'Proveedor: ' . ($item->supplier_name ?? 'N/A') . ' - Almacén Destino: ' . ($item->destination_name ?? 'N/A'),
                            2 => 'Almacén Origen: ' . ($item->origin_name ?? 'N/A'),
                            3 => 'Origen: ' . ($item->origin_name ?? 'N/A') . ' - Destino: ' . ($item->destination_name ?? 'N/A'),
                            4 => 'En Almacén: ' . ($item->origin_name ?? 'N/A'),
                            default => '-',
                        };
                    @endphp
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') }}</td>
                        <td class="{{ $badgeClass }}">{{ $tipoNombre }}</td>
                        <td>{{ $descripcion }}</td>
                        <td class="text-center fw-bold">{{ $item->amount }}</td>
                        <td>{{ $item->obs ?? '-' }}</td>
                        <td>{{ $item->user_name ?? 'N/A' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No se encontraron registros</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </main>

</body>
</html>