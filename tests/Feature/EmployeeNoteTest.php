<?php

namespace Fereydooni\Shopping\Tests\Feature;

use Fereydooni\Shopping\app\Enums\EmployeeNotePriority;
use Fereydooni\Shopping\app\Enums\EmployeeNoteType;
use Fereydooni\Shopping\app\Models\Employee;
use Fereydooni\Shopping\app\Models\EmployeeNote;
use Fereydooni\Shopping\app\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeNoteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $this->user = User::factory()->create();
        $this->employee = Employee::factory()->create();
    }

    public function test_can_create_employee_note()
    {
        $noteData = [
            'employee_id' => $this->employee->id,
            'user_id' => $this->user->id,
            'title' => 'Test Note',
            'content' => 'This is a test note content.',
            'note_type' => EmployeeNoteType::GENERAL->value,
            'priority' => EmployeeNotePriority::MEDIUM->value,
            'is_private' => false,
            'tags' => ['test', 'sample'],
        ];

        $response = $this->postJson('/api/employee-notes', $noteData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'employee_id',
                    'user_id',
                    'title',
                    'content',
                    'note_type',
                    'priority',
                    'is_private',
                    'tags',
                    'created_at',
                    'updated_at',
                ],
            ]);

        $this->assertDatabaseHas('employee_notes', [
            'title' => 'Test Note',
            'employee_id' => $this->employee->id,
            'user_id' => $this->user->id,
        ]);
    }

    public function test_can_retrieve_employee_note()
    {
        $note = EmployeeNote::factory()->create([
            'employee_id' => $this->employee->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->getJson("/api/employee-notes/{$note->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'employee_id',
                    'user_id',
                    'title',
                    'content',
                    'note_type',
                    'priority',
                    'is_private',
                    'tags',
                    'created_at',
                    'updated_at',
                ],
            ]);
    }

    public function test_can_update_employee_note()
    {
        $note = EmployeeNote::factory()->create([
            'employee_id' => $this->employee->id,
            'user_id' => $this->user->id,
        ]);

        $updateData = [
            'title' => 'Updated Note Title',
            'content' => 'This is the updated content.',
            'priority' => EmployeeNotePriority::HIGH->value,
        ];

        $response = $this->putJson("/api/employee-notes/{$note->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Employee note updated successfully',
                'data' => [
                    'title' => 'Updated Note Title',
                    'content' => 'This is the updated content.',
                    'priority' => EmployeeNotePriority::HIGH->value,
                ],
            ]);

        $this->assertDatabaseHas('employee_notes', [
            'id' => $note->id,
            'title' => 'Updated Note Title',
        ]);
    }

    public function test_can_delete_employee_note()
    {
        $note = EmployeeNote::factory()->create([
            'employee_id' => $this->employee->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->deleteJson("/api/employee-notes/{$note->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Employee note deleted successfully',
            ]);

        $this->assertSoftDeleted('employee_notes', [
            'id' => $note->id,
        ]);
    }

    public function test_can_archive_employee_note()
    {
        $note = EmployeeNote::factory()->create([
            'employee_id' => $this->employee->id,
            'user_id' => $this->user->id,
            'is_archived' => false,
        ]);

        $response = $this->postJson("/api/employee-notes/{$note->id}/archive");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Employee note archived successfully',
            ]);

        $this->assertDatabaseHas('employee_notes', [
            'id' => $note->id,
            'is_archived' => true,
        ]);
    }

    public function test_can_unarchive_employee_note()
    {
        $note = EmployeeNote::factory()->create([
            'employee_id' => $this->employee->id,
            'user_id' => $this->user->id,
            'is_archived' => true,
        ]);

        $response = $this->postJson("/api/employee-notes/{$note->id}/unarchive");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Employee note unarchived successfully',
            ]);

        $this->assertDatabaseHas('employee_notes', [
            'id' => $note->id,
            'is_archived' => false,
        ]);
    }

    public function test_can_get_employee_notes()
    {
        EmployeeNote::factory()->count(3)->create([
            'employee_id' => $this->employee->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->getJson("/api/employees/{$this->employee->id}/notes");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'employee_id',
                        'user_id',
                        'title',
                        'content',
                        'note_type',
                        'priority',
                        'is_private',
                        'tags',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);

        $response->assertJsonCount(3, 'data');
    }

    public function test_can_search_employee_notes()
    {
        EmployeeNote::factory()->create([
            'employee_id' => $this->employee->id,
            'user_id' => $this->user->id,
            'title' => 'Performance Review Note',
            'content' => 'This is about performance.',
        ]);

        EmployeeNote::factory()->create([
            'employee_id' => $this->employee->id,
            'user_id' => $this->user->id,
            'title' => 'Training Note',
            'content' => 'This is about training.',
        ]);

        $response = $this->getJson('/api/employee-notes/search?q=performance');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Performance Review Note');
    }

    public function test_validation_requires_required_fields()
    {
        $response = $this->postJson('/api/employee-notes', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'employee_id',
                'user_id',
                'title',
                'content',
                'note_type',
                'priority',
            ]);
    }

    public function test_validation_enforces_field_limits()
    {
        $noteData = [
            'employee_id' => $this->employee->id,
            'user_id' => $this->user->id,
            'title' => str_repeat('a', 256), // Exceeds 255 limit
            'content' => 'Valid content',
            'note_type' => EmployeeNoteType::GENERAL->value,
            'priority' => EmployeeNotePriority::MEDIUM->value,
        ];

        $response = $this->postJson('/api/employee-notes', $noteData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    public function test_can_list_all_employee_notes()
    {
        EmployeeNote::factory()->count(5)->create([
            'employee_id' => $this->employee->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->getJson('/api/employee-notes');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'current_page',
                    'data' => [
                        '*' => [
                            'id',
                            'employee_id',
                            'user_id',
                            'title',
                            'content',
                            'note_type',
                            'priority',
                            'is_private',
                            'tags',
                            'created_at',
                            'updated_at',
                        ],
                    ],
                    'per_page',
                    'total',
                ],
            ]);
    }
}
