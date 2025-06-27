<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceSubmissionResource\Pages;
use App\Filament\Resources\InvoiceSubmissionResource\RelationManagers;
use App\Models\InvoiceSubmission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Enums\InvoiceStatus;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceStatusApprovalMail;
use Illuminate\Support\Facades\URL;
use App\Mail\BulkInvoiceStatusApprovalMail;
use Illuminate\Support\Collection;

class InvoiceSubmissionResource extends Resource
{
    protected static ?string $model = InvoiceSubmission::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('invoice_date')
                    ->label('Invoice Date')
                    ->required()
                    ->default(now()),

                Forms\Components\Select::make('vendor_id')
                    ->label('Vendor')
                    ->relationship('vendor', 'VendorName') // adjust to correct column name in VendorMaster
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\TextInput::make('invoice_number')
                    ->label('Invoice Number')
                    ->nullable()
                    ->unique(ignoreRecord: true),

                Forms\Components\TextInput::make('amount')
                    ->label('Amount')
                    ->numeric()
                    ->nullable(),

                Forms\Components\Select::make('sent_to_user_id')
                    ->label('Sent To User')
                    ->relationship('sentToUser', 'name') // adjust to correct field
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\FileUpload::make('invoice_files')
                    ->label('Invoice Files')
                    ->multiple()
                    ->reorderable()
                    ->directory('invoices')
                    ->maxFiles(10)
                    ->maxSize(2048) // 2 MB per file
                    ->downloadable()
                    ->openable()// enables 'view' link in Filament UI
                    ->visibility('public') // or 'private'
                    ->required(false),
                Forms\Components\Select::make('status')
                    ->options(collect(InvoiceStatus::cases())->mapWithKeys(fn ($case) => [
                        $case->value => $case->label()
                    ])->toArray())
                    ->default(InvoiceStatus::Pending->value)
                    ->required()
                    ->label('Status'),
                Forms\Components\Textarea::make('notes')
                    ->label('Notes')
                    ->nullable()
                    ->maxLength(500),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->label('Submitted At'),
                Tables\Columns\TextColumn::make('invoice_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('vendor.VendorName')->label('Vendor'),
                Tables\Columns\TextColumn::make('invoice_number')->label('Invoice No.')->sortable(),
                Tables\Columns\TextColumn::make('amount')->money('INR', true),
                Tables\Columns\TextColumn::make('sentToUser.name')->label('Sent To'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (InvoiceStatus $state) => $state->label())
                    ->colors([
                        'secondary' => InvoiceStatus::Pending->value,
                        'success' => InvoiceStatus::Accepted->value,
                        'danger' => InvoiceStatus::Rejected->value,
                    ]),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn ($record) => InvoiceSubmissionResource::getUrl('view', ['record' => $record])),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('sendForApproval')
                    ->label('Send for Approval')
                    ->icon('heroicon-o-paper-airplane')
                    ->action(function (Collection $records) {
                        $user = $records->first()->sentToUser;

                        $invoiceData = $records->map(function ($invoice) {
                            return [
                                'invoice' => $invoice,
                                'approveUrl' => URL::signedRoute('invoices.approve', ['invoice' => $invoice->id]),
                                'rejectUrl' => URL::signedRoute('invoices.reject', ['invoice' => $invoice->id]),
                            ];
                        });

                        Mail::to($user->email)->send(
                            new BulkInvoiceStatusApprovalMail($invoiceData)
                        );
                    })
                    ->deselectRecordsAfterCompletion(),
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
            'index' => Pages\ListInvoiceSubmissions::route('/'),
            'create' => Pages\CreateInvoiceSubmission::route('/create'),
            'edit' => Pages\EditInvoiceSubmission::route('/{record}/edit'),
            'view' => Pages\ViewInvoiceSubmission::route('/{record}'),
        ];
    }
}
