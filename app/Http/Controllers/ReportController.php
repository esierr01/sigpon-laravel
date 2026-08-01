<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use App\Models\Inventory;
use App\Models\Equipment;
use App\Models\Category;
use App\Models\BrandModel;
use App\Models\Unit;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\Movement;
use App\Models\User;
use App\Models\Role;
use App\Models\General;
use App\Models\LogAccess;
use App\Models\LogChange;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Reporte de Inventario (Stock) - Vista web
     */
    public function stock(Request $request)
    {
        $data = collect();
        $reportType = $request->input('report_type');

        if ($request->has('generate') && $reportType) {
            $query = Inventory::join('equipment', 'inventory.equipment_id', '=', 'equipment.id')
                ->leftJoin('categories', 'equipment.category_id', '=', 'categories.id')
                ->leftJoin('brand_models', 'equipment.brand_model_id', '=', 'brand_models.id')
                ->leftJoin('units', 'equipment.unit_id', '=', 'units.id')
                ->leftJoin('stores', 'inventory.store_id', '=', 'stores.id')
                ->select(
                    'inventory.id',
                    'inventory.stock',
                    'inventory.last_change',
                    'equipment.sku',
                    'equipment.name as equipment_name',
                    'equipment.umbral',
                    'categories.name as category_name',
                    'brand_models.brand',
                    'brand_models.model',
                    'units.name as unit_name',
                    'stores.name as store_name'
                );

            switch ($reportType) {
                case 'available_by_equipment':
                    $query->where('inventory.stock', '>', 0)
                        ->orderBy('equipment.name', 'asc');
                    break;

                case 'available_by_store':
                    $query->where('inventory.stock', '>', 0)
                        ->orderBy('stores.name', 'asc')
                        ->orderBy('equipment.name', 'asc');
                    break;

                case 'unavailable_by_equipment':
                    $query->where('inventory.stock', '=', 0)
                        ->orderBy('equipment.name', 'asc');
                    break;

                default:
                    $query->where('inventory.stock', '>', 0)
                        ->orderBy('equipment.name', 'asc');
            }

            $data = $query->get();
        }

        return view('reports.stock', compact('data', 'reportType'));
    }

    /**
     * Reporte de Inventario (Stock) - PDF
     */
    public function stockPdf(Request $request)
    {
        $reportType = $request->input('report_type', 'available_by_equipment');

        // Mapeo del tipo a nombre legible
        $reportTitles = [
            'available_by_equipment' => 'Equipamiento disponible (ordenado por equipo)',
            'available_by_store' => 'Equipamiento disponible (Ordenado por Almacén)',
            'unavailable_by_equipment' => 'Equipamiento no disponible (Ordenado por equipo)',
        ];
        $reportTitle = $reportTitles[$reportType] ?? 'Reporte de Stock';

        // Construir la misma query del reporte web
        $query = Inventory::join('equipment', 'inventory.equipment_id', '=', 'equipment.id')
            ->leftJoin('categories', 'equipment.category_id', '=', 'categories.id')
            ->leftJoin('brand_models', 'equipment.brand_model_id', '=', 'brand_models.id')
            ->leftJoin('units', 'equipment.unit_id', '=', 'units.id')
            ->leftJoin('stores', 'inventory.store_id', '=', 'stores.id')
            ->select(
                'inventory.id',
                'inventory.stock',
                'inventory.last_change',
                'equipment.sku',
                'equipment.name as equipment_name',
                'equipment.umbral',
                'categories.name as category_name',
                'brand_models.brand',
                'brand_models.model',
                'units.name as unit_name',
                'stores.name as store_name'
            );

        switch ($reportType) {
            case 'available_by_equipment':
                $query->where('inventory.stock', '>', 0)->orderBy('equipment.name', 'asc');
                break;
            case 'available_by_store':
                $query->where('inventory.stock', '>', 0)->orderBy('stores.name', 'asc')->orderBy('equipment.name', 'asc');
                break;
            case 'unavailable_by_equipment':
                $query->where('inventory.stock', '=', 0)->orderBy('equipment.name', 'asc');
                break;
            default:
                $query->where('inventory.stock', '>', 0)->orderBy('equipment.name', 'asc');
        }

        $data = $query->get();

        // Datos del encabezado (tabla general)
        $general = General::first();
        $general = General::first();
        $fecha = Carbon::now()->format('d/m/Y');

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('reports.stock_pdf', compact('data', 'general', 'fecha', 'reportType', 'reportTitle'))
            ->setPaper('letter', 'landscape');

        return $pdf->stream('reporte_stock_' . Carbon::now()->format('Ymd_His') . '.pdf');
    }

    /**
     * Reporte de Inventario (Stock) - Excel
     */
    public function stockExcel(Request $request)
    {
        $reportType = $request->input('report_type', 'available_by_equipment');

        $reportTitles = [
            'available_by_equipment' => 'Equipamiento disponible (ordenado por equipo)',
            'available_by_store' => 'Equipamiento disponible (Ordenado por Almacén)',
            'unavailable_by_equipment' => 'Equipamiento no disponible (Ordenado por equipo)',
        ];
        $reportTitle = $reportTitles[$reportType] ?? 'Reporte de Stock';

        // Query igual que en stockPdf
        $query = Inventory::join('equipment', 'inventory.equipment_id', '=', 'equipment.id')
            ->leftJoin('categories', 'equipment.category_id', '=', 'categories.id')
            ->leftJoin('brand_models', 'equipment.brand_model_id', '=', 'brand_models.id')
            ->leftJoin('units', 'equipment.unit_id', '=', 'units.id')
            ->leftJoin('stores', 'inventory.store_id', '=', 'stores.id')
            ->select(
                'inventory.id',
                'inventory.stock',
                'inventory.last_change',
                'equipment.sku',
                'equipment.name as equipment_name',
                'equipment.umbral',
                'categories.name as category_name',
                'brand_models.brand',
                'brand_models.model',
                'units.name as unit_name',
                'stores.name as store_name'
            );

        switch ($reportType) {
            case 'available_by_equipment':
                $query->where('inventory.stock', '>', 0)->orderBy('equipment.name', 'asc');
                break;
            case 'available_by_store':
                $query->where('inventory.stock', '>', 0)->orderBy('stores.name', 'asc')->orderBy('equipment.name', 'asc');
                break;
            case 'unavailable_by_equipment':
                $query->where('inventory.stock', '=', 0)->orderBy('equipment.name', 'asc');
                break;
            default:
                $query->where('inventory.stock', '>', 0)->orderBy('equipment.name', 'asc');
        }

        $data = $query->get();
        $general = General::first();
        $fecha = Carbon::now()->format('d/m/Y');

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Estilos
        $styleHeader = [
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3660']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ];

        $styleTitle = [
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];

        $styleSubtitle = [
            'font' => ['size' => 11],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];

        $styleType = [
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '1E3660']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];

        $styleFooter = [
            'font' => ['size' => 9, 'color' => ['rgb' => '555555']],
        ];

        // Ancho de columnas
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(18);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(28);
        $sheet->getColumnDimension('E')->setWidth(14);
        $sheet->getColumnDimension('F')->setWidth(12);
        $sheet->getColumnDimension('G')->setWidth(22);
        $sheet->getColumnDimension('H')->setWidth(16);
        $sheet->getColumnDimension('I')->setWidth(20);

        // Fila 1: Department (izq) | RIF (der)
        $sheet->setCellValue('A1', $general->department ?? 'Departamento no configurado');
        $sheet->setCellValue('I1', $general->rif ?? 'RIF no configurado');
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('I1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

        // Fila 3: Título
        $sheet->mergeCells('A3:I3');
        $sheet->setCellValue('A3', $general->title_report_1 ?? 'Título no configurado');
        $sheet->getStyle('A3')->applyFromArray($styleTitle);

        // Fila 4: Subtítulo
        $sheet->mergeCells('A4:I4');
        $sheet->setCellValue('A4', $general->subtitle_report_1 ?? 'Subtítulo no configurado');
        $sheet->getStyle('A4')->applyFromArray($styleSubtitle);

        // Fila 5: Tipo de reporte
        $sheet->mergeCells('A5:I5');
        $sheet->setCellValue('A5', $reportTitle);
        $sheet->getStyle('A5')->applyFromArray($styleType);


        // ... (dentro del método stockExcel, antes del bloque de headers) ...

        // Fila 7: Headers de tabla
        $headers = ['Equipo', 'SKU', 'Categoría', 'Marca/Modelo', 'Unidad', 'Stock', 'Almacén', 'Umbral Mínimo', 'Fecha Último Cambio'];
        $col = 1;
        foreach ($headers as $header) {
            $colLetter = Coordinate::stringFromColumnIndex($col);
            $coordinate = $colLetter . '7';

            $sheet->setCellValue($coordinate, $header);
            $sheet->getStyle($coordinate)->applyFromArray($styleHeader);
            $col++;
        }

        // Datos desde fila 8
        $row = 8;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $item->equipment_name);
            $sheet->setCellValue('B' . $row, $item->sku);
            $sheet->setCellValue('C' . $row, $item->category_name ?? 'N/A');
            $sheet->setCellValue('D' . $row, ($item->brand ?? 'N/A') . ' - ' . ($item->model ?? 'N/A'));
            $sheet->setCellValue('E' . $row, $item->unit_name ?? 'N/A');
            $sheet->setCellValue('F' . $row, $item->stock);
            $sheet->setCellValue('G' . $row, $item->store_name ?? 'N/A');
            $sheet->setCellValue('H' . $row, $item->umbral);
            $sheet->setCellValue('I' . $row, $item->last_change ? Carbon::parse($item->last_change)->format('d/m/Y H:i') : '-');

            // Color condicional para stock
            if ($item->stock <= $item->umbral) {
                $sheet->getStyle('F' . $row)->getFont()->getColor()->setRGB('DC3545');
                $sheet->getStyle('F' . $row)->getFont()->setBold(true);
            } else {
                $sheet->getStyle('F' . $row)->getFont()->getColor()->setRGB('198754');
            }

            // Bordes
            $sheet->getStyle('A' . $row . ':I' . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            $row++;
        }

        // Alinear columnas
        $sheet->getStyle('F8:F' . ($row - 1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('H8:H' . ($row - 1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Footer: última fila + 2
        $footerRow = $row + 2;
        $sheet->setCellValue('A' . $footerRow, $general->footer ?? '');
        $sheet->getStyle('A' . $footerRow)->applyFromArray($styleFooter);
        $sheet->setCellValue('I' . $footerRow, $fecha);
        $sheet->getStyle('I' . $footerRow)->applyFromArray($styleFooter);
        $sheet->getStyle('I' . $footerRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

        // Descargar
        $filename = 'reporte_stock_' . Carbon::now()->format('Ymd_His') . '.xlsx';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Reporte de Inventario (Bajo Stock) - Vista web
     */
    public function lowStock(Request $request)
    {
        $data = collect();
        $reportType = $request->input('report_type');

        if ($request->has('generate') && $reportType) {
            $query = Inventory::join('equipment', 'inventory.equipment_id', '=', 'equipment.id')
                ->leftJoin('categories', 'equipment.category_id', '=', 'categories.id')
                ->leftJoin('brand_models', 'equipment.brand_model_id', '=', 'brand_models.id')
                ->leftJoin('units', 'equipment.unit_id', '=', 'units.id')
                ->leftJoin('stores', 'inventory.store_id', '=', 'stores.id')
                ->whereColumn('inventory.stock', '<=', 'equipment.umbral')
                ->select(
                    'inventory.id',
                    'inventory.stock',
                    'inventory.last_change',
                    'equipment.sku',
                    'equipment.name as equipment_name',
                    'equipment.umbral',
                    'categories.name as category_name',
                    'brand_models.brand',
                    'brand_models.model',
                    'units.name as unit_name',
                    'stores.name as store_name'
                );

            switch ($reportType) {
                case 'low_by_equipment':
                    $query->orderBy('equipment.name', 'asc');
                    break;

                case 'low_by_store':
                    $query->orderBy('stores.name', 'asc')
                        ->orderBy('equipment.name', 'asc');
                    break;

                default:
                    $query->orderBy('equipment.name', 'asc');
            }

            $data = $query->get();
        }

        return view('reports.low_stock', compact('data', 'reportType'));
    }

    /**
     * Reporte de Inventario (Bajo Stock) - PDF
     */
    public function lowStockPdf(Request $request)
    {
        $reportType = $request->input('report_type', 'low_by_equipment');

        $reportTitles = [
            'low_by_equipment' => 'Equipamiento Bajo Stock (ordenado por equipo)',
            'low_by_store' => 'Equipamiento Bajo Stock (Ordenado por Almacén)',
        ];
        $reportTitle = $reportTitles[$reportType] ?? 'Reporte de Bajo Stock';

        $query = Inventory::join('equipment', 'inventory.equipment_id', '=', 'equipment.id')
            ->leftJoin('categories', 'equipment.category_id', '=', 'categories.id')
            ->leftJoin('brand_models', 'equipment.brand_model_id', '=', 'brand_models.id')
            ->leftJoin('units', 'equipment.unit_id', '=', 'units.id')
            ->leftJoin('stores', 'inventory.store_id', '=', 'stores.id')
            ->whereColumn('inventory.stock', '<=', 'equipment.umbral')
            ->select(
                'inventory.id',
                'inventory.stock',
                'inventory.last_change',
                'equipment.sku',
                'equipment.name as equipment_name',
                'equipment.umbral',
                'categories.name as category_name',
                'brand_models.brand',
                'brand_models.model',
                'units.name as unit_name',
                'stores.name as store_name'
            );

        switch ($reportType) {
            case 'low_by_equipment':
                $query->orderBy('equipment.name', 'asc');
                break;
            case 'low_by_store':
                $query->orderBy('stores.name', 'asc')->orderBy('equipment.name', 'asc');
                break;
            default:
                $query->orderBy('equipment.name', 'asc');
        }

        $data = $query->get();
        $general = General::first();
        $fecha = Carbon::now()->format('d/m/Y');

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('reports.low_stock_pdf', compact('data', 'general', 'fecha', 'reportType', 'reportTitle'))
            ->setPaper('letter', 'landscape');

        return $pdf->stream('reporte_bajo_stock_' . Carbon::now()->format('Ymd_His') . '.pdf');
    }

    /**
     * Reporte de Inventario (Bajo Stock) - Excel
     */
    public function lowStockExcel(Request $request)
    {
        $reportType = $request->input('report_type', 'low_by_equipment');

        $reportTitles = [
            'low_by_equipment' => 'Equipamiento Bajo Stock (ordenado por equipo)',
            'low_by_store' => 'Equipamiento Bajo Stock (Ordenado por Almacén)',
        ];
        $reportTitle = $reportTitles[$reportType] ?? 'Reporte de Bajo Stock';

        $query = Inventory::join('equipment', 'inventory.equipment_id', '=', 'equipment.id')
            ->leftJoin('categories', 'equipment.category_id', '=', 'categories.id')
            ->leftJoin('brand_models', 'equipment.brand_model_id', '=', 'brand_models.id')
            ->leftJoin('units', 'equipment.unit_id', '=', 'units.id')
            ->leftJoin('stores', 'inventory.store_id', '=', 'stores.id')
            ->whereColumn('inventory.stock', '<=', 'equipment.umbral')
            ->select(
                'inventory.id',
                'inventory.stock',
                'inventory.last_change',
                'equipment.sku',
                'equipment.name as equipment_name',
                'equipment.umbral',
                'categories.name as category_name',
                'brand_models.brand',
                'brand_models.model',
                'units.name as unit_name',
                'stores.name as store_name'
            );

        switch ($reportType) {
            case 'low_by_equipment':
                $query->orderBy('equipment.name', 'asc');
                break;
            case 'low_by_store':
                $query->orderBy('stores.name', 'asc')->orderBy('equipment.name', 'asc');
                break;
            default:
                $query->orderBy('equipment.name', 'asc');
        }

        $data = $query->get();
        $general = General::first();
        $fecha = Carbon::now()->format('d/m/Y');

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $styleHeader = [
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3660']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ];

        $styleTitle = [
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];

        $styleSubtitle = [
            'font' => ['size' => 11],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];

        $styleType = [
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '1E3660']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];

        $styleFooter = [
            'font' => ['size' => 9, 'color' => ['rgb' => '555555']],
        ];

        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(18);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(28);
        $sheet->getColumnDimension('E')->setWidth(14);
        $sheet->getColumnDimension('F')->setWidth(12);
        $sheet->getColumnDimension('G')->setWidth(22);
        $sheet->getColumnDimension('H')->setWidth(16);
        $sheet->getColumnDimension('I')->setWidth(20);

        $sheet->setCellValue('A1', $general->department ?? 'Departamento no configurado');
        $sheet->setCellValue('I1', $general->rif ?? 'RIF no configurado');
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('I1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

        $sheet->mergeCells('A3:I3');
        $sheet->setCellValue('A3', $general->title_report_2 ?? 'Título no configurado');
        $sheet->getStyle('A3')->applyFromArray($styleTitle);

        $sheet->mergeCells('A4:I4');
        $sheet->setCellValue('A4', $general->subtitle_report_2 ?? 'Subtítulo no configurado');
        $sheet->getStyle('A4')->applyFromArray($styleSubtitle);

        $sheet->mergeCells('A5:I5');
        $sheet->setCellValue('A5', $reportTitle);
        $sheet->getStyle('A5')->applyFromArray($styleType);

        $headers = ['Equipo', 'SKU', 'Categoría', 'Marca/Modelo', 'Unidad', 'Stock', 'Almacén', 'Umbral Mínimo', 'Fecha Último Cambio'];
        $col = 1;
        foreach ($headers as $header) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            $coordinate = $colLetter . '7';
            $sheet->setCellValue($coordinate, $header);
            $sheet->getStyle($coordinate)->applyFromArray($styleHeader);
            $col++;
        }

        $row = 8;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $item->equipment_name);
            $sheet->setCellValue('B' . $row, $item->sku);
            $sheet->setCellValue('C' . $row, $item->category_name ?? 'N/A');
            $sheet->setCellValue('D' . $row, ($item->brand ?? 'N/A') . ' - ' . ($item->model ?? 'N/A'));
            $sheet->setCellValue('E' . $row, $item->unit_name ?? 'N/A');
            $sheet->setCellValue('F' . $row, $item->stock);
            $sheet->setCellValue('G' . $row, $item->store_name ?? 'N/A');
            $sheet->setCellValue('H' . $row, $item->umbral);
            $sheet->setCellValue('I' . $row, $item->last_change ? Carbon::parse($item->last_change)->format('d/m/Y H:i') : '-');

            $sheet->getStyle('F' . $row)->getFont()->getColor()->setRGB('DC3545');
            $sheet->getStyle('F' . $row)->getFont()->setBold(true);

            $sheet->getStyle('A' . $row . ':I' . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $row++;
        }

        $sheet->getStyle('F8:F' . ($row - 1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('H8:H' . ($row - 1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $footerRow = $row + 2;
        $sheet->setCellValue('A' . $footerRow, $general->footer ?? '');
        $sheet->getStyle('A' . $footerRow)->applyFromArray($styleFooter);
        $sheet->setCellValue('I' . $footerRow, $fecha);
        $sheet->getStyle('I' . $footerRow)->applyFromArray($styleFooter);
        $sheet->getStyle('I' . $footerRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

        $filename = 'reporte_bajo_stock_' . Carbon::now()->format('Ymd_His') . '.xlsx';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Reporte de Movimientos - Vista web
     */
    public function movements(Request $request)
    {
        $data = collect();
        $reportType = $request->input('report_type');

        if ($request->has('generate') && $reportType) {
            $query = Movement::join('equipment', 'movements.equipment_id', '=', 'equipment.id')
                ->leftJoin('suppliers', 'movements.supplier_id', '=', 'suppliers.id')
                ->leftJoin('stores as origin', 'movements.origin_id', '=', 'origin.id')
                ->leftJoin('stores as destination', 'movements.destination_id', '=', 'destination.id')
                ->leftJoin('users', 'movements.user_id', '=', 'users.id')
                ->select(
                    'movements.id',
                    'movements.movement_type',
                    'movements.amount',
                    'movements.obs',
                    'movements.created_at',
                    'equipment.name as equipment_name',
                    'suppliers.name as supplier_name',
                    'origin.name as origin_name',
                    'destination.name as destination_name',
                    'users.name as user_name'
                );

            switch ($reportType) {
                case 'movements_by_date':
                    $query->orderBy('movements.created_at', 'desc');
                    break;

                case 'movements_by_type':
                    $query->orderBy('movements.movement_type', 'asc')
                        ->orderBy('movements.created_at', 'desc');
                    break;

                default:
                    $query->orderBy('movements.created_at', 'desc');
            }

            $data = $query->get();
        }

        return view('reports.movements', compact('data', 'reportType'));
    }

    /**
     * Reporte de Movimientos - PDF
     */
    public function movementsPdf(Request $request)
    {
        $reportType = $request->input('report_type', 'movements_by_date');

        $reportTitles = [
            'movements_by_date' => 'Movimientos (ordenado por fecha)',
            'movements_by_type' => 'Movimientos (Ordenado por Tipo de Movimiento)',
        ];
        $reportTitle = $reportTitles[$reportType] ?? 'Reporte de Movimientos';

        $query = Movement::join('equipment', 'movements.equipment_id', '=', 'equipment.id')
            ->leftJoin('suppliers', 'movements.supplier_id', '=', 'suppliers.id')
            ->leftJoin('stores as origin', 'movements.origin_id', '=', 'origin.id')
            ->leftJoin('stores as destination', 'movements.destination_id', '=', 'destination.id')
            ->leftJoin('users', 'movements.user_id', '=', 'users.id')
            ->select(
                'movements.id',
                'movements.movement_type',
                'movements.amount',
                'movements.obs',
                'movements.created_at',
                'equipment.name as equipment_name',
                'suppliers.name as supplier_name',
                'origin.name as origin_name',
                'destination.name as destination_name',
                'users.name as user_name'
            );

        switch ($reportType) {
            case 'movements_by_date':
                $query->orderBy('movements.created_at', 'desc');
                break;

            case 'movements_by_type':
                $query->orderBy('movements.movement_type', 'asc')
                    ->orderBy('movements.created_at', 'desc');
                break;

            default:
                $query->orderBy('movements.created_at', 'desc');
        }

        $data = $query->get();
        $general = General::first();
        $fecha = Carbon::now()->format('d/m/Y');

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('reports.movements_pdf', compact('data', 'general', 'fecha', 'reportType', 'reportTitle'))
            ->setPaper('letter', 'landscape');

        return $pdf->stream('reporte_movimientos_' . Carbon::now()->format('Ymd_His') . '.pdf');
    }

    /**
     * Reporte de Movimientos - Excel
     */
    public function movementsExcel(Request $request)
    {
        $reportType = $request->input('report_type', 'movements_by_date');

        $reportTitles = [
            'movements_by_date' => 'Movimientos (ordenado por fecha)',
            'movements_by_type' => 'Movimientos (Ordenado por Tipo de Movimiento)',
        ];
        $reportTitle = $reportTitles[$reportType] ?? 'Reporte de Movimientos';

        $query = Movement::join('equipment', 'movements.equipment_id', '=', 'equipment.id')
            ->leftJoin('suppliers', 'movements.supplier_id', '=', 'suppliers.id')
            ->leftJoin('stores as origin', 'movements.origin_id', '=', 'origin.id')
            ->leftJoin('stores as destination', 'movements.destination_id', '=', 'destination.id')
            ->leftJoin('users', 'movements.user_id', '=', 'users.id')
            ->select(
                'movements.id',
                'movements.movement_type',
                'movements.amount',
                'movements.obs',
                'movements.created_at',
                'equipment.name as equipment_name',
                'suppliers.name as supplier_name',
                'origin.name as origin_name',
                'destination.name as destination_name',
                'users.name as user_name'
            );

        switch ($reportType) {
            case 'movements_by_date':
                $query->orderBy('movements.created_at', 'desc');
                break;

            case 'movements_by_type':
                $query->orderBy('movements.movement_type', 'asc')
                    ->orderBy('movements.created_at', 'desc');
                break;

            default:
                $query->orderBy('movements.created_at', 'desc');
        }

        $data = $query->get();
        $general = General::first();
        $fecha = Carbon::now()->format('d/m/Y');

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $styleHeader = [
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3660']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ];

        $styleTitle = [
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];

        $styleSubtitle = [
            'font' => ['size' => 11],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];

        $styleType = [
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '1E3660']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];

        $styleFooter = [
            'font' => ['size' => 9, 'color' => ['rgb' => '555555']],
        ];

        $sheet->getColumnDimension('A')->setWidth(18);
        $sheet->getColumnDimension('B')->setWidth(16);
        $sheet->getColumnDimension('C')->setWidth(45);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('E')->setWidth(35);
        $sheet->getColumnDimension('F')->setWidth(22);

        $sheet->setCellValue('A1', $general->department ?? 'Departamento no configurado');
        $sheet->setCellValue('F1', $general->rif ?? 'RIF no configurado');
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('F1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

        $sheet->mergeCells('A3:F3');
        $sheet->setCellValue('A3', $general->title_report_3 ?? 'Título no configurado');
        $sheet->getStyle('A3')->applyFromArray($styleTitle);

        $sheet->mergeCells('A4:F4');
        $sheet->setCellValue('A4', $general->subtitle_report_3 ?? 'Subtítulo no configurado');
        $sheet->getStyle('A4')->applyFromArray($styleSubtitle);

        $sheet->mergeCells('A5:F5');
        $sheet->setCellValue('A5', $reportTitle);
        $sheet->getStyle('A5')->applyFromArray($styleType);

        $headers = ['Fecha Movimiento', 'Tipo Movimiento', 'Descripción', 'Cantidad', 'Observación', 'Realizado por'];
        $col = 1;
        foreach ($headers as $header) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            $coordinate = $colLetter . '7';
            $sheet->setCellValue($coordinate, $header);
            $sheet->getStyle($coordinate)->applyFromArray($styleHeader);
            $col++;
        }

        $row = 8;
        foreach ($data as $item) {
            $descripcion = match ((int)$item->movement_type) {
                1 => 'Proveedor: ' . ($item->supplier_name ?? 'N/A') . ' - Almacén Destino: ' . ($item->destination_name ?? 'N/A'),
                2 => 'Almacén Origen: ' . ($item->origin_name ?? 'N/A'),
                3 => 'Origen: ' . ($item->origin_name ?? 'N/A') . ' - Destino: ' . ($item->destination_name ?? 'N/A'),
                4 => 'En Almacén: ' . ($item->origin_name ?? 'N/A'),
                default => '-',
            };

            $tipoNombre = match ((int)$item->movement_type) {
                1 => 'Compra',
                2 => 'Salida',
                3 => 'Traslado',
                4 => 'Ajuste',
                default => 'Desconocido',
            };

            $sheet->setCellValue('A' . $row, Carbon::parse($item->created_at)->format('d/m/Y H:i'));
            $sheet->setCellValue('B' . $row, $tipoNombre);
            $sheet->setCellValue('C' . $row, $descripcion);
            $sheet->setCellValue('D' . $row, $item->amount);
            $sheet->setCellValue('E' . $row, $item->obs ?? '-');
            $sheet->setCellValue('F' . $row, $item->user_name ?? 'N/A');

            // Color según tipo
            $color = match ((int)$item->movement_type) {
                1 => '198754',
                2 => 'DC3545',
                3 => 'FFC107',
                4 => '6C757D',
                default => '000000',
            };
            $sheet->getStyle('B' . $row)->getFont()->getColor()->setRGB($color);
            $sheet->getStyle('B' . $row)->getFont()->setBold(true);

            $sheet->getStyle('A' . $row . ':F' . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $row++;
        }

        $sheet->getStyle('D8:D' . ($row - 1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $footerRow = $row + 2;
        $sheet->setCellValue('A' . $footerRow, $general->footer ?? '');
        $sheet->getStyle('A' . $footerRow)->applyFromArray($styleFooter);
        $sheet->setCellValue('F' . $footerRow, $fecha);
        $sheet->getStyle('F' . $footerRow)->applyFromArray($styleFooter);
        $sheet->getStyle('F' . $footerRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

        $filename = 'reporte_movimientos_' . Carbon::now()->format('Ymd_His') . '.xlsx';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Reporte de Historial - Vista web
     */
    public function history(Request $request)
    {
        $data = collect();
        $reportType = $request->input('report_type');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        if ($request->has('generate') && $reportType) {
            $request->validate([
                'report_type' => 'required|in:access,changes',
                'date_from'   => 'required|date',
                'date_to'     => 'required|date|after_or_equal:date_from',
            ]);

            $from = Carbon::parse($dateFrom)->startOfDay();
            $to   = Carbon::parse($dateTo)->endOfDay();

            if ($reportType === 'access') {
                $data = LogAccess::whereBetween('created_at', [$from, $to])
                    ->orderBy('created_at', 'desc')
                    ->get();
            } else {
                $data = LogChange::with('user')
                    ->whereBetween('created_at', [$from, $to])
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
        }

        return view('reports.history', compact('data', 'reportType', 'dateFrom', 'dateTo'));
    }

    /**
     * Reporte de Historial - PDF
     */
    public function historyPdf(Request $request)
    {
        $reportType = $request->input('report_type', 'access');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $reportTitles = [
            'access'   => 'Accesos (ordenado por fecha)',
            'changes'  => 'Cambios (Ordenado por Fecha)',
        ];
        $reportTitle = $reportTitles[$reportType] ?? 'Reporte de Historial';

        $from = Carbon::parse($dateFrom)->startOfDay();
        $to   = Carbon::parse($dateTo)->endOfDay();

        if ($reportType === 'access') {
            $data = LogAccess::whereBetween('created_at', [$from, $to])
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $data = LogChange::with('user')
                ->whereBetween('created_at', [$from, $to])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        $general = General::first();
        $fecha = Carbon::now()->format('d/m/Y');

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('reports.history_pdf', compact('data', 'general', 'fecha', 'reportType', 'reportTitle', 'dateFrom', 'dateTo'))
            ->setPaper('letter', 'landscape');

        return $pdf->stream('reporte_historial_' . Carbon::now()->format('Ymd_His') . '.pdf');
    }

    /**
     * Reporte de Historial - Excel
     */
    public function historyExcel(Request $request)
    {
        $reportType = $request->input('report_type', 'access');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $reportTitles = [
            'access'   => 'Accesos (ordenado por fecha)',
            'changes'  => 'Cambios (Ordenado por Fecha)',
        ];
        $reportTitle = $reportTitles[$reportType] ?? 'Reporte de Historial';

        $from = Carbon::parse($dateFrom)->startOfDay();
        $to   = Carbon::parse($dateTo)->endOfDay();

        if ($reportType === 'access') {
            $data = LogAccess::whereBetween('created_at', [$from, $to])
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $data = LogChange::with('user')
                ->whereBetween('created_at', [$from, $to])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        $general = General::first();
        $fecha = Carbon::now()->format('d/m/Y');

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $styleHeader = [
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3660']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ];

        $styleTitle = [
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];

        $styleSubtitle = [
            'font' => ['size' => 11],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];

        $styleType = [
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '1E3660']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];

        $styleFooter = [
            'font' => ['size' => 9, 'color' => ['rgb' => '555555']],
        ];

        $sheet->setCellValue('A1', $general->department ?? 'Departamento no configurado');
        $sheet->getStyle('A1')->getFont()->setBold(true);

        $sheet->mergeCells('A3:E3');
        $sheet->setCellValue('A3', $general->title_report_4 ?? 'Título no configurado');
        $sheet->getStyle('A3')->applyFromArray($styleTitle);

        $sheet->mergeCells('A4:E4');
        $sheet->setCellValue('A4', $general->subtitle_report_4 ?? 'Subtítulo no configurado');
        $sheet->getStyle('A4')->applyFromArray($styleSubtitle);

        $sheet->mergeCells('A5:E5');
        $sheet->setCellValue('A5', $reportTitle);
        $sheet->getStyle('A5')->applyFromArray($styleType);

        $sheet->setCellValue('A6', 'Desde: ' . Carbon::parse($dateFrom)->format('d/m/Y') . '  Hasta: ' . Carbon::parse($dateTo)->format('d/m/Y'));
        $sheet->mergeCells('A6:E6');
        $sheet->getStyle('A6')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A6')->getFont()->setItalic(true)->setSize(10);

        if ($reportType === 'access') {
            $sheet->getColumnDimension('A')->setWidth(20);
            $sheet->getColumnDimension('B')->setWidth(30);
            $sheet->getColumnDimension('C')->setWidth(16);
            $sheet->getColumnDimension('D')->setWidth(45);

            $headers = ['Fecha y Hora', 'Correo', 'Resultado', 'Observación'];
            $lastCol = 'D';
        } else {
            $sheet->getColumnDimension('A')->setWidth(20);
            $sheet->getColumnDimension('B')->setWidth(25);
            $sheet->getColumnDimension('C')->setWidth(20);
            $sheet->getColumnDimension('D')->setWidth(16);
            $sheet->getColumnDimension('E')->setWidth(50);

            $headers = ['Fecha y Hora', 'Usuario', 'Tabla', 'Dirección IP', 'Observación'];
            $lastCol = 'E';
        }

        // RIF a la derecha (fila 1)
        $sheet->setCellValue($lastCol . '1', $general->rif ?? 'RIF no configurado');
        $sheet->getStyle($lastCol . '1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

        $col = 1;
        foreach ($headers as $header) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            $coordinate = $colLetter . '8';
            $sheet->setCellValue($coordinate, $header);
            $sheet->getStyle($coordinate)->applyFromArray($styleHeader);
            $col++;
        }

        $row = 9;
        if ($reportType === 'access') {
            foreach ($data as $item) {
                $sheet->setCellValue('A' . $row, Carbon::parse($item->created_at)->format('d/m/Y H:i:s'));
                $sheet->setCellValue('B' . $row, $item->mail);
                $sheet->setCellValue('C' . $row, $item->result ? 'Exitoso' : 'Fallido');
                $sheet->setCellValue('D' . $row, $item->obs ?? '-');

                if ($item->result) {
                    $sheet->getStyle('C' . $row)->getFont()->getColor()->setRGB('198754');
                } else {
                    $sheet->getStyle('C' . $row)->getFont()->getColor()->setRGB('DC3545');
                }
                $sheet->getStyle('C' . $row)->getFont()->setBold(true);

                $sheet->getStyle('A' . $row . ':' . $lastCol . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $row++;
            }
        } else {
            foreach ($data as $item) {
                $sheet->setCellValue('A' . $row, Carbon::parse($item->created_at)->format('d/m/Y H:i:s'));
                $sheet->setCellValue('B' . $row, $item->user?->name ?? 'Sistema / Eliminado');
                $sheet->setCellValue('C' . $row, $item->table);
                $sheet->setCellValue('D' . $row, $item->ip);
                $sheet->setCellValue('E' . $row, $item->obs ?? '-');

                $sheet->getStyle('A' . $row . ':' . $lastCol . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $row++;
            }
        }

        $footerRow = $row + 2;
        $sheet->setCellValue('A' . $footerRow, $general->footer ?? '');
        $sheet->getStyle('A' . $footerRow)->applyFromArray($styleFooter);
        $sheet->setCellValue($lastCol . $footerRow, $fecha);
        $sheet->getStyle($lastCol . $footerRow)->applyFromArray($styleFooter);
        $sheet->getStyle($lastCol . $footerRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

        $filename = 'reporte_historial_' . Carbon::now()->format('Ymd_His') . '.xlsx';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
