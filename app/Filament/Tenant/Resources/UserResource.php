<?php

namespace App\Filament\Tenant\Resources;

use App\Enums\Tenant\UserStatus;
use App\Enums\Tenant\UserType;
use App\Filament\Tenant\Resources\UserResource\Pages\CreateUser;
use App\Filament\Tenant\Resources\UserResource\Pages\EditUser;
use App\Filament\Tenant\Resources\UserResource\Pages\ListUsers;
use App\Models\Tenant\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
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

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string|\UnitEnum|null $navigationGroup = 'Identitas & Akses';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Pengguna')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(150),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('username')
                            ->label('Username')
                            ->unique(ignoreRecord: true)
                            ->maxLength(100),
                        TextInput::make('phone')
                            ->label('Nomor Telepon')
                            ->tel()
                            ->maxLength(30),
                        TextInput::make('password')
                            ->label('Kata Sandi')
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->maxLength(255),
                        Select::make('user_type')
                            ->label('Tipe Pengguna')
                            ->options(array_combine(
                                array_map(fn (UserType $type) => $type->value, UserType::cases()),
                                array_map(fn (UserType $type) => ucfirst($type->value), UserType::cases())
                            ))
                            ->default(UserType::Staff->value)
                            ->required(),
                        Select::make('status')
                            ->label('Status')
                            ->options(array_combine(
                                array_map(fn (UserStatus $status) => $status->value, UserStatus::cases()),
                                array_map(fn (UserStatus $status) => ucfirst($status->value), UserStatus::cases())
                            ))
                            ->default(UserStatus::Active->value)
                            ->required(),
                        Select::make('roles')
                            ->label('Peran (Roles)')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload(),
                    ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('username')
                    ->label('Username')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Telepon'),
                TextColumn::make('user_type')
                    ->label('Tipe')
                    ->badge(),
                TextColumn::make('roles.name')
                    ->label('Peran')
                    ->badge(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('user_type')
                    ->label('Tipe Pengguna')
                    ->options(array_combine(
                        array_map(fn (UserType $type) => $type->value, UserType::cases()),
                        array_map(fn (UserType $type) => ucfirst($type->value), UserType::cases())
                    )),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(array_combine(
                        array_map(fn (UserStatus $status) => $status->value, UserStatus::cases()),
                        array_map(fn (UserStatus $status) => ucfirst($status->value), UserStatus::cases())
                    )),
                SelectFilter::make('roles')
                    ->label('Peran')
                    ->relationship('roles', 'name'),
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
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
