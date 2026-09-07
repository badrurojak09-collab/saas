<?php

namespace App\Filament\Tenant\Resources;

use App\Enums\Tenant\StudentBillStatus;
use App\Filament\Tenant\Resources\StudentBillResource\Pages\CreateStudentBill;
use App\Filament\Tenant\Resources\StudentBillResource\Pages\EditStudentBill;
use App\Filament\Tenant\Resources\StudentBillResource\Pages\ListStudentBills;
use App\Filament\Tenant\Resources\StudentBillResource\Pages\ViewStudentBill;
use App\Models\Tenant\Student;
use App\Models\Tenant\StudentBill;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StudentBillResource extends Resource
{
    protected static ?string $model = StudentBill::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static string|\UnitEnum|null $navigationGroup = 'Keuangan';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Tagihan Mahasiswa';

    protected static ?string $pluralModelLabel = 'Tagihan Mahasiswa';

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::whereIn('status', [
            StudentBillStatus::Issued,
            StudentBillStatus::Overdue,
        ])->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Tagihan')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('bill_number')
                            ->label('Nomor Tagihan / Invoice')
                            ->default(fn () => 'BILL-'.date('Ymd').'-'.rand(1000, 9999))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),
                        Select::make('student_id')
                            ->label('Mahasiswa')
                            ->relationship('student', 'name')
                            ->getOptionLabelFromRecordUsing(fn (Student $record): string => "{$record->student_number} - {$record->name}")
                            ->searchable(['name', 'student_number'])
                            ->preload()
                            ->required(),
                        Select::make('semester_id')
                            ->label('Semester')
                            ->relationship('semester', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('fee_type_id')
                            ->label('Jenis Pembayaran / Komponen')
                            ->relationship('feeType', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        DatePicker::make('due_date')
                            ->label('Jatuh Tempo')
                            ->required(),
                        Select::make('status')
                            ->label('Status Tagihan')
                            ->options(StudentBillStatus::class)
                            ->default(StudentBillStatus::Issued->value)
                            ->required(),
                    ]),
                ]),

            Section::make('Rincian Nominal Tagihan')
                ->schema([
                    Grid::make(3)->schema([
                        TextInput::make('amount')
                            ->label('Nominal Tarif')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),
                        TextInput::make('discount')
                            ->label('Potongan / Beasiswa')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0),
                        TextInput::make('total_amount')
                            ->label('Total Tagihan Akhir')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),
                        Textarea::make('description')
                            ->label('Keterangan Tagihan')
                            ->columnSpanFull()
                            ->rows(2),
                    ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('bill_number')
                    ->label('No. Tagihan')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold')
                    ->icon('heroicon-o-document-currency-dollar'),
                TextColumn::make('student.student_number')
                    ->label('NIM')
                    ->searchable()
                    ->badge()
                    ->color('gray'),
                TextColumn::make('student.name')
                    ->label('Nama Mahasiswa')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->description(fn (StudentBill $record): ?string => $record->student?->studyProgram?->name),
                TextColumn::make('feeType.name')
                    ->label('Jenis Biaya')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                TextColumn::make('total_amount')
                    ->label('Total Tagihan')
                    ->money('IDR')
                    ->weight('bold')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('semester_id')
                    ->label('Semester')
                    ->relationship('semester', 'name'),
                SelectFilter::make('fee_type_id')
                    ->label('Jenis Biaya')
                    ->relationship('feeType', 'name'),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(StudentBillStatus::class),
            ])
            ->recordActions([
                Action::make('markPaid')
                    ->label('Set Lunas')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (StudentBill $record): bool => $record->status !== StudentBillStatus::Paid)
                    ->action(function (StudentBill $record): void {
                        $record->update([
                            'status' => StudentBillStatus::Paid,
                        ]);
                        Notification::make()->title('Tagihan berhasil ditandai Lunas')->success()->send();
                    }),
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStudentBills::route('/'),
            'create' => CreateStudentBill::route('/create'),
            'view' => ViewStudentBill::route('/{record}'),
            'edit' => EditStudentBill::route('/{record}/edit'),
        ];
    }
}
