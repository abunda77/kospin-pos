<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VoucherDiskonResource\Pages;
use App\Filament\Resources\VoucherDiskonResource\RelationManagers;
use App\Models\VoucherDiskon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Support\RawJs;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VoucherDiskonResource extends Resource
{
    protected static ?string $model = VoucherDiskon::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Menejemen Produk';
    protected static ?string $navigationLabel = 'Voucher Diskon';



    protected static ?string $recordTitleAttribute = 'kode_voucher';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('kode_voucher')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->default(function () {
                        $prefix = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 4));
                        $suffix = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
                        return $prefix . '-' . $suffix;
                    })
                    ->disabled()
                    ->maxLength(255),
                Forms\Components\Select::make('jenis_discount')
                    ->required()
                    ->options([
                        'prosentase' => 'Prosentase',
                        'nominal' => 'Nominal'
                    ])
                    ->live(),
                Forms\Components\TextInput::make('nilai_discount')
                    ->required()
                    ->numeric()
                    ->prefix(function (Forms\Get $get) {
                        return $get('jenis_discount') === 'prosentase' ? '%' : 'Rp';
                    })
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set, $state) {
                        if ($get('jenis_discount') === 'prosentase' && $state > 100) {
                            $set('nilai_discount', 100);
                        }
                    }),
                Forms\Components\DateTimePicker::make('expired_time')
                    ->required()
                    ->native(false)
                    ->displayFormat('d/m/Y H:i')
                    ->minDate(now()),
                Forms\Components\TextInput::make('stok_voucher')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->default(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_voucher')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nilai_discount')
                    ->money('idr')
                    ->sortable(),
                Tables\Columns\TextColumn::make('jenis_discount')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'prosentase' => 'success',
                        'nominal' => 'info',
                    }),
                Tables\Columns\TextColumn::make('expired_time')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stok_voucher')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListVoucherDiskons::route('/'),
            'create' => Pages\CreateVoucherDiskon::route('/create'),
            'edit' => Pages\EditVoucherDiskon::route('/{record}/edit'),
        ];
    }
}
