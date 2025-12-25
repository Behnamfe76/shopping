<?php

namespace Fereydooni\Shopping\database\seeders;

use Fereydooni\Shopping\app\Enums\EmployeeNotePriority;
use Fereydooni\Shopping\app\Enums\EmployeeNoteType;
use Fereydooni\Shopping\app\Models\Employee;
use Fereydooni\Shopping\app\Models\EmployeeNote;
use Fereydooni\Shopping\app\Models\User;
use Illuminate\Database\Seeder;

class EmployeeNoteSeeder extends Seeder
{
    public function run(): void
    {
        // Get some sample employees and users
        $employees = Employee::take(5)->get();
        $users = User::take(3)->get();

        if ($employees->isEmpty() || $users->isEmpty()) {
            $this->command->warn('No employees or users found. Skipping EmployeeNote seeding.');

            return;
        }

        $noteTypes = EmployeeNoteType::cases();
        $priorities = EmployeeNotePriority::cases();

        $sampleNotes = [
            [
                'title' => 'Excellent performance this quarter',
                'content' => 'The employee has shown exceptional performance in meeting all quarterly goals. Their dedication and attention to detail have been outstanding.',
                'note_type' => EmployeeNoteType::PERFORMANCE,
                'priority' => EmployeeNotePriority::HIGH,
                'is_private' => false,
                'tags' => ['performance', 'quarterly', 'excellent'],
            ],
            [
                'title' => 'Training completion - Customer Service',
                'content' => 'Successfully completed the advanced customer service training program. The employee demonstrated strong communication skills and problem-solving abilities.',
                'note_type' => EmployeeNoteType::TRAINING,
                'priority' => EmployeeNotePriority::MEDIUM,
                'is_private' => false,
                'tags' => ['training', 'customer-service', 'completed'],
            ],
            [
                'title' => 'Goal setting for Q2',
                'content' => 'Discussed and set new goals for Q2. Focus areas include improving efficiency, learning new software tools, and mentoring junior team members.',
                'note_type' => EmployeeNoteType::GOAL,
                'priority' => EmployeeNotePriority::MEDIUM,
                'is_private' => false,
                'tags' => ['goals', 'q2', 'planning'],
            ],
            [
                'title' => 'Team collaboration feedback',
                'content' => 'Received positive feedback from team members about excellent collaboration and support. The employee is a great team player.',
                'note_type' => EmployeeNoteType::FEEDBACK,
                'priority' => EmployeeNotePriority::LOW,
                'is_private' => false,
                'tags' => ['feedback', 'collaboration', 'teamwork'],
            ],
            [
                'title' => 'Safety incident report',
                'content' => 'Minor safety incident occurred during equipment operation. No injuries, but proper safety protocols need to be reinforced.',
                'note_type' => EmployeeNoteType::INCIDENT,
                'priority' => EmployeeNotePriority::HIGH,
                'is_private' => true,
                'tags' => ['safety', 'incident', 'protocols'],
            ],
            [
                'title' => 'Recognition for innovation',
                'content' => 'The employee suggested and implemented a new process that improved team efficiency by 15%. This kind of innovative thinking should be encouraged.',
                'note_type' => EmployeeNoteType::PRAISE,
                'priority' => EmployeeNotePriority::HIGH,
                'is_private' => false,
                'tags' => ['innovation', 'efficiency', 'recognition'],
            ],
            [
                'title' => 'General administrative note',
                'content' => 'Updated contact information and emergency contacts. All documentation is now current and accurate.',
                'note_type' => EmployeeNoteType::GENERAL,
                'priority' => EmployeeNotePriority::LOW,
                'is_private' => false,
                'tags' => ['administrative', 'documentation', 'updated'],
            ],
            [
                'title' => 'Performance improvement plan',
                'content' => 'Created performance improvement plan to address areas of concern. Regular check-ins scheduled to monitor progress.',
                'note_type' => EmployeeNoteType::PERFORMANCE,
                'priority' => EmployeeNotePriority::URGENT,
                'is_private' => true,
                'tags' => ['performance', 'improvement', 'plan'],
            ],
        ];

        foreach ($sampleNotes as $noteData) {
            $employee = $employees->random();
            $user = $users->random();

            EmployeeNote::create([
                'employee_id' => $employee->id,
                'user_id' => $user->id,
                'title' => $noteData['title'],
                'content' => $noteData['content'],
                'note_type' => $noteData['note_type'],
                'priority' => $noteData['priority'],
                'is_private' => $noteData['is_private'],
                'is_archived' => false,
                'tags' => $noteData['tags'],
                'attachments' => [],
            ]);
        }

        $this->command->info('EmployeeNote seeding completed successfully.');
    }
}
