<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Produk;
use App\Models\Penjualan;
use App\Models\Barang; 
use Carbon\Carbon; 

class StatsOverview extends BaseWidget
{
    // === SATPAM AKSES (Updated) ===
    // Sekarang Admin DAN Produksi boleh liat widget ini
    public static function canView(): bool
    {
        return auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isProduksi());
    }

    protected function getStats(): array
    {
        $user = auth()->user();

        // === TAMPILAN 1: KHUSUS ADMIN (Fokus Cuan) ===
        if ($user->isAdmin()) {
            // 1. Logic Chart: Ambil Data Penjualan 7 Hari Terakhir
            $pemasukanChart = [];
            $transaksiChart = [];
            
            // Loop mundur 7 hari ke belakang
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                
                $pemasukanChart[] = Penjualan::whereDate('created_at', $date)->sum('total_harga');
                $transaksiChart[] = Penjualan::whereDate('created_at', $date)->count();
            }

            $totalPemasukan = Penjualan::sum('total_harga');
            
            // Hitung nilai aset yang mengendap di gudang bahan baku
            $totalAsetBahan = Barang::all()->sum(function ($barang) {
                return $barang->stok * $barang->harga;
            });

            // Tentukan warna icon tren (naik/turun dibanding kemarin)
            // Logic simple: Kalo hari ini lebih gede dari kemarin -> Ijo
            $isNaik = end($pemasukanChart) >= prev($pemasukanChart);
            $colorTrend = $isNaik ? 'success' : 'danger';
            $iconTrend = $isNaik ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down';

            return [
                Stat::make('Total Pemasukan', 'Rp ' . number_format($totalPemasukan, 0, ',', '.'))
                    ->description('Omzet kotor (7 hari terakhir)')
                    ->descriptionIcon($iconTrend)
                    ->chart($pemasukanChart) 
                    ->color($colorTrend),

                Stat::make('Nilai Aset Bahan Baku', 'Rp ' . number_format($totalAsetBahan, 0, ',', '.'))
                    ->description('Modal tertahan di gudang')
                    ->descriptionIcon('heroicon-m-banknotes')
                    ->color('warning'),

                Stat::make('Total Transaksi', Penjualan::count())
                    ->description('Jumlah struk penjualan')
                    ->chart($transaksiChart) 
                    ->color('primary'),
            ];
        }

        // === TAMPILAN 2: KHUSUS TIM PRODUKSI (Fokus Stok) ===
        // Tim produksi butuh tau sisa stok produk jadi & bahan baku yang kritis
        if ($user->isProduksi()) {
            // 1. Total Stok Produk (Bakso/Sosis/dll) yang siap jual
            $stokProduk = Produk::sum('jumlah_stok');

            // 2. Cek Bahan Baku yang udah mau abis (misal stok di bawah 5)
            $bahanKritis = Barang::where('stok', '<', 5)->count();

            return [
                Stat::make('Stok Produk Siap Jual', $stokProduk . ' Pcs/Porsi')
                    ->description('Total stok di freezer/display')
                    ->descriptionIcon('heroicon-m-cube')
                    ->color('success'),

                Stat::make('Bahan Baku Menipis', $bahanKritis . ' Item')
                    ->description($bahanKritis > 0 ? 'Segera lapor Admin buat belanja!' : 'Stok bahan baku aman')
                    ->descriptionIcon('heroicon-m-exclamation-triangle')
                    ->color($bahanKritis > 0 ? 'danger' : 'success'), // Merah kalo ada yg mau abis

                Stat::make('Varian Menu', Produk::count() . ' Jenis')
                    ->description('Total jenis produk aktif')
                    ->color('info'),
            ];
        }

        return [];
    }
}