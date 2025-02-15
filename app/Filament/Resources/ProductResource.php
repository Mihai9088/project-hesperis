<?php

namespace App\Filament\Resources;

use App\Enums\ProductStatusEnum;
use Filament\Forms;
use Filament\Tables;
use App\Models\Product;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProductResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProductResource\RelationManagers;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()->schema([
                    TextInput::make('title')->live(onBlur: true)->required()->afterStateUpdated(function (string $operation, $state, callable $set,) {
                        $set('slug', Str::slug($state));
                    }),
                    TextInput::make('slug')->required(),
                    Select::make('department_id')->relationship('department', 'name')->label(__('Department'))->preload()->searchable()->required()->reactive()->afterStateUpdated(function (callable $set,) {
                        $set('category_id', null);
                    }),
                    Select::make('category_id')->relationship(name: 'category', titleAttribute: 'name', modifyQueryUsing: function (Builder $query, callable $get) {
                        $departmentId = $get('department_id');
                        if ($departmentId) {
                            $query->where('department_id', $departmentId);
                        }
                    })->label(__('Category'))->preload()->searchable()->required(),
                    RichEditor::make('description')->required()->toolbarButtons(['bold', 'italic', 'underline', 'strike', 'link', 'unorderedList', 'orderedList', 'blockquote', 'h2', 'h3', 'redo', 'undo', 'table'])->columnSpan(2),
                    TextInput::make('price')->numeric()->required(),
                    TextInput::make('quantity')->integer()->required(),
                    Select::make('status')->options(ProductStatusEnum::labels())->default(ProductStatusEnum::Draft->value)->required(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
