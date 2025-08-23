<?php

namespace Tests\Feature;

use Tests\TestCase;
use Fereydooni\Shopping\app\Models\EmployeePerformanceReview;
use Fereydooni\Shopping\app\Models\Employee;
use Fereydooni\Shopping\app\Models\User;
use Fereydooni\Shopping\app\DTOs\EmployeePerformanceReviewDTO;
use Fereydooni\Shopping\app\Services\EmployeePerformanceReviewService;
use Fereydooni\Shopping\app\Enums\EmployeePerformanceReviewStatus;
use Fereydooni\Shopping\app\Enums\EmployeePerformanceReviewRating;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;

class EmployeePerformanceReviewTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected Employee $employee;
    protected User $reviewer;
    protected User $approver;
    protected EmployeePerformanceReviewService $service;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $this->employee = Employee::factory()->create();
        $this->reviewer = User::factory()->create();
        $this->approver = User::factory()->create();
        $this->service = app(EmployeePerformanceReviewService::class);

        // Disable events and notifications for testing
        Event::fake();
        Notification::fake();
    }

    /** @test */
    public function it_can_create_employee_performance_review()
    {
        $data = [
            'employee_id' => $this->employee->id,
            'reviewer_id' => $this->reviewer->id,
            'review_period_start' => '2024-01-01',
            'review_period_end' => '2024-12-31',
            'review_date' => '2024-12-15',
            'next_review_date' => '2025-06-15',
            'overall_rating' => 4.0,
            'performance_score' => 85.0,
            'strengths' => 'Excellent communication skills',
            'areas_for_improvement' => 'Time management',
            'recommendations' => 'Continue current performance',
            'employee_comments' => 'I am satisfied with my performance',
            'reviewer_comments' => 'Employee shows great potential'
        ];

        $review = $this->service->createReview($data);

        $this->assertInstanceOf(EmployeePerformanceReviewDTO::class, $review);
        $this->assertEquals($this->employee->id, $review->employee_id);
        $this->assertEquals($this->reviewer->id, $review->reviewer_id);
        $this->assertEquals(4.0, $review->overall_rating);
        $this->assertEquals(85.0, $review->performance_score);
    }

    /** @test */
    public function it_can_update_employee_performance_review()
    {
        $review = EmployeePerformanceReview::factory()->create([
            'employee_id' => $this->employee->id,
            'reviewer_id' => $this->reviewer->id,
            'status' => EmployeePerformanceReviewStatus::DRAFT
        ]);

        $updateData = [
            'overall_rating' => 4.5,
            'performance_score' => 90.0,
            'strengths' => 'Updated strengths',
            'areas_for_improvement' => 'Updated improvement areas'
        ];

        $updatedReview = $this->service->updateReview($review->id, $updateData);

        $this->assertInstanceOf(EmployeePerformanceReviewDTO::class, $updatedReview);
        $this->assertEquals(4.5, $updatedReview->overall_rating);
        $this->assertEquals(90.0, $updatedReview->performance_score);
        $this->assertEquals('Updated strengths', $updatedReview->strengths);
    }

    /** @test */
    public function it_can_delete_employee_performance_review()
    {
        $review = EmployeePerformanceReview::factory()->create([
            'employee_id' => $this->employee->id,
            'reviewer_id' => $this->reviewer->id
        ]);

        $result = $this->service->deleteReview($review->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('employee_performance_reviews', ['id' => $review->id]);
    }

    /** @test */
    public function it_can_submit_review_for_approval()
    {
        $review = EmployeePerformanceReview::factory()->create([
            'employee_id' => $this->employee->id,
            'reviewer_id' => $this->reviewer->id,
            'status' => EmployeePerformanceReviewStatus::DRAFT
        ]);

        $result = $this->service->submitReview($review->id);

        $this->assertTrue($result);
        $this->assertDatabaseHas('employee_performance_reviews', [
            'id' => $review->id,
            'status' => EmployeePerformanceReviewStatus::PENDING_APPROVAL
        ]);
    }

    /** @test */
    public function it_can_approve_review()
    {
        $review = EmployeePerformanceReview::factory()->create([
            'employee_id' => $this->employee->id,
            'reviewer_id' => $this->reviewer->id,
            'status' => EmployeePerformanceReviewStatus::PENDING_APPROVAL
        ]);

        $result = $this->service->approveReview($review->id, $this->approver->id);

        $this->assertTrue($result);
        $this->assertDatabaseHas('employee_performance_reviews', [
            'id' => $review->id,
            'status' => EmployeePerformanceReviewStatus::APPROVED,
            'is_approved' => true,
            'approved_by' => $this->approver->id
        ]);
    }

    /** @test */
    public function it_can_reject_review()
    {
        $review = EmployeePerformanceReview::factory()->create([
            'employee_id' => $this->employee->id,
            'reviewer_id' => $this->reviewer->id,
            'status' => EmployeePerformanceReviewStatus::PENDING_APPROVAL
        ]);

        $reason = 'Incomplete information provided';
        $result = $this->service->rejectReview($review->id, $this->approver->id, $reason);

        $this->assertTrue($result);
        $this->assertDatabaseHas('employee_performance_reviews', [
            'id' => $review->id,
            'status' => EmployeePerformanceReviewStatus::REJECTED
        ]);
    }

    /** @test */
    public function it_can_assign_reviewer()
    {
        $review = EmployeePerformanceReview::factory()->create([
            'employee_id' => $this->employee->id,
            'reviewer_id' => $this->reviewer->id
        ]);

        $newReviewer = User::factory()->create();
        $result = $this->service->assignReviewer($review->id, $newReviewer->id);

        $this->assertTrue($result);
        $this->assertDatabaseHas('employee_performance_reviews', [
            'id' => $review->id,
            'reviewer_id' => $newReviewer->id
        ]);
    }

    /** @test */
    public function it_can_schedule_review()
    {
        $review = EmployeePerformanceReview::factory()->create([
            'employee_id' => $this->employee->id,
            'reviewer_id' => $this->reviewer->id
        ]);

        $reviewDate = '2025-01-15';
        $result = $this->service->scheduleReview($review->id, $reviewDate);

        $this->assertTrue($result);
        $this->assertDatabaseHas('employee_performance_reviews', [
            'id' => $review->id,
            'review_date' => $reviewDate
        ]);
    }

    /** @test */
    public function it_can_get_employee_reviews()
    {
        // Create multiple reviews for the same employee
        EmployeePerformanceReview::factory()->count(3)->create([
            'employee_id' => $this->employee->id,
            'reviewer_id' => $this->reviewer->id
        ]);

        $reviews = $this->service->getEmployeeReviews($this->employee->id);

        $this->assertCount(3, $reviews);
        $reviews->each(function ($review) {
            $this->assertEquals($this->employee->id, $review->employee_id);
        });
    }

    /** @test */
    public function it_can_get_employee_latest_review()
    {
        // Create reviews with different dates
        EmployeePerformanceReview::factory()->create([
            'employee_id' => $this->employee->id,
            'reviewer_id' => $this->reviewer->id,
            'review_date' => '2023-12-15'
        ]);

        $latestReview = EmployeePerformanceReview::factory()->create([
            'employee_id' => $this->employee->id,
            'reviewer_id' => $this->reviewer->id,
            'review_date' => '2024-12-15'
        ]);

        $result = $this->service->getEmployeeLatestReview($this->employee->id);

        $this->assertInstanceOf(EmployeePerformanceReviewDTO::class, $result);
        $this->assertEquals($latestReview->id, $result->id);
    }

    /** @test */
    public function it_can_get_employee_rating_history()
    {
        EmployeePerformanceReview::factory()->create([
            'employee_id' => $this->employee->id,
            'reviewer_id' => $this->reviewer->id,
            'overall_rating' => 3.5,
            'review_date' => '2023-12-15'
        ]);

        EmployeePerformanceReview::factory()->create([
            'employee_id' => $this->employee->id,
            'reviewer_id' => $this->reviewer->id,
            'overall_rating' => 4.0,
            'review_date' => '2024-12-15'
        ]);

        $ratingHistory = $this->service->getEmployeeRatingHistory($this->employee->id);

        $this->assertCount(2, $ratingHistory);
        $this->assertEquals(3.5, $ratingHistory->first()->overall_rating);
        $this->assertEquals(4.0, $ratingHistory->last()->overall_rating);
    }

    /** @test */
    public function it_can_calculate_employee_average_rating()
    {
        EmployeePerformanceReview::factory()->create([
            'employee_id' => $this->employee->id,
            'reviewer_id' => $this->reviewer->id,
            'overall_rating' => 3.0
        ]);

        EmployeePerformanceReview::factory()->create([
            'employee_id' => $this->employee->id,
            'reviewer_id' => $this->reviewer->id,
            'overall_rating' => 5.0
        ]);

        $averageRating = $this->service->getEmployeeAverageRating($this->employee->id);

        $this->assertEquals(4.0, $averageRating);
    }

    /** @test */
    public function it_can_get_pending_approvals()
    {
        EmployeePerformanceReview::factory()->create([
            'employee_id' => $this->employee->id,
            'reviewer_id' => $this->reviewer->id,
            'status' => EmployeePerformanceReviewStatus::PENDING_APPROVAL
        ]);

        $pendingApprovals = $this->service->getPendingApprovals();

        $this->assertCount(1, $pendingApprovals);
        $this->assertEquals(EmployeePerformanceReviewStatus::PENDING_APPROVAL, $pendingApprovals->first()->status);
    }

    /** @test */
    public function it_can_get_overdue_reviews()
    {
        EmployeePerformanceReview::factory()->create([
            'employee_id' => $this->employee->id,
            'reviewer_id' => $this->reviewer->id,
            'review_date' => now()->subDays(30),
            'status' => EmployeePerformanceReviewStatus::DRAFT
        ]);

        $overdueReviews = $this->service->getOverdueReviews();

        $this->assertCount(1, $overdueReviews);
    }

    /** @test */
    public function it_can_get_upcoming_reviews()
    {
        EmployeePerformanceReview::factory()->create([
            'employee_id' => $this->employee->id,
            'reviewer_id' => $this->reviewer->id,
            'review_date' => now()->addDays(7)
        ]);

        $upcomingReviews = $this->service->getUpcomingReviews();

        $this->assertCount(1, $upcomingReviews);
    }

    /** @test */
    public function it_can_search_reviews()
    {
        EmployeePerformanceReview::factory()->create([
            'employee_id' => $this->employee->id,
            'reviewer_id' => $this->reviewer->id,
            'strengths' => 'Excellent communication skills'
        ]);

        $searchResults = $this->service->searchReviews('communication');

        $this->assertCount(1, $searchResults);
        $this->assertStringContainsString('communication', $searchResults->first()->strengths);
    }

    /** @test */
    public function it_can_get_review_statistics()
    {
        // Create reviews with different statuses and ratings
        EmployeePerformanceReview::factory()->create([
            'employee_id' => $this->employee->id,
            'reviewer_id' => $this->reviewer->id,
            'status' => EmployeePerformanceReviewStatus::APPROVED,
            'overall_rating' => 4.0
        ]);

        EmployeePerformanceReview::factory()->create([
            'employee_id' => $this->employee->id,
            'reviewer_id' => $this->reviewer->id,
            'status' => EmployeePerformanceReviewStatus::PENDING_APPROVAL,
            'overall_rating' => 3.5
        ]);

        $statistics = $this->service->getReviewStatistics();

        $this->assertIsArray($statistics);
        $this->assertArrayHasKey('total_reviews', $statistics);
        $this->assertArrayHasKey('approved_count', $statistics);
        $this->assertArrayHasKey('pending_count', $statistics);
        $this->assertArrayHasKey('average_rating', $statistics);
    }

    /** @test */
    public function it_can_generate_performance_report()
    {
        EmployeePerformanceReview::factory()->count(3)->create([
            'employee_id' => $this->employee->id,
            'reviewer_id' => $this->reviewer->id,
            'overall_rating' => 4.0
        ]);

        $report = $this->service->generatePerformanceReport($this->employee->id, 'year');

        $this->assertIsArray($report);
        $this->assertArrayHasKey('employee_id', $report);
        $this->assertArrayHasKey('period', $report);
        $this->assertArrayHasKey('total_reviews', $report);
        $this->assertArrayHasKey('average_rating', $report);
    }

    /** @test */
    public function it_can_export_reviews()
    {
        EmployeePerformanceReview::factory()->count(2)->create([
            'employee_id' => $this->employee->id,
            'reviewer_id' => $this->reviewer->id
        ]);

        $exportData = $this->service->exportReviews([], 'json');

        $this->assertIsString($exportData);
        $this->assertNotEmpty($exportData);
    }

    /** @test */
    public function it_can_import_reviews()
    {
        $importData = json_encode([
            [
                'employee_id' => $this->employee->id,
                'reviewer_id' => $this->reviewer->id,
                'review_period_start' => '2024-01-01',
                'review_period_end' => '2024-12-31',
                'review_date' => '2024-12-15',
                'next_review_date' => '2025-06-15',
                'overall_rating' => 4.0,
                'performance_score' => 85.0
            ]
        ]);

        $result = $this->service->importReviews($importData, 'json');

        $this->assertTrue($result);
        $this->assertDatabaseHas('employee_performance_reviews', [
            'employee_id' => $this->employee->id,
            'overall_rating' => 4.0
        ]);
    }

    /** @test */
    public function it_can_bulk_approve_reviews()
    {
        $reviews = EmployeePerformanceReview::factory()->count(3)->create([
            'employee_id' => $this->employee->id,
            'reviewer_id' => $this->reviewer->id,
            'status' => EmployeePerformanceReviewStatus::PENDING_APPROVAL
        ]);

        $reviewIds = $reviews->pluck('id')->toArray();
        $result = $this->service->bulkApproveReviews($reviewIds);

        $this->assertTrue($result);

        foreach ($reviewIds as $reviewId) {
            $this->assertDatabaseHas('employee_performance_reviews', [
                'id' => $reviewId,
                'status' => EmployeePerformanceReviewStatus::APPROVED
            ]);
        }
    }

    /** @test */
    public function it_can_bulk_reject_reviews()
    {
        $reviews = EmployeePerformanceReview::factory()->count(3)->create([
            'employee_id' => $this->employee->id,
            'reviewer_id' => $this->reviewer->id,
            'status' => EmployeePerformanceReviewStatus::PENDING_APPROVAL
        ]);

        $reviewIds = $reviews->pluck('id')->toArray();
        $reason = 'Incomplete documentation';
        $result = $this->service->bulkRejectReviews($reviewIds, $reason);

        $this->assertTrue($result);

        foreach ($reviewIds as $reviewId) {
            $this->assertDatabaseHas('employee_performance_reviews', [
                'id' => $reviewId,
                'status' => EmployeePerformanceReviewStatus::REJECTED
            ]);
        }
    }

    /** @test */
    public function it_can_send_review_reminders()
    {
        // Create overdue reviews
        EmployeePerformanceReview::factory()->create([
            'employee_id' => $this->employee->id,
            'reviewer_id' => $this->reviewer->id,
            'review_date' => now()->subDays(30),
            'status' => EmployeePerformanceReviewStatus::DRAFT
        ]);

        $result = $this->service->sendReviewReminders();

        $this->assertTrue($result);
    }

    /** @test */
    public function it_validates_required_fields_on_creation()
    {
        $data = [
            'employee_id' => $this->employee->id,
            // Missing required fields
        ];

        $this->expectException(\Exception::class);
        $this->service->createReview($data);
    }

    /** @test */
    public function it_validates_rating_range()
    {
        $data = [
            'employee_id' => $this->employee->id,
            'reviewer_id' => $this->reviewer->id,
            'review_period_start' => '2024-01-01',
            'review_period_end' => '2024-12-31',
            'review_date' => '2024-12-15',
            'next_review_date' => '2025-06-15',
            'overall_rating' => 6.0, // Invalid rating
            'performance_score' => 85.0
        ];

        $this->expectException(\Exception::class);
        $this->service->createReview($data);
    }

    /** @test */
    public function it_validates_performance_score_range()
    {
        $data = [
            'employee_id' => $this->employee->id,
            'reviewer_id' => $this->reviewer->id,
            'review_period_start' => '2024-01-01',
            'review_period_end' => '2024-12-31',
            'review_date' => '2024-12-15',
            'next_review_date' => '2025-06-15',
            'overall_rating' => 4.0,
            'performance_score' => 150.0 // Invalid score
        ];

        $this->expectException(\Exception::class);
        $this->service->createReview($data);
    }

    /** @test */
    public function it_validates_date_ranges()
    {
        $data = [
            'employee_id' => $this->employee->id,
            'reviewer_id' => $this->reviewer->id,
            'review_period_start' => '2024-12-31',
            'review_period_end' => '2024-01-01', // End before start
            'review_date' => '2024-12-15',
            'next_review_date' => '2024-12-10', // Next review before current
            'overall_rating' => 4.0,
            'performance_score' => 85.0
        ];

        $this->expectException(\Exception::class);
        $this->service->createReview($data);
    }

    /** @test */
    public function it_can_handle_goals_tracking()
    {
        $data = [
            'employee_id' => $this->employee->id,
            'reviewer_id' => $this->reviewer->id,
            'review_period_start' => '2024-01-01',
            'review_period_end' => '2024-12-31',
            'review_date' => '2024-12-15',
            'next_review_date' => '2025-06-15',
            'overall_rating' => 4.0,
            'performance_score' => 85.0,
            'goals_achieved' => ['Goal 1', 'Goal 2'],
            'goals_missed' => ['Goal 3']
        ];

        $review = $this->service->createReview($data);

        $this->assertEquals(['Goal 1', 'Goal 2'], $review->goals_achieved);
        $this->assertEquals(['Goal 3'], $review->goals_missed);
    }

    /** @test */
    public function it_can_handle_workflow_transitions()
    {
        $review = EmployeePerformanceReview::factory()->create([
            'employee_id' => $this->employee->id,
            'reviewer_id' => $this->reviewer->id,
            'status' => EmployeePerformanceReviewStatus::DRAFT
        ]);

        // Draft -> Submitted
        $this->service->submitReview($review->id);
        $this->assertEquals(EmployeePerformanceReviewStatus::PENDING_APPROVAL, $review->fresh()->status);

        // Submitted -> Approved
        $this->service->approveReview($review->id, $this->approver->id);
        $this->assertEquals(EmployeePerformanceReviewStatus::APPROVED, $review->fresh()->status);
    }

    /** @test */
    public function it_can_handle_soft_deletes()
    {
        $review = EmployeePerformanceReview::factory()->create([
            'employee_id' => $this->employee->id,
            'reviewer_id' => $this->reviewer->id
        ]);

        $this->service->deleteReview($review->id);

        $this->assertSoftDeleted('employee_performance_reviews', ['id' => $review->id]);
    }

    /** @test */
    public function it_can_handle_rating_calculations()
    {
        EmployeePerformanceReview::factory()->create([
            'employee_id' => $this->employee->id,
            'reviewer_id' => $this->reviewer->id,
            'overall_rating' => 3.0
        ]);

        EmployeePerformanceReview::factory()->create([
            'employee_id' => $this->employee->id,
            'reviewer_id' => $this->reviewer->id,
            'overall_rating' => 4.0
        ]);

        EmployeePerformanceReview::factory()->create([
            'employee_id' => $this->employee->id,
            'reviewer_id' => $this->reviewer->id,
            'overall_rating' => 5.0
        ]);

        $averageRating = $this->service->getEmployeeAverageRating($this->employee->id);
        $this->assertEquals(4.0, $averageRating);

        $ratingHistory = $this->service->getEmployeeRatingHistory($this->employee->id);
        $this->assertCount(3, $ratingHistory);
    }

    /** @test */
    public function it_can_handle_department_statistics()
    {
        $departmentId = 1; // Assuming department ID

        EmployeePerformanceReview::factory()->create([
            'employee_id' => $this->employee->id,
            'reviewer_id' => $this->reviewer->id,
            'overall_rating' => 4.0
        ]);

        $statistics = $this->service->getDepartmentReviewStatistics($departmentId);

        $this->assertIsArray($statistics);
        $this->assertArrayHasKey('total_reviews', $statistics);
        $this->assertArrayHasKey('average_rating', $statistics);
    }

    /** @test */
    public function it_can_handle_company_statistics()
    {
        EmployeePerformanceReview::factory()->count(5)->create([
            'employee_id' => $this->employee->id,
            'reviewer_id' => $this->reviewer->id,
            'overall_rating' => 4.0
        ]);

        $statistics = $this->service->getCompanyReviews();

        $this->assertCount(5, $statistics);
    }
}
