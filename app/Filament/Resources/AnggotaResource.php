<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnggotaResource\Pages;
use App\Filament\Resources\AnggotaResource\RelationManagers;
use App\Models\Anggota;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Tables\Actions\ViewAction;
use Picqer\Barcode\BarcodeGeneratorPNG;

class AnggotaResource extends Resource
{
    protected static ?string $model = Anggota::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Manajemen Anggota';
    protected static ?string $navigationLabel = 'Anggota';
    protected static ?string $pluralModelLabel = 'Anggota';
    protected static ?string $pluralLabel = 'Anggota';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_lengkap')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('nik')
                    ->required()
                    ->maxLength(255),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_lengkap')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nik')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_pembelian')
                    ->money('idr')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('nama_lengkap')
                    ->label('Nama Lengkap'),
                TextEntry::make('nik')
                    ->label('NIK'),
                TextEntry::make('total_pembelian')
                    ->money('idr')
                    ->label('Total Pembelian')
                    ->badge()
                    ->color('success'),
                ImageEntry::make('barcode')
                    ->label('Barcode')
                    ->getStateUsing(function ($record) {
                        $generator = new BarcodeGeneratorPNG();
                        $barcode = base64_encode($generator->getBarcode($record->nik, $generator::TYPE_CODE_128, 2, 30));
                        return 'data:image/png;base64,' . $barcode;
                    })
                    ->extraAttributes([
                        'style' => 'background-color: #ffffff; max-width: 400px; max-height: 100px;'
                    ]),
                ImageEntry::make('qr_code')
                    ->label('QR Code')
                    ->getStateUsing(function ($record) {
                        $url = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . $record->nik;
                        return $url;
                    }),
                TextEntry::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->label('Diperbarui Pada')
                    ->dateTime(),
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
            'index' => Pages\ListAnggotas::route('/'),
            'create' => Pages\CreateAnggota::route('/create'),
            'edit' => Pages\EditAnggota::route('/{record}/edit'),
            'view' => Pages\ViewAnggota::route('/{record}'),
        ];
    }
}
