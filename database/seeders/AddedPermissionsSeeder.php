<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class AddedPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // POS
        $permissions = [
            'pos_edit_invoice_item' => [
                'name' => 'pos_edit_invoice_item',
                'display_name' => 'POS Edit Invoice Item'
            ],
            'pos_delete_invoice_item' => [
                'name' => 'pos_delete_invoice_item',
                'display_name' => 'POS Delete Invoice Item'
            ],
            'pos_reset_invoice' => [
                'name' => 'pos_reset_invoice',
                'display_name' => 'POS Reset Invoice'
            ],
            'pos_add_shipping_cost' => [
                'name' => 'pos_add_shipping_cost',
                'display_name' => 'POS Add Shipping Cost'
            ],
            'pos_add_invoice_discount' => [
                'name' => 'pos_add_invoice_discount',
                'display_name' => 'POS Add Invoice Discount'
            ],
        ];

        foreach ($permissions as $group => $permission) {
            $permissionCount = Permission::where('name', $permission['name'])->count();

            if ($permissionCount == 0) {
                $newPermission = new Permission();
                $newPermission->name = $permission['name'];
                $newPermission->display_name = $permission['display_name'];
                $newPermission->save();
            }
        }
    }
}
