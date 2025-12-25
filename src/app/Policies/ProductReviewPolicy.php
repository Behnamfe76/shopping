<?php

namespace Fereydooni\Shopping\app\Policies;

use Fereydooni\Shopping\app\Enums\ReviewStatus;
use Fereydooni\Shopping\app\Models\ProductReview;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User;

class ProductReviewPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any product reviews.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('product-review.view.any') ||
               $user->can('product-review.view.own') ||
               $user->can('product-review.view.approved');
    }

    /**
     * Determine whether the user can view the product review.
     */
    public function view(User $user, ProductReview $review): bool
    {
        // Users can view their own reviews
        if ($user->id === $review->user_id && $user->can('product-review.view.own')) {
            return true;
        }

        // Users can view approved reviews
        if ($review->status === ReviewStatus::APPROVED && $user->can('product-review.view.approved')) {
            return true;
        }

        // Users with any view permission can view all reviews
        return $user->can('product-review.view.any');
    }

    /**
     * Determine whether the user can view approved reviews.
     */
    public function viewApproved(User $user): bool
    {
        return $user->can('product-review.view.approved');
    }

    /**
     * Determine whether the user can view pending reviews.
     */
    public function viewPending(User $user): bool
    {
        return $user->can('product-review.view.pending');
    }

    /**
     * Determine whether the user can view rejected reviews.
     */
    public function viewRejected(User $user): bool
    {
        return $user->can('product-review.view.rejected');
    }

    /**
     * Determine whether the user can create product reviews.
     */
    public function create(User $user): bool
    {
        return $user->can('product-review.create.own') || $user->can('product-review.create.any');
    }

    /**
     * Determine whether the user can update the product review.
     */
    public function update(User $user, ProductReview $review): bool
    {
        // Users can update their own reviews
        if ($user->id === $review->user_id && $user->can('product-review.update.own')) {
            return true;
        }

        // Users with any update permission can update all reviews
        return $user->can('product-review.update.any');
    }

    /**
     * Determine whether the user can delete the product review.
     */
    public function delete(User $user, ProductReview $review): bool
    {
        // Users can delete their own reviews
        if ($user->id === $review->user_id && $user->can('product-review.delete.own')) {
            return true;
        }

        // Users with any delete permission can delete all reviews
        return $user->can('product-review.delete.any');
    }

    /**
     * Determine whether the user can approve the product review.
     */
    public function approve(User $user, ProductReview $review): bool
    {
        // Users can approve their own reviews
        if ($user->id === $review->user_id && $user->can('product-review.approve.own')) {
            return true;
        }

        // Users with any approve permission can approve all reviews
        return $user->can('product-review.approve.any');
    }

    /**
     * Determine whether the user can reject the product review.
     */
    public function reject(User $user, ProductReview $review): bool
    {
        // Users can reject their own reviews
        if ($user->id === $review->user_id && $user->can('product-review.reject.own')) {
            return true;
        }

        // Users with any reject permission can reject all reviews
        return $user->can('product-review.reject.any');
    }

    /**
     * Determine whether the user can feature the product review.
     */
    public function feature(User $user, ProductReview $review): bool
    {
        // Users can feature their own reviews
        if ($user->id === $review->user_id && $user->can('product-review.feature.own')) {
            return true;
        }

        // Users with any feature permission can feature all reviews
        return $user->can('product-review.feature.any');
    }

    /**
     * Determine whether the user can verify the product review.
     */
    public function verify(User $user, ProductReview $review): bool
    {
        // Users can verify their own reviews
        if ($user->id === $review->user_id && $user->can('product-review.verify.own')) {
            return true;
        }

        // Users with any verify permission can verify all reviews
        return $user->can('product-review.verify.any');
    }

    /**
     * Determine whether the user can vote on the product review.
     */
    public function vote(User $user, ProductReview $review): bool
    {
        // Users cannot vote on their own reviews
        if ($user->id === $review->user_id) {
            return false;
        }

        // Users can vote on any review
        if ($user->can('product-review.vote.any')) {
            return true;
        }

        // Users can vote on their own reviews (if they have the permission)
        return $user->can('product-review.vote.own');
    }

    /**
     * Determine whether the user can flag the product review.
     */
    public function flag(User $user, ProductReview $review): bool
    {
        // Users can flag any review
        if ($user->can('product-review.flag.any')) {
            return true;
        }

        // Users can flag their own reviews
        if ($user->id === $review->user_id && $user->can('product-review.flag.own')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can moderate the product review.
     */
    public function moderate(User $user, ProductReview $review): bool
    {
        // Users can moderate their own reviews
        if ($user->id === $review->user_id && $user->can('product-review.moderate.own')) {
            return true;
        }

        // Users with any moderate permission can moderate all reviews
        return $user->can('product-review.moderate.any');
    }

    /**
     * Determine whether the user can search product reviews.
     */
    public function search(User $user): bool
    {
        return $user->can('product-review.search.any') || $user->can('product-review.search.own');
    }

    /**
     * Determine whether the user can export product reviews.
     */
    public function export(User $user): bool
    {
        return $user->can('product-review.export');
    }

    /**
     * Determine whether the user can import product reviews.
     */
    public function import(User $user): bool
    {
        return $user->can('product-review.import');
    }

    /**
     * Determine whether the user can view review analytics.
     */
    public function viewAnalytics(User $user): bool
    {
        return $user->can('product-review.analytics.view.any') || $user->can('product-review.analytics.view.own');
    }

    /**
     * Determine whether the user can view review reports.
     */
    public function viewReports(User $user): bool
    {
        return $user->can('product-review.reports.view') || $user->can('product-review.reports.generate');
    }
}
