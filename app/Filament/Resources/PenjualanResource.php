<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenjualanResource\Pages;
use App\Models\Penjualan;
use App\Models\Produk;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PenjualanResource extends Resource
{
    protected static ?string $model = Penjualan::class;

    protected static ?string $navigationIcon = 'heroicon-s-currency-dollar';

    protected static ?string $navigationLabel = 'Transaksi Penjualan';

     public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin();
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Input Transaksi')
                    ->schema([
                        Forms\Components\Select::make('produk_id')
                            ->label('Pilih Produk')
                            ->options(Produk::all()->pluck('nama_produk', 'id')) // Ambil list produk
                            ->searchable()
                            ->required()
                            ->reactive() // 1. Wajib reactive biar bisa trigger event
                            ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                // Logic: Pas produk dipilih, ambil harganya
                                $produk = Produk::find($state);
                                if ($produk) {
                                    $set('harga_satuan', $produk->harga_produk);
                                    // Hitung ulang total kalo jumlah udah diisi
                                    $jumlah = (int) $get('jumlah');
                                    $set('total_harga', $jumlah * $produk->harga_produk);
                                }
                            }),

                        Forms\Components\TextInput::make('jumlah')
                            ->required()
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->reactive() // 2. Ini juga reactive
                            ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                // Logic: Pas jumlah diubah, kaliin sama harga satuan
                                $hargaSatuan = (int) $get('harga_satuan');
                                $set('total_harga', (int) $state * $hargaSatuan);
                            }),

                        // Hidden field buat nyimpen harga satuan sementara (helper)
                        Forms\Components\Hidden::make('harga_satuan'),

                        Forms\Components\TextInput::make('total_harga')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->readOnly() // 3. Readonly biar admin gak salah edit manual
                            ->dehydrated(), // Tetap dikirim ke database walau disabled/readonly
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('produk.nama_produk')
                    ->label('Produk')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('jumlah')
                    ->label('Qty')
                    ->numeric()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('total_harga')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->color('success'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Transaksi')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc') // Urutkan dari yang terbaru
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPenjualans::route('/'),
            'create' => Pages\CreatePenjualan::route('/create'),
            'edit' => Pages\EditPenjualan::route('/{record}/edit'),
        ];
    }
}