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
use App\Models\BulkInvoiceApproval;
use Illuminate\Support\Str;

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
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->label('Submitted At')->searchable(),
                Tables\Columns\TextColumn::make('invoice_date')->date()->sortable()->searchable(),
                Tables\Columns\TextColumn::make('vendor.VendorName')->label('Vendor')->searchable(),
                Tables\Columns\TextColumn::make('invoice_number')->label('Invoice No.')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('amount')->money('INR', true)->searchable(),
                Tables\Columns\TextColumn::make('sentToUser.name')->label('Sent To')->searchable(),
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
                // Filter by Status
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(
                        collect(InvoiceStatus::cases())
                            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
                            ->toArray()
                    )
                    ->default(InvoiceStatus::Pending->value),

                // Filter by Sent User
                Tables\Filters\SelectFilter::make('sent_to_user_id')
                    ->label('Sent To User')
                    ->relationship('sentToUser', 'name')
                    ->searchable()
                    ->preload(),

                // Filter by Vendor
                Tables\Filters\SelectFilter::make('vendor_id')
                    ->label('Vendor')
                    ->relationship('vendor', 'VendorName')
                    ->searchable()
                    ->preload(),

                // Filter by Invoice Date Range
                Tables\Filters\Filter::make('invoice_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('From'),
                        Forms\Components\DatePicker::make('until')->label('Until'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['from'], fn ($q) => $q->whereDate('invoice_date', '>=', $data['from']))
                            ->when($data['until'], fn ($q) => $q->whereDate('invoice_date', '<=', $data['until']));
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->disabled(fn ($record) => $record->status === InvoiceStatus::Accepted),

                Tables\Actions\DeleteAction::make()
                    ->disabled(fn ($record) => $record->status === InvoiceStatus::Accepted),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('sendForApproval')
                    ->label('Send for Approval')
                    ->icon('heroicon-o-paper-airplane')
                    ->action(function (Collection $records) {
                        $user = $records->first()->sentToUser;
                        $ids = $records->pluck('id')->toArray();

                        $token = Str::uuid()->toString();

                        BulkInvoiceApproval::create([
                            'token' => $token,
                            'invoice_ids' => $ids,
                        ]);

                        $approveUrl = URL::signedRoute('invoices.bulk-approve', ['token' => $token]);
                        $rejectUrl = URL::signedRoute('invoices.bulk-reject', ['token' => $token]);

                        Mail::to($user->email)->send(
                            new BulkInvoiceStatusApprovalMail($records, $approveUrl, $rejectUrl)
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
