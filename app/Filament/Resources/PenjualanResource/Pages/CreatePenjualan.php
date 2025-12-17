<?php

namespace App\Filament\Resources\PenjualanResource\Pages;

use App\Filament\Resources\PenjualanResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreatePenjualan extends CreateRecord
{
    protected static string $resource = PenjualanResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index'); // Balik ke list setelah simpan
    }

    // Magic happens here! 
    protected function afterCreate(): void
    {
        // Ambil data penjualan yang baru aja dibuat
        $penjualan = $this->record;

        // Cek stok dulu cukup gak (Validasi tambahan)
        $produk = $penjualan->produk;
        
        if ($produk) {
            // Kurangi stok produk
            $produk->decrement('jumlah_stok', $penjualan->jumlah);
            
            // Opsional: Kasih notif kalau stok menipis
            if ($produk->fresh()->jumlah_stok < 10) {
                Notification::make()
                    ->warning()
                    ->title('Stok Menipis!')
                    ->body("Stok {$produk->nama_produk} tinggal dikit nih, restock gih!")
                    ->send();
            }
        }
    }
}