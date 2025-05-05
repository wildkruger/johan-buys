<?php

namespace Modules\CryptoExchange\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PermissionRoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $cryptoExchangePermissions = \App\Models\Permission::whereIn('group', config('cryptoexchange.permission_group'))->get(['id', 'user_type']);

        foreach ($cryptoExchangePermissions as $permission) {
            if ($permission->user_type == 'Admin') {
                $adminRolePermissions[] = [
                    'role_id' => 1,
                    'permission_id' => $permission->id,
                ];
            }
            if ($permission->user_type == 'User') {
                $userRolePermissions[] = [
                    'role_id' => 2, 
                    'permission_id' => $permission->id
                ];
            }
            if ($permission->user_type == 'User') {
                $merchantRolePermissions[] = [
                    'role_id' => 3, 
                    'permission_id' => $permission->id
                ];
            }
        }
        
        DB::table('permission_role')->insert(
            $adminRolePermissions, 
            $userRolePermissions, 
            $merchantRolePermissions
        );
    }
}
