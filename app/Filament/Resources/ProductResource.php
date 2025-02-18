<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Product;
use App\Enums\RolesEnum;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Facades\Filament;
use App\Enums\ProductStatusEnum;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\Pages\EditProduct;
use App\Filament\Resources\ProductResource\Pages\ProductImages;
use App\Filament\Resources\ProductResource\Pages\ProductVariations;
use App\Filament\Resources\ProductResource\Pages\ProductVariationTypes;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProductResource\RelationManagers;
use Faker\Provider\ar_EG\Text;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-s-queue-list';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::End;

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
                    TextInput::make('quantity')->integer(),
                    Select::make('status')->options(ProductStatusEnum::labels())->default(ProductStatusEnum::Draft->value)->required(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('images')->collection('images')->limit(1)->label('Image')->conversion('thumb'),
                TextColumn::make('title')->sortable()->words(10)->searchable(),
                TextColumn::make('status')->badge()->colors(ProductStatusEnum::colors())->sortable(),
                TextColumn::make('department.name'),
                TextColumn::make('category.name'),
                TextColumn::make('created_at')->dateTime(),

            ])
            ->filters([
                SelectFilter::make('status')->options(ProductStatusEnum::labels()),
                SelectFilter::make('department_id')->relationship('department', 'name'),
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
            'images' => Pages\ProductImages::route('/{record}/images'),
            'variation-types' => Pages\ProductVariationTypes::route('/{record}/variation-types'),
            'variations' => ProductVariations::route('/{record}/variations'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return
            $page->generateNavigationItems([
                EditProduct::class,
                ProductImages::class,
                ProductVariationTypes::class,
                ProductVariations::class,
            ]);
    }

    public static function canViewAny(): bool
    {
        $user = Filament::auth()->user();

        return $user && $user->hasRole(RolesEnum::Vendor->value);
    }
}
