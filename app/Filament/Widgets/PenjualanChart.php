<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Penjualan;
use Illuminate\Support\Facades\DB;

class PenjualanChart extends ChartWidget
{
    protected static ?string $heading = 'Grafik Penjualan';

    protected function getData(): array
    {
        // Ambil data penjualan perbulan dari database
        $penjualanPerbulan = Penjualan::select(
            DB::raw('MONTH(created_at) as bulan'),
            DB::raw('SUM(total_harga) as total')
        )
            ->whereYear('created_at', date('Y'))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        // Siapkan array untuk semua bulan
        $bulanLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $data = array_fill(0, 12, 0);

        // Isi data penjualan ke array
        foreach ($penjualanPerbulan as $penjualan) {
            $data[$penjualan->bulan - 1] = $penjualan->total;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Penjualan (Rp)',
                    'data' => $data,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 2,
                    'fill' => true,
                ],
            ],
            'labels' => $bulanLabels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
