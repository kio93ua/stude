<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PostResource\Pages;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    protected static ?string $navigationLabel = 'Пости';
    protected static ?string $navigationGroup = 'Контент сайту';
    protected static ?string $pluralModelLabel = 'Пости';
    protected static ?string $modelLabel = 'Пост';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Контент')
                    ->schema([
                        TextInput::make('title')
                            ->label('Заголовок')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (callable $set, ?string $state, callable $get): void {
                                if ($get('is_manual_slug') === true) {
                                    return;
                                }

                                $set('slug', Str::slug((string) $state));
                            }),
                        TextInput::make('slug')
                            ->label('Слаг (URL)')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Використовується у посиланні: /posts/{slug}')
                            ->afterStateHydrated(function ($state, callable $set): void {
                                $set('is_manual_slug', filled($state));
                            })
                            ->afterStateUpdated(function (callable $set): void {
                                $set('is_manual_slug', true);
                            }),
                        Forms\Components\Hidden::make('is_manual_slug')
                            ->default(false)
                            ->dehydrated(false),
                        TextInput::make('excerpt')
                            ->label('Короткий опис')
                            ->maxLength(300)
                            ->columnSpanFull(),
                        RichEditor::make('body')
                            ->label('Основний текст')
                            ->required()
                            ->toolbarButtons([
                                'blockquote',
                                'bold',
                                'bulletList',
                                'codeBlock',
                                'h2',
                                'h3',
                                'italic',
                                'link',
                                'orderedList',
                                'underline',
                                'strike',
                                'media',
                            ])
                            ->columnSpanFull()
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('posts/editor')
                            ->fileAttachmentsVisibility('public'),
                    ])->columns(2),

                Section::make('Медіа')
                    ->schema([
                        FileUpload::make('cover_image_path')
                            ->label('Обкладинка')
                            ->image()
                            ->imageEditor()
                            ->directory('posts/covers')
                            ->disk('public')
                            ->visibility('public')
                            ->imagePreviewHeight('220')
                            ->maxSize(8192),
                        FileUpload::make('gallery_images')
                            ->label('Галерея')
                            ->multiple()
                            ->reorderable()
                            ->image()
                            ->directory('posts/gallery')
                            ->disk('public')
                            ->visibility('public')
                            ->panelLayout('grid')
                            ->imageEditor()
                            ->maxFiles(10)
                            ->maxSize(8192),
                        TextInput::make('youtube_url')
                            ->label('YouTube посилання')
                            ->url()
                            ->maxLength(255)
                            ->helperText('Підтримуються посилання формату youtu.be або youtube.com/watch?v=...'),
                    ])->columns(2),

                Section::make('Публікація')
                    ->schema([
                        Toggle::make('is_published')
                            ->label('Опубліковано')
                            ->inline(false),
                        DateTimePicker::make('published_at')
                            ->label('Дата публікації')
                            ->seconds(false)
                            ->helperText('Якщо залишити порожнім, при публікації виставиться поточний час.')
                            ->nullable(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('cover_image_path')
                    ->label('Обкладинка')
                    ->disk('public')
                    ->size(60)
                    ->circular(),
                TextColumn::make('title')
                    ->label('Заголовок')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('slug')
                    ->label('URL')
                    ->searchable()
                    ->toggleable(),
                IconColumn::make('is_published')
                    ->label('Статус')
                    ->boolean()
                    ->trueIcon('heroicon-m-bolt')
                    ->falseIcon('heroicon-m-cloud'),
                TextColumn::make('published_at')
                    ->label('Опубліковано')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Оновлено')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_published')
                    ->label('Публікація')
                    ->trueLabel('Опубліковані')
                    ->falseLabel('Чернетки')
                    ->placeholder('Всі'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('published_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
