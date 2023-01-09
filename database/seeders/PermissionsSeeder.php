<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('workspace_permissions')->insert([
            [
                'label' => 'Create note',
                'slug' => 'create_note',
                'description' => 'This feature allows the user to create a new note.',
                'example_video' => '',
            ],
            [
                'label' => 'Write on note',
                'slug' => 'write_note',
                'description' => 'This feature allows the user to write text in a note.',
                'example_video' => '',
            ],
            [
                'label' => 'Delete note',
                'slug' => 'delete_note',
                'description' => 'This feature allows the user to delete a note.',
                'example_video' => '',
            ],
            [
                'label' => 'Read note',
                'slug' => 'read_note',
                'description' => 'This feature allows the user to see activities and read text of a note.',
                'example_video' => '',
            ],
            [
                'label' => 'Add member',
                'slug' => 'add_member',
                'description' => 'This feature allows the user to add more members into a Workspace.',
                'example_video' => '',
            ],
            [
                'label' => 'Delete member',
                'slug' => 'delete_member',
                'description' => 'This feature allows the user to delete members of a Workspace.',
                'example_video' => '',
            ],
            [
                'label' => 'Update member',
                'slug' => 'update_member',
                'description' => 'This feature allows the user to change members info of a Workspace.',
                'example_video' => '',
            ],
        ]);
    }
}
