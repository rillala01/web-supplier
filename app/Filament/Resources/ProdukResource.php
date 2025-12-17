<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProdukResource\Pages;
use App\Models\Produk;
use App\Models\Barang;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProdukResource extends Resource
{
    protected static ?string $model = Produk::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    
    protected static ?string $navigationLabel = 'Manajemen Produk';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Produk')
                    ->schema([
                        Forms\Components\TextInput::make('nama_produk')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('harga_produk')
                            ->required()
                            ->numeric()
                            ->prefix('Rp'),
                        Forms\Components\TextInput::make('jumlah_stok')
                            ->label('Stok Produk Jadi')
                            ->default(0)
                            ->numeric()
                            ->helperText('Stok akan bertambah lewat menu Produksi (opsional)'),
                    ])->columns(2),

                // INI BAGIAN LOGIC RESEPNYA BRO
                Forms\Components\Section::make('Resep / Komposisi Bahan')
                    ->description('Tentukan bahan baku apa saja yang dipakai untuk 1 unit produk ini.')
                    ->schema([
                        Forms\Components\Repeater::make('reseps')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('barang_id')
                                    ->label('Bahan Baku')
                                    ->options(Barang::all()->pluck('nama_barang', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->reactive() // Biar bisa ambil data satuan realtime
                                    ->afterStateUpdated(fn ($state, Forms\Set $set) => 
                                        $set('satuan_display', Barang::find($state)?->satuan ?? '-')
                                    ),
                                
                                Forms\Components\TextInput::make('jumlah_pemakaian')
                                    ->label('Jumlah Dipakai')
                                    ->numeric()
                                    ->required(),
                                
                                Forms\Components\TextInput::make('satuan_display')
                                    ->label('Satuan')
                                    ->disabled()
                                    ->dehydrated(false), // Field ini cuma buat tampilan, gak masuk DB
                            ])
                            ->columns(3)
                            ->addActionLabel('Tambah Bahan Baku')
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_produk')
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('reseps_count')
                    ->counts('reseps')
                    ->label('Jml Bahan'),
                Tables\Columns\TextColumn::make('jumlah_stok')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => $state < 10 ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('harga_produk')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProduks::route('/'),
            'create' => Pages\CreateProduk::route('/create'),
            'edit' => Pages\EditProduk::route('/{record}/edit'),
        ];
    }
}