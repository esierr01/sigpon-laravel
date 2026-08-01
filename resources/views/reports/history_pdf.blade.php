<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte Historial</title>
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
            margin-bottom: 4px;
        }

        .header-titles .date-range {
            font-size: 10px;
            font-style: italic;
            color: #666;
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

        .text-success {
            color: #198754;
            font-weight: bold;
        }

        .text-danger {
            color: #dc3545;
            font-weight: bold;
        }

        .text-muted {
            color: #6c757d;
        }

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
            <div class="title-report">{{ $general->title_report_4 ?? 'Título no configurado' }}</div>
            <div class="subtitle-report">{{ $general->subtitle_report_4 ?? 'Subtítulo no configurado' }}</div>
            <div class="report-type">{{ $reportTitle }}</div>
            <div class="date-range">
                Período: {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} -
                {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}
            </div>
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
        @if ($reportType === 'access')
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Fecha y Hora</th>
                        <th>Correo</th>
                        <th>Resultado</th>
                        <th>Observación</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $item)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i:s') }}</td>
                            <td>{{ $item->mail }}</td>
                            <td class="{{ $item->result ? 'text-success' : 'text-danger' }}">
                                {{ $item->result ? 'Exitoso' : 'Fallido' }}
                            </td>
                            <td>{{ $item->obs ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No se encontraron registros</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Fecha y Hora</th>
                        <th>Usuario</th>
                        <th>Tabla</th>
                        <th>Dirección IP</th>
                        <th>Observación</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $item)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i:s') }}</td>
                            <td>
                                @if ($item->user)
                                    <span class="fw-bold">{{ $item->user->name }}</span>
                                @else
                                    <span class="text-muted">Sistema / Eliminado</span>
                                @endif
                            </td>
                            <td><span class="text-success fw-bold">{{ $item->table }}</span></td>
                            <td><code>{{ $item->ip }}</code></td>
                            <td>{{ $item->obs }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No se encontraron registros</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @endif
    </main>

</body>

</html>
