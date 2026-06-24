<?php

namespace App\Filament\Resources\Portfolios;

use App\Filament\Resources\Portfolios\Pages;
use App\Models\Portfolio;
use App\Models\Service;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PortfolioResource extends Resource
{
    protected static ?string $model = Portfolio::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-briefcase';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Section::make('Basic Information')
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn($state, Forms\Set $set) =>
                            $set('slug', Str::slug($state))),

                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(Portfolio::class, 'slug', ignoreRecord: true),

                    Forms\Components\TextInput::make('client_name')->maxLength(255),

                    Forms\Components\Select::make('service_id')
                        ->label('Service')
                        ->options(Service::active()->pluck('name', 'id'))
                        ->searchable()
                        ->nullable(),
                ])->columns(2),

            Forms\Components\Section::make('Details')
                ->schema([
                    Forms\Components\RichEditor::make('description')->columnSpanFull(),
                    Forms\Components\FileUpload::make('featured_image')
                        ->image()->directory('portfolios')->maxSize(2048)->columnSpanFull(),
                    Forms\Components\FileUpload::make('gallery')
                        ->image()->directory('portfolios/gallery')->multiple()->maxSize(2048)->columnSpanFull(),
                    Forms\Components\TextInput::make('project_url')->url()->maxLength(255),
                    Forms\Components\DatePicker::make('completed_at')->label('Completion Date'),
                ])->columns(2),

            Forms\Components\Section::make('Display Settings')
                ->schema([
                    Forms\Components\Select::make('status')
                        ->required()
                        ->options(['published' => 'Published', 'draft' => 'Draft'])
                        ->default('published'),
                    Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
                    Forms\Components\Toggle::make('is_featured')->label('Featured on homepage'),
                ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('featured_image'),
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('client_name')->searchable(),
                Tables\Columns\TextColumn::make('service.name')->label('Service'),
                Tables\Columns\IconColumn::make('is_featured')->boolean(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors(['success' => 'published', 'warning' => 'draft']),
                Tables\Columns\TextColumn::make('completed_at')->date()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['published' => 'Published', 'draft' => 'Draft']),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->reorderable('sort_order')
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPortfolios::route('/'),
            'create' => Pages\CreatePortfolio::route('/create'),
            'view' => Pages\ViewPortfolio::route('/{record}'),
            'edit' => Pages\EditPortfolio::route('/{record}/edit'),
        ];
    }
}