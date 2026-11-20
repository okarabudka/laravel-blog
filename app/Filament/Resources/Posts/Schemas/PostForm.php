<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('title')
                                    ->required()
                                    ->maxLength(2048)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug((string) $state))),
                                TextInput::make('slug')
                                    ->required()
                                    ->maxLength(2048),
                            ]),
                        RichEditor::make('body')
                            ->required(),
                        TextInput::make('meta_title'),
                        TextInput::make('meta_description'),
                        Toggle::make('active')
                            ->required(),
                        DateTimePicker::make('published_at'),
                    ])->columnSpan(8),
                Section::make()
                    ->schema([
                        FileUpload::make('thumbnail')
                            ->image()
                            ->disk('public')
                            ->directory('posts'),
                        Select::make('categories')
                            ->multiple()
                            ->relationship('categories', 'title')
                            ->preload()
                            ->required(),
                    ])->columnSpan(4),
            ])->columns(12);
    }
}
