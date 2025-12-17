<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Penjualan;
use Illuminate\Support\Carbon;

class ChartPenjualan extends ChartWidget
{
    protected static ?string $heading = 'Grafik Pemasukan Bulanan';

    // Biar urutannya rapi (setelah StatsOverview)
    protected static ?int $sort = 2;

    // Biar chart-nya lebar menuhin layar
    protected int | string | array $columnSpan = 'full';

    // Tinggi chart biar enak diliat
    protected static ?string $maxHeight = '300px';

    // === SATPAM AKSES ===
    // Sama kayak tadi, cuma Admin yang boleh liat grafik duit
    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    protected function getData(): array
    {
        // Kita ambil data tahun ini
        $tahunIni = now()->year;
        
        $dataPerBulan = [];
        $labels = [];

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $date = Carbon::create()->month($bulan);
            $labels[] = $date->format('F'); // Nama Bulan (January, February...)

            // Query Total Duit per Bulan
            $total = Penjualan::whereYear('created_at', $tahunIni)
                ->whereMonth('created_at', $bulan)
                ->sum('total_harga');
            
            $dataPerBulan[] = $total;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pemasukan (Rp)',
                    'data' => $dataPerBulan,
                    'fill' => 'start', 
                    'borderColor' => '#10b981', 
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)', 
                    'tension' => 0.4, 
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line'; 
    }
}