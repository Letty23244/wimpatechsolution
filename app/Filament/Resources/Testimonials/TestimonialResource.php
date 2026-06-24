<?php

namespace App\Filament\Resources\Testimonials;

use App\Filament\Resources\Testimonials\Pages;
use App\Models\Service;
use App\Models\Testimonial;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-star';
    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Section::make('Client Information')
                ->schema([
                    Forms\Components\TextInput::make('client_name')->required()->maxLength(255),
                    Forms\Components\TextInput::make('client_company')->maxLength(255),
                    Forms\Components\FileUpload::make('client_photo')
                        ->image()->directory('testimonials')->maxSize(2048)->avatar(),
                    Forms\Components\Select::make('service_id')
                        ->label('Related Service')
                        ->options(Service::active()->pluck('name', 'id'))
                        ->searchable()
                        ->nullable(),
                ])->columns(2),

            Forms\Components\Section::make('Testimonial')
                ->schema([
                    Forms\Components\Select::make('rating')
                        ->required()
                        ->options([
                            1 => '⭐ 1 Star',
                            2 => '⭐⭐ 2 Stars',
                            3 => '⭐⭐⭐ 3 Stars',
                            4 => '⭐⭐⭐⭐ 4 Stars',
                            5 => '⭐⭐⭐⭐⭐ 5 Stars',
                        ])
                        ->default(5),

                    Forms\Components\Select::make('status')
                        ->required()
                        ->options(['pending' => 'Pending', 'approved' => 'Approved'])
                        ->default('pending'),

                    Forms\Components\Textarea::make('content')->required()->rows(4)->columnSpanFull(),
                    Forms\Components\Toggle::make('is_featured')->label('Featured on homepage'),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('client_photo')->circular(),
                Tables\Columns\TextColumn::make('client_name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('client_company')->searchable(),
                Tables\Columns\TextColumn::make('service.name')->label('Service'),
                Tables\Columns\TextColumn::make('rating')->sortable(),
                Tables\Columns\IconColumn::make('is_featured')->boolean(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors(['success' => 'approved', 'warning' => 'pending']),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['pending' => 'Pending', 'approved' => 'Approved']),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTestimonials::route('/'),
            'create' => Pages\CreateTestimonial::route('/create'),
            'view' => Pages\ViewTestimonial::route('/{record}'),
            'edit' => Pages\EditTestimonial::route('/{record}/edit'),
        ];
    }
}