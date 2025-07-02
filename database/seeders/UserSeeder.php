<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@gmail.com',
            'role_id' => 1,
            'password' => Hash::make('Test@12345')
        ]);
        $dealer =User::create([

                'name' => 'Omar Farid',
                'email' => 'ofarid27@gmail.com',
                'password' => Hash::make('Test@12345')
        ]);
        $buyer =User::create([

                'name' => 'Omar Farid',
                'email' => 'ofarid27@hotmail.com',
                'password' => Hash::make('Test@12345')
        ]);

        //Role create
        $admin_role = Role::create(['name' => 'admin']);
        $dealer_role = Role::create(['name' => 'dealer']);
        $buyer_role = Role::create(['name' => 'buyer']);




        $permissions = [

            //Super Admin permission
            [
                'group_name' => 'Inventories',
                'permissions'=>
                [
                   'inventory import',


                ]
            ],

            // Inventory permission
            [
                'group_name' => 'Users',
                'permissions'=>
                    [
                        'Add User',
                        'Edit User',
                        'View User',
                        'Delete User',

                    ]
            ],
            // User permission
            [
                'group_name' => 'Role',
                'permissions'=>
                    [
                        'Add Role',
                        'Edit Role',
                        'View Role',
                        'Delete Role',

                    ]
            ],

            // Role permission

            [
                'group_name' => 'Permission',
                'permissions'=>
                    [
                        'Add Permission',
                        'Edit Permission',
                        'View Permission',
                        'Delete Permission',

                    ]
            ],

            // Lead permission

            [
                'group_name' => 'Inventories Feature',
                'permissions'=>
                    [
                        'Vehicle Year',
                        'Vehicle Make',
                        'Vehicle Model',
                        'Vehicle Trim',
                        'Vehicle Body',

                    ]
            ],


            [
                'group_name' => 'Lead management',
                'permissions'=>
                    [
                        'View Lead',
                        'Delete Lead',

                    ]
            ],


            [
                'group_name' => 'Message',
                'permissions'=>
                    [
                        'View Message',
                        'Delete Message',

                    ]
            ],

            [
                'group_name' => 'News Managment',
                'permissions'=>
                    [
                        'Add News',
                        'Edit News',
                        'View News',
                        'Delete News',

                    ]
            ],

            [
                'group_name' => 'Slider Managment',
                'permissions'=>
                    [
                        'Add Slider',
                        'Edit Slider',
                        'View Slider',
                        'Delete Slider',

                    ]
            ],




        ];


        for ($i = 0; $i<count($permissions); $i++)
        {
           $permissionGroup = $permissions[$i]['group_name'];
            for ($j = 0; $j<count($permissions[$i]['permissions']); $j++)
            {

                Permission::create(['name' => $permissions[$i]['permissions'][$j] , 'group_name'=> $permissionGroup]);

                $admin_role->syncPermissions(Permission::all());
                $admin->assignRole($admin_role);
            }
        }

        $dealer->givePermissionTo($dealer_role);
        $buyer->givePermissionTo($buyer_role);



    }
}
