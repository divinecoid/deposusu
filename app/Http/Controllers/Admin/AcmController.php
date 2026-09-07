<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AcmController extends Controller
{
    protected $features = [
        'products' => 'Products & Inventory',
        'customers' => 'Customers Management',
        'suppliers' => 'Suppliers Management',
        'branches_areas' => 'Branches & Areas',
        'orders' => 'Order Management',
        'invoices' => 'Invoice Management',
        'payments' => 'Payment Management',
        'expenses' => 'Expenses & Accounting',
        'warehouse' => 'Warehouse (WMS)',
        'live_chat' => 'Live Chat Support',
        'kasir' => 'POS / Cashier',
        'marketplace' => 'Marketplace Integration',
        'deliveries' => 'Delivery Management',
        'reports' => 'Reports & Analytics',
        'performance' => 'Employee Performance',
        'acm' => 'Access Control Matrix (ACM)'
    ];

    protected $actions = [
        'create' => 'Create (C)',
        'read' => 'Read (R)',
        'update' => 'Update (U)',
        'delete' => 'Delete (D)',
    ];

    public function index()
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $roles = Role::all();
        $features = $this->features;
        $actions = $this->actions;

        // Ensure all permissions exist in the database
        foreach ($features as $featureKey => $featureName) {
            foreach (array_keys($actions) as $action) {
                Permission::firstOrCreate([
                    'name' => "{$featureKey}.{$action}",
                    'guard_name' => 'web'
                ]);
            }
        }

        // Build a grid of permissions per role
        $matrix = [];
        foreach ($features as $featureKey => $featureName) {
            foreach (array_keys($actions) as $action) {
                $permissionName = "{$featureKey}.{$action}";
                $matrix[$featureKey][$action] = [];
                
                foreach ($roles as $role) {
                    $matrix[$featureKey][$action][$role->id] = $role->hasPermissionTo($permissionName);
                }
            }
        }

        return view('admin.acm.index', compact('roles', 'features', 'actions', 'matrix'));
    }

    public function update(Request $request)
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $roles = Role::all();
        $features = $this->features;
        $actions = $this->actions;

        // Request format: permissions[role_id][feature][action] = "1"
        $submittedPermissions = $request->input('permissions', []);

        foreach ($roles as $role) {
            $rolePermissions = [];

            foreach ($features as $featureKey => $featureName) {
                foreach (array_keys($actions) as $action) {
                    $permissionName = "{$featureKey}.{$action}";
                    
                    if (isset($submittedPermissions[$role->id][$featureKey][$action])) {
                        $rolePermissions[] = $permissionName;
                    }
                }
            }

            // Sync the permissions for this role
            $role->syncPermissions($rolePermissions);
        }

        return redirect()->route('admin.acm.index')->with('success', 'Access Control Matrix updated successfully.');
    }
}
