<?php

namespace App\Filament\Resources\TextWidgets;

use App\Filament\Resources\TextWidgets\Pages\CreateTextWidget;
use App\Filament\Resources\TextWidgets\Pages\EditTextWidget;
use App\Filament\Resources\TextWidgets\Pages\ListTextWidgets;
use App\Filament\Resources\TextWidgets\Pages\ViewTextWidget;
use App\Models\TextWidget;
use BackedEnum;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class TextWidgetResource extends Resource
{
    protected static ?string $model = TextWidget::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquaresPlus;

    protected static string|UnitEnum|null $navigationGroup = 'Content';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->required()
                    ->maxLength(255),
                FileUpload::make('image')
                    ->image()
                    ->disk('public')
                    ->directory('widgets'),
                TextInput::make('title')
                    ->required()
                    ->maxLength(2048),
                RichEditor::make('content'),
                Toggle::make('active')
                    ->required(),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key'),
                TextColumn::make('title'),
                IconColumn::make('active')->boolean(),
                TextColumn::make('updated_at')->dateTime(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTextWidgets::route('/'),
            'create' => CreateTextWidget::route('/create'),
            'view' => ViewTextWidget::route('/{record}'),
            'edit' => EditTextWidget::route('/{record}/edit'),
        ];
    }
}
