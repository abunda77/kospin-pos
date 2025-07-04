<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentMethodResource\Pages;
use App\Filament\Resources\PaymentMethodResource\RelationManagers;
use App\Models\PaymentMethod;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
class PaymentMethodResource extends Resource implements HasShieldPermissions
{
    public static function getPermissionPrefixes(): array
    {
        return [
            'view_any',
            'create',
            'update',
            'delete_any',
        ];
    }
    protected static ?string $model = PaymentMethod::class;

    protected static ?string $navigationIcon = 'heroicon-m-newspaper';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'Metode Pembayaran';

    protected static ?string $navigationGroup = 'Menejemen keuangan';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('image')
                    ->image()
                    ->required(),
                Forms\Components\Toggle::make('is_cash')
                    ->required()
                    ->label('Pembayaran Tunai'),
                Forms\Components\Select::make('gateway')
                    ->label('Payment Gateway')
                    ->options([
                        '' => 'Manual (Tanpa Gateway)',
                        'midtrans' => 'Midtrans',
                        'xendit' => 'Xendit',
                        'duitku' => 'Duitku',
                    ])
                    ->reactive()
                    ->afterStateUpdated(fn (Forms\Set $set) => $set('gateway_config', null)),
                Forms\Components\KeyValue::make('gateway_config')
                    ->label('Konfigurasi Gateway')
                    ->visible(fn (Forms\Get $get) => !empty($get('gateway')))
                    ->keyLabel('Parameter')
                    ->valueLabel('Nilai')
                    ->keyPlaceholder('Contoh: payment_type')
                    ->valuePlaceholder('Contoh: credit_card')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif?')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_cash')
                    ->boolean(),
                Tables\Columns\TextColumn::make('gateway')
                    ->label('Gateway'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Aktif?'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListPaymentMethods::route('/'),
            'create' => Pages\CreatePaymentMethod::route('/create'),
            'edit' => Pages\EditPaymentMethod::route('/{record}/edit'),
        ];
    }
}

