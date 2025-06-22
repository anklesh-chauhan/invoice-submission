<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VendorMasterResource\Pages;
use App\Filament\Resources\VendorMasterResource\RelationManagers;
use App\Models\VendorMaster;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Enums\VendorType;
use App\Enums\VendorStatus;


class VendorMasterResource extends Resource
{
    protected static ?string $model = VendorMaster::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Vendor Details')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('VendorCode')
                            ->required()
                            ->maxLength(20)
                            ->unique(VendorMaster::class, 'VendorCode', ignoreRecord: true),
                        Forms\Components\TextInput::make('VendorName')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\Select::make('VendorType')
                            ->options(VendorType::class)
                            ->required()
                            ->enum(VendorType::class),
                        Forms\Components\Select::make('Status')
                            ->options(VendorStatus::class)
                            ->default(VendorStatus::Active)
                            ->required()
                            ->enum(VendorStatus::class),
                        Forms\Components\TextInput::make('ContactPerson')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('Email')
                            ->email()
                            ->maxLength(100)
                            ->unique(VendorMaster::class, 'Email', ignoreRecord: true),
                        Forms\Components\TextInput::make('Phone')
                            ->tel()
                            ->maxLength(20),
                    ]),
                Forms\Components\Section::make('Address')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('AddressLine1')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('AddressLine2')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('City')
                            ->maxLength(50),
                        Forms\Components\TextInput::make('State')
                            ->maxLength(50),
                        Forms\Components\TextInput::make('Country')
                            ->maxLength(50),
                        Forms\Components\TextInput::make('PostalCode')
                            ->maxLength(20),
                    ]),
                Forms\Components\Section::make('Financial Details')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('TaxID')
                            ->maxLength(50),
                        Forms\Components\TextInput::make('PaymentTerms')
                            ->maxLength(50),
                        Forms\Components\TextInput::make('Currency')
                            ->maxLength(3),
                        Forms\Components\TextInput::make('BankName')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('BankAccountNumber')
                            ->maxLength(50),
                        Forms\Components\TextInput::make('RoutingNumber')
                            ->maxLength(50),
                    ]),
                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Textarea::make('Notes')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('VendorCode')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('VendorName')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('VendorType')
                    ->sortable(),
                Tables\Columns\TextColumn::make('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('Phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Active' => 'success',
                        'Inactive' => 'gray',
                        'Suspended' => 'danger',
                    })
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
                Tables\Filters\SelectFilter::make('VendorType')
                    ->options(VendorType::class),
                Tables\Filters\SelectFilter::make('Status')
                    ->options(VendorStatus::class),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListVendorMasters::route('/'),
            'create' => Pages\CreateVendorMaster::route('/create'),
            'edit' => Pages\EditVendorMaster::route('/{record}/edit'),
        ];
    }
}
