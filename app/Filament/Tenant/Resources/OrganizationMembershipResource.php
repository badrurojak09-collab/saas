<?php

namespace App\Filament\Tenant\Resources;

use App\Enums\Tenant\OrganizationMembershipType;
use App\Filament\Tenant\Resources\OrganizationMembershipResource\Pages\CreateOrganizationMembership;
use App\Filament\Tenant\Resources\OrganizationMembershipResource\Pages\EditOrganizationMembership;
use App\Filament\Tenant\Resources\OrganizationMembershipResource\Pages\ListOrganizationMemberships;
use App\Models\Tenant\OrganizationMembership;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrganizationMembershipResource extends Resource
{
    protected static ?string $model = OrganizationMembership::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string|\UnitEnum|null $navigationGroup = 'Organisasi';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Keanggotaan')
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('user_id')
                            ->label('User')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('organization_unit_id')
                            ->label('Unit Organisasi')
                            ->relationship('organizationUnit', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('membership_type')
                            ->label('Tipe Keanggotaan')
                            ->options(OrganizationMembershipType::class)
                            ->default(OrganizationMembershipType::Member->value)
                            ->required(),
                        Toggle::make('is_primary')
                            ->label('Primary')
                            ->boolean()
                            ->default(false),
                        TextInput::make('starts_at')
                            ->label('Mulai')
                            ->datetime()
                            ->nullable(),
                        TextInput::make('ends_at')
                            ->label('Berakhir')
                            ->datetime()
                            ->nullable(),
                    ]),
                ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('organizationUnit.name')
                    ->label('Unit Organisasi')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('membership_type')
                    ->label('Tipe')
                    ->badge(),
                TextColumn::make('is_primary')
                    ->label('Primary')
                    ->boolean(),
                TextColumn::make('starts_at')
                    ->label('Mulai')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->label('Berakhir')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('membership_type')
                    ->label('Tipe')
                    ->options(OrganizationMembershipType::class),
                SelectFilter::make('is_primary')
                    ->label('Primary')
                    ->boolean(),
                SelectFilter::make('organization_unit_id')
                    ->label('Unit Organisasi')
                    ->relationship('organizationUnit', 'name'),
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
            'index' => ListOrganizationMemberships::route('/'),
            'create' => CreateOrganizationMembership::route('/create'),
            'edit' => EditOrganizationMembership::route('/{record}/edit'),
        ];
    }
}
