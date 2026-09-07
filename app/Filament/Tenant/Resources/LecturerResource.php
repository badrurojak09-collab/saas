<?php

namespace App\Filament\Tenant\Resources;

use App\Enums\Tenant\EmploymentStatus;
use App\Filament\Tenant\Resources\LecturerResource\Pages\CreateLecturer;
use App\Filament\Tenant\Resources\LecturerResource\Pages\EditLecturer;
use App\Filament\Tenant\Resources\LecturerResource\Pages\ListLecturers;
use App\Models\Tenant\Lecturer;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LecturerResource extends Resource
{
    protected static ?string $model = Lecturer::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string|\UnitEnum|null $navigationGroup = 'Sivitas Akademika';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Biodata Dosen')
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('user_id')
                            ->label('Akun Pengguna')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload(),
                        TextInput::make('employee_number')
                            ->label('NIP / Nomor Pegawai')
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),
                        TextInput::make('nidn')
                            ->label('NIDN')
                            ->unique(ignoreRecord: true)
                            ->maxLength(30),
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(150),
                        TextInput::make('academic_title')
                            ->label('Gelar Akademik')
                            ->maxLength(100),
                        Select::make('gender')
                            ->label('Jenis Kelamin')
                            ->options(['L' => 'Laki-laki', 'P' => 'Perempuan']),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(100),
                        TextInput::make('phone')
                            ->label('Nomor Telepon')
                            ->tel()
                            ->maxLength(30),
                        Select::make('employment_status')
                            ->label('Status Kepegawaian')
                            ->options([
                                EmploymentStatus::Permanent->value => 'Tetap',
                                EmploymentStatus::Contract->value => 'Kontrak',
                                EmploymentStatus::Honorary->value => 'Honorer / DPK',
                            ])
                            ->default(EmploymentStatus::Permanent->value)
                            ->required(),
                        DatePicker::make('joined_at')
                            ->label('Tanggal Bergabung'),
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => 'Aktif',
                                'inactive' => 'Nonaktif',
                            ])
                            ->default('active')
                            ->required(),
                    ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee_number')
                    ->label('NIP')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->badge()
                    ->color('gray'),
                TextColumn::make('nidn')
                    ->label('NIDN')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),
                TextColumn::make('name')
                    ->label('Nama Dosen')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Lecturer $record): ?string => $record->academic_title),
                TextColumn::make('employment_status')
                    ->label('Status Pegawai')
                    ->badge()
                    ->color(fn (?EmploymentStatus $state): string => match ($state) {
                        EmploymentStatus::Permanent => 'success',
                        EmploymentStatus::Contract => 'warning',
                        EmploymentStatus::Honorary => 'info',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (?EmploymentStatus $state): string => match ($state) {
                        EmploymentStatus::Permanent => 'Tetap',
                        EmploymentStatus::Contract => 'Kontrak',
                        EmploymentStatus::Honorary => 'Honorer / DPK',
                        default => '-',
                    }),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->icon('heroicon-o-envelope')
                    ->color('primary')
                    ->copyable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'gray',
                        default => 'secondary',
                    }),
            ])
            ->filters([
                SelectFilter::make('employment_status')
                    ->label('Status Pegawai')
                    ->options([
                        EmploymentStatus::Permanent->value => 'Tetap',
                        EmploymentStatus::Contract->value => 'Kontrak',
                        EmploymentStatus::Honorary->value => 'Honorer / DPK',
                    ]),
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Nonaktif',
                    ]),
            ])
            ->recordActions([
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
            'index' => ListLecturers::route('/'),
            'create' => CreateLecturer::route('/create'),
            'edit' => EditLecturer::route('/{record}/edit'),
        ];
    }
}
