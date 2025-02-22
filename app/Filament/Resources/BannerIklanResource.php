<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BannerIklanResource\Pages;
use App\Filament\Resources\BannerIklanResource\RelationManagers;
use App\Models\BannerIklan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BannerIklanResource extends Resource
{
    protected static ?string $model = BannerIklan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Manajemen Iklan';

    protected static ?string $navigationLabel = 'Banner Iklan';
    protected static ?int $navigationSort = 1;
    protected static ?string $pluralModelLabel = 'Banner Iklan';
    protected static ?string $pluralLabel = 'Banner Iklan';
    protected static ?string $modelLabel = 'Banner Iklan';
    protected static ?string $label = 'Banner Iklan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('judul_iklan')
                    ->required()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('banner_image')
                    ->image()
                    ->required()
                    ->visibility('public')
                    ->directory('public/banners')
                    ->imageEditor()
                    ->imageResizeMode('contain') // Menyesuaikan ukuran secara proporsional
                    // ->imageCropAspectRatio('16:9') // Ini akan memotong gambar jika rasio tidak sesuai
                    ->imageResizeTargetWidth('1024')
                    ->imageResizeTargetHeight('1024')
                    ->maxSize(2048)
                    ->preserveFilenames()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->helperText('Format yang didukung: JPG, PNG, WEBP. Maksimal 2MB. Gambar akan disesuaikan secara proporsional.'),
                Forms\Components\Textarea::make('deskripsi')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\DateTimePicker::make('tanggal_mulai')
                    ->required(),
                Forms\Components\DateTimePicker::make('tanggal_selesai')
                    ->required(),
                Forms\Components\TextInput::make('pemilik_iklan')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('keterangan')
                    ->columnSpanFull(),
                Forms\Components\Select::make('status')
                    ->required()
                    ->options([
                        'aktif' => 'Aktif',
                        'nonaktif' => 'Nonaktif'
                    ])
                    ->default('aktif'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('judul_iklan')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('banner_image'),
                Tables\Columns\TextColumn::make('tanggal_mulai')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_selesai')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pemilik_iklan')
                    ->searchable(),
                Tables\Columns\SelectColumn::make('status')
                    ->options([
                        'aktif' => 'Aktif',
                        'nonaktif' => 'Nonaktif'
                    ]),
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
            'index' => Pages\ListBannerIklans::route('/'),
            'create' => Pages\CreateBannerIklan::route('/create'),
            'edit' => Pages\EditBannerIklan::route('/{record}/edit'),
        ];
    }
}
