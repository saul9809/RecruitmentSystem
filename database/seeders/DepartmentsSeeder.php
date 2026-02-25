<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            ['department_name' => 'Human Resources', 'department_description' => 'Handles recruitment, employee relations, and organizational development.', 'location' => 'Building A'],
            ['department_name' => 'Finance', 'department_description' => 'Manages budgeting, accounting, and financial planning.', 'location' => 'Building B'],
            ['department_name' => 'Marketing', 'department_description' => 'Responsible for market research, advertising, and promotional activities.', 'location' => 'Building C'],
            ['department_name' => 'Sales', 'department_description' => 'Focuses on selling products or services and maintaining customer relationships.', 'location' => 'Building D'],
            ['department_name' => 'Information Technology', 'department_description' => 'Oversees the companys technology infrastructure and support.', 'location' => 'Building E'],
            ['department_name' => 'Customer Service', 'department_description' => 'Provides support and assistance to customers.', 'location' => 'Building F'],
            ['department_name' => 'Research and Development', 'department_description' => 'Conducts research to develop new products or improve existing ones.', 'location' => 'Building G'],
            ['department_name' => 'Operations', 'department_description' => 'Manages day-to-day activities and ensures efficient processes.', 'location' => 'Building H'],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
