<?php

namespace App\Exports;

use App\Models\Product;
use App\Models\Tax;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductsExport implements FromCollection, WithHeadings
{
    use Exportable;

    public function headings(): array
    {
        return [
            'name',
            'item_code',
            'barcode_symbology',
            'description',
            'unit',
            'category',
            'brand',
            'tax',
            'mrp',
            'purchase_price',
            'sales_price',
            'purchase_tax_type',
            'sales_tax_type',
            'stock_quantitiy_alert',
            'opening_stock',
            'opening_stock_date',
            'wholesale_price',
            'wholesale_quantity',
            'warehouse',
            'units_amount'
        ];
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $warehouse = warehouse();

        $products = Product::where('warehouse_id', $warehouse->id)->select(
            'id',
            'name',
            'item_code',
            'barcode_symbology',
            'description',
            'unit_id',
            'category_id',
            'brand_id',
            'warehouse_id',
        )->with('details', 'category:id,name', 'brand:id,name', 'warehouse:id,name', 'unit:id,name')->get();

        return $products->map(function ($product) {
            return [
                'name' => $product->name,
                'item_code' => $product->item_code,
                'barcode_symbology' => $product->barcode_symbology,
                'description' => $product->description,
                'unit' => $product->unit?->name,
                'category' => $product->category?->name,
                'brand' => $product->brand?->name,
                'tax' => Tax::find($product->details->tax_id)?->name,
                'mrp' => $product->details->mrp,
                'purchase_price' => $product->details->purchase_price,
                'sales_price' => $product->details->sales_price,
                'purchase_tax_type' => $product->details->purchase_tax_type,
                'sales_tax_type' => $product->details->sales_tax_type,
                'stock_quantitiy_alert' => $product->details->stock_quantitiy_alert,
                'opening_stock' => $product->details->opening_stock,
                'opening_stock_date' => $product->details->opening_stock_date,
                'wholesale_price' => $product->details->wholesale_price,
                'wholesale_quantity' => $product->details->wholesale_quantity,
                'warehouse' => $product->warehouse?->name,
                'units_amount' => $product->details->units_amount
            ];
        });
    }
}
