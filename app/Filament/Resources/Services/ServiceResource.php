<?php

namespace App\Filament\Resources\Services;

use App\Filament\Resources\Services\Pages;
use App\Models\Service;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Section::make('Basic Information')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn($state, Forms\Set $set) =>
                            $set('slug', Str::slug($state))),

                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(Service::class, 'slug', ignoreRecord: true),

                    Forms\Components\Select::make('category')
                        ->required()
                        ->options([
                            'web_development' => 'Web Development',
                            'mobile_app' => 'Mobile App',
                            'printing' => 'Printing',
                            'hosting' => 'Hosting',
                        ]),

                    Forms\Components\Select::make('status')
                        ->required()
                        ->options(['active' => 'Active', 'inactive' => 'Inactive'])
                        ->default('active'),
                ])->columns(2),

            Forms\Components\Section::make('Details')
                ->schema([
                    Forms\Components\TextInput::make('short_description')->maxLength(255)->columnSpanFull(),
                    Forms\Components\RichEditor::make('description')->columnSpanFull(),
                    Forms\Components\TextInput::make('icon')->placeholder('e.g. heroicon-o-globe-alt')->maxLength(255),
                    Forms\Components\FileUpload::make('image')->image()->directory('services')->maxSize(2048),
                ])->columns(2),

            Forms\Components\Section::make('Pricing')
                ->schema([
                    Forms\Components\TextInput::make('starting_price')->numeric()->prefix('UGX')->minValue(0),
                    Forms\Components\TextInput::make('price_unit')->placeholder('e.g. per project, per month')->maxLength(100),
                ])->columns(2),

            Forms\Components\Section::make('Features & Display')
                ->schema([
                    Forms\Components\Repeater::make('features')
                        ->simple(Forms\Components\TextInput::make('feature')->required())
                        ->columnSpanFull(),
                    Forms\Components\Toggle::make('is_featured')->label('Featured on homepage'),
                    Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->circular(),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\BadgeColumn::make('category')
                    ->colors([
                        'primary' => 'web_development',
                        'success' => 'mobile_app',
                        'warning' => 'printing',
                        'danger' => 'hosting',
                    ]),
                Tables\Columns\IconColumn::make('is_featured')->boolean(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors(['success' => 'active', 'danger' => 'inactive']),
                Tables\Columns\TextColumn::make('sort_order')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'web_development' => 'Web Development',
                        'mobile_app' => 'Mobile App',
                        'printing' => 'Printing',
                        'hosting' => 'Hosting',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options(['active' => 'Active', 'inactive' => 'Inactive']),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->reorderable('sort_order')
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'view' => Pages\ViewService::route('/{record}'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}