<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Produk;
use App\Models\Penjualan;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Jumlah Produk', Produk::count())
                ->description('Total Produk'),

            Stat::make('Jumlah Penjualan', Penjualan::count())
                ->description('Total Penjualan'),
        ];
    }
}
