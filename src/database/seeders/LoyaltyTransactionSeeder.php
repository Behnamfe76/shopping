<?php

namespace Fereydooni\Shopping\database\seeders;

use Carbon\Carbon;
use Fereydooni\Shopping\app\Enums\LoyaltyReferenceType;
use Fereydooni\Shopping\app\Enums\LoyaltyTransactionStatus;
use Fereydooni\Shopping\app\Enums\LoyaltyTransactionType;
use Fereydooni\Shopping\app\Models\Customer;
use Fereydooni\Shopping\app\Models\LoyaltyTransaction;
use Illuminate\Database\Seeder;

class LoyaltyTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing customers and users
        $customers = Customer::take(10)->get();
        $users = \App\Models\User::take(5)->get();

        if ($customers->isEmpty() || $users->isEmpty()) {
            $this->command->warn('No customers or users found. Please seed customers and users first.');

            return;
        }

        $this->command->info('Seeding loyalty transactions...');

        // Create sample loyalty transactions
        foreach ($customers as $customer) {
            $this->createCustomerLoyaltyTransactions($customer, $users);
        }

        $this->command->info('Loyalty transactions seeded successfully!');
    }

    /**
     * Create loyalty transactions for a specific customer
     */
    protected function createCustomerLoyaltyTransactions($customer, $users): void
    {
        $transactionTypes = [
            LoyaltyTransactionType::EARNED,
            LoyaltyTransactionType::BONUS,
            LoyaltyTransactionType::REDEEMED,
            LoyaltyTransactionType::ADJUSTMENT,
        ];

        $referenceTypes = [
            LoyaltyReferenceType::ORDER,
            LoyaltyReferenceType::CAMPAIGN,
            LoyaltyReferenceType::MANUAL,
            LoyaltyReferenceType::SYSTEM,
        ];

        $statuses = [
            LoyaltyTransactionStatus::COMPLETED,
            LoyaltyTransactionStatus::PENDING,
        ];

        // Create 5-15 transactions per customer
        $transactionCount = rand(5, 15);

        for ($i = 0; $i < $transactionCount; $i++) {
            $transactionType = $transactionTypes[array_rand($transactionTypes)];
            $referenceType = $referenceTypes[array_rand($referenceTypes)];
            $status = $statuses[array_rand($statuses)];
            $user = $users->random();

            // Determine points based on transaction type
            $points = $this->getPointsForTransactionType($transactionType);
            $pointsValue = $points * 0.01; // 1 point = $0.01

            // Create transaction
            $transaction = LoyaltyTransaction::create([
                'customer_id' => $customer->id,
                'user_id' => $user->id,
                'transaction_type' => $transactionType,
                'points' => $points,
                'points_value' => $pointsValue,
                'reference_type' => $referenceType,
                'reference_id' => $this->getReferenceId($referenceType),
                'description' => $this->getDescription($transactionType, $referenceType),
                'reason' => $this->getReason($transactionType),
                'status' => $status,
                'expires_at' => $this->getExpirationDate($transactionType),
                'metadata' => $this->getMetadata($transactionType, $referenceType),
                'created_at' => $this->getRandomDate(),
                'updated_at' => $this->getRandomDate(),
            ]);

            // Update customer loyalty points
            if ($status === LoyaltyTransactionStatus::COMPLETED) {
                if (in_array($transactionType, [LoyaltyTransactionType::EARNED, LoyaltyTransactionType::BONUS, LoyaltyTransactionType::ADJUSTMENT])) {
                    $customer->increment('loyalty_points', $points);
                } elseif ($transactionType === LoyaltyTransactionType::REDEEMED) {
                    $customer->decrement('loyalty_points', $points);
                }
            }
        }
    }

    /**
     * Get points for transaction type
     */
    protected function getPointsForTransactionType(LoyaltyTransactionType $type): int
    {
        return match ($type) {
            LoyaltyTransactionType::EARNED => rand(10, 500),
            LoyaltyTransactionType::BONUS => rand(5, 100),
            LoyaltyTransactionType::REDEEMED => rand(50, 1000),
            LoyaltyTransactionType::ADJUSTMENT => rand(-200, 200),
            default => rand(10, 100),
        };
    }

    /**
     * Get reference ID based on reference type
     */
    protected function getReferenceId(LoyaltyReferenceType $referenceType): ?int
    {
        return match ($referenceType) {
            LoyaltyReferenceType::ORDER => rand(1, 1000),
            LoyaltyReferenceType::PRODUCT => rand(1, 500),
            LoyaltyReferenceType::CAMPAIGN => rand(1, 50),
            LoyaltyReferenceType::MANUAL, LoyaltyReferenceType::SYSTEM => null,
        };
    }

    /**
     * Get description for transaction
     */
    protected function getDescription(LoyaltyTransactionType $transactionType, LoyaltyReferenceType $referenceType): string
    {
        $descriptions = [
            LoyaltyTransactionType::EARNED => [
                LoyaltyReferenceType::ORDER => 'Points earned from order purchase',
                LoyaltyReferenceType::CAMPAIGN => 'Points earned from promotional campaign',
                LoyaltyReferenceType::MANUAL => 'Points added manually by admin',
                LoyaltyReferenceType::SYSTEM => 'Points earned from system activity',
            ],
            LoyaltyTransactionType::BONUS => [
                LoyaltyReferenceType::CAMPAIGN => 'Bonus points from special promotion',
                LoyaltyReferenceType::MANUAL => 'Bonus points awarded manually',
                LoyaltyReferenceType::SYSTEM => 'Bonus points from system reward',
            ],
            LoyaltyTransactionType::REDEEMED => [
                LoyaltyReferenceType::ORDER => 'Points redeemed for order discount',
                LoyaltyReferenceType::MANUAL => 'Points redeemed manually',
                LoyaltyReferenceType::SYSTEM => 'Points redeemed for system reward',
            ],
            LoyaltyTransactionType::ADJUSTMENT => [
                LoyaltyReferenceType::MANUAL => 'Points adjustment by admin',
                LoyaltyReferenceType::SYSTEM => 'Points adjustment by system',
            ],
        ];

        return $descriptions[$transactionType][$referenceType] ?? 'Loyalty transaction';
    }

    /**
     * Get reason for transaction
     */
    protected function getReason(LoyaltyTransactionType $transactionType): ?string
    {
        $reasons = [
            LoyaltyTransactionType::EARNED => [
                'Purchase reward',
                'First order bonus',
                'Seasonal promotion',
                'Referral reward',
            ],
            LoyaltyTransactionType::BONUS => [
                'Birthday bonus',
                'Holiday promotion',
                'Customer appreciation',
                'Special event reward',
            ],
            LoyaltyTransactionType::REDEEMED => [
                'Order discount',
                'Product discount',
                'Service redemption',
                'Gift card purchase',
            ],
            LoyaltyTransactionType::ADJUSTMENT => [
                'Correction of error',
                'Customer service adjustment',
                'System correction',
                'Manual adjustment',
            ],
        ];

        $typeReasons = $reasons[$transactionType] ?? [];

        return ! empty($typeReasons) ? $typeReasons[array_rand($typeReasons)] : null;
    }

    /**
     * Get expiration date for transaction
     */
    protected function getExpirationDate(LoyaltyTransactionType $transactionType): ?Carbon
    {
        // Only earned and bonus points typically expire
        if (in_array($transactionType, [LoyaltyTransactionType::EARNED, LoyaltyTransactionType::BONUS])) {
            // 30% chance of having expiration date
            if (rand(1, 100) <= 30) {
                return Carbon::now()->addDays(rand(30, 365));
            }
        }

        return null;
    }

    /**
     * Get metadata for transaction
     */
    protected function getMetadata(LoyaltyTransactionType $transactionType, LoyaltyReferenceType $referenceType): array
    {
        $metadata = [
            'seeded' => true,
            'transaction_type_label' => $transactionType->label(),
            'reference_type_label' => $referenceType->label(),
        ];

        // Add specific metadata based on transaction type
        switch ($transactionType) {
            case LoyaltyTransactionType::EARNED:
                $metadata['earning_rate'] = rand(1, 10).'%';
                $metadata['order_amount'] = rand(1000, 100000) / 100;
                break;
            case LoyaltyTransactionType::BONUS:
                $metadata['bonus_type'] = ['birthday', 'holiday', 'referral', 'seasonal'][array_rand([0, 1, 2, 3])];
                break;
            case LoyaltyTransactionType::REDEEMED:
                $metadata['redemption_rate'] = rand(1, 5).'%';
                $metadata['discount_amount'] = rand(500, 5000) / 100;
                break;
            case LoyaltyTransactionType::ADJUSTMENT:
                $metadata['adjustment_reason'] = ['correction', 'service', 'system', 'manual'][array_rand([0, 1, 2, 3])];
                break;
        }

        return $metadata;
    }

    /**
     * Get random date within the last year
     */
    protected function getRandomDate(): Carbon
    {
        return Carbon::now()->subDays(rand(0, 365));
    }
}
