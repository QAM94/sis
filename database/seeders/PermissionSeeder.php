<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cases = ['create', 'read', 'update', 'delete'];
        $modules = ['announcement', 'course', 'course_timing', 'domain', 'instructor', 'program',
            'student', 'fee_voucher', 'schedule', 'transcript', 'notification', 'role', 'user',
            'offered_course', 'student_enrollment'];
        $permissions = [];

        foreach ($modules as $module) {
            foreach ($cases as $case) {
                $permissions[] = $module . '-' . $case;
            }
        }
        $permissions[] = 'offered_course-register';

        // Create permissions in the database
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }
    }
}
