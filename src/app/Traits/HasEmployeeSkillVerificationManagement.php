<?php

namespace App\Traits;

use App\Models\EmployeeSkill;
use App\DTOs\EmployeeSkillDTO;
use App\Repositories\Interfaces\EmployeeSkillRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

trait HasEmployeeSkillVerificationManagement
{
    protected EmployeeSkillRepositoryInterface $employeeSkillRepository;

    /**
     * Verify an employee skill
     */
    public function verifyEmployeeSkill(EmployeeSkill $skill, int $verifiedBy): bool
    {
        try {
            $result = $this->employeeSkillRepository->verify($skill, $verifiedBy);
            
            if ($result) {
                // Clear related caches
                $this->clearVerificationCaches($skill->employee_id);
                
                // Log the verification
                Log::info('Employee skill verified', [
                    'skill_id' => $skill->id,
                    'employee_id' => $skill->employee_id,
                    'verified_by' => $verifiedBy,
                    'skill_name' => $skill->skill_name
                ]);
            }
            
            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to verify employee skill', [
                'skill_id' => $skill->id,
                'verified_by' => $verifiedBy,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Unverify an employee skill
     */
    public function unverifyEmployeeSkill(EmployeeSkill $skill): bool
    {
        try {
            $result = $this->employeeSkillRepository->unverify($skill);
            
            if ($result) {
                $this->clearVerificationCaches($skill->employee_id);
                
                Log::info('Employee skill unverified', [
                    'skill_id' => $skill->id,
                    'employee_id' => $skill->employee_id,
                    'skill_name' => $skill->skill_name
                ]);
            }
            
            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to unverify employee skill', [
                'skill_id' => $skill->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get all verified skills for an employee
     */
    public function getEmployeeVerifiedSkills(int $employeeId): Collection
    {
        return Cache::remember("employee_verified_skills_{$employeeId}", 3600, function () use ($employeeId) {
            return $this->employeeSkillRepository->findVerified()
                ->where('employee_id', $employeeId);
        });
    }

    /**
     * Get all verified skills for an employee as DTOs
     */
    public function getEmployeeVerifiedSkillsDTO(int $employeeId): Collection
    {
        return $this->getEmployeeVerifiedSkills($employeeId)->map(function ($skill) {
            return EmployeeSkillDTO::fromModel($skill);
        });
    }

    /**
     * Get all unverified skills for an employee
     */
    public function getEmployeeUnverifiedSkills(int $employeeId): Collection
    {
        return Cache::remember("employee_unverified_skills_{$employeeId}", 3600, function () use ($employeeId) {
            return $this->employeeSkillRepository->findUnverified()
                ->where('employee_id', $employeeId);
        });
    }

    /**
     * Get all unverified skills for an employee as DTOs
     */
    public function getEmployeeUnverifiedSkillsDTO(int $employeeId): Collection
    {
        return $this->getEmployeeUnverifiedSkills($employeeId)->map(function ($skill) {
            return EmployeeSkillDTO::fromModel($skill);
        });
    }

    /**
     * Get verification statistics for an employee
     */
    public function getEmployeeVerificationStats(int $employeeId): array
    {
        $totalSkills = $this->employeeSkillRepository->getEmployeeSkillCount($employeeId);
        $verifiedSkills = $this->getEmployeeVerifiedSkills($employeeId)->count();
        $unverifiedSkills = $this->getEmployeeUnverifiedSkills($employeeId)->count();
        
        return [
            'total_skills' => $totalSkills,
            'verified_skills' => $verifiedSkills,
            'unverified_skills' => $unverifiedSkills,
            'verification_rate' => $totalSkills > 0 ? round(($verifiedSkills / $totalSkills) * 100, 2) : 0,
            'pending_verification' => $unverifiedSkills,
        ];
    }

    /**
     * Get verification statistics for all employees
     */
    public function getAllEmployeesVerificationStats(): array
    {
        $totalSkills = $this->employeeSkillRepository->getTotalSkillCount();
        $verifiedSkills = $this->employeeSkillRepository->getTotalVerifiedSkillsCount();
        $unverifiedSkills = $totalSkills - $verifiedSkills;
        
        return [
            'total_skills' => $totalSkills,
            'verified_skills' => $verifiedSkills,
            'unverified_skills' => $unverifiedSkills,
            'verification_rate' => $totalSkills > 0 ? round(($verifiedSkills / $totalSkills) * 100, 2) : 0,
            'pending_verification' => $unverifiedSkills,
        ];
    }

    /**
     * Get skills pending verification
     */
    public function getSkillsPendingVerification(): Collection
    {
        return Cache::remember('skills_pending_verification', 1800, function () {
            return $this->employeeSkillRepository->findUnverified();
        });
    }

    /**
     * Get skills pending verification as DTOs
     */
    public function getSkillsPendingVerificationDTO(): Collection
    {
        return $this->getSkillsPendingVerification()->map(function ($skill) {
            return EmployeeSkillDTO::fromModel($skill);
        });
    }

    /**
     * Get skills pending verification for a specific employee
     */
    public function getEmployeeSkillsPendingVerification(int $employeeId): Collection
    {
        return $this->getEmployeeUnverifiedSkills($employeeId);
    }

    /**
     * Get skills pending verification for a specific employee as DTOs
     */
    public function getEmployeeSkillsPendingVerificationDTO(int $employeeId): Collection
    {
        return $this->getEmployeeSkillsPendingVerification($employeeId)->map(function ($skill) {
            return EmployeeSkillDTO::fromModel($skill);
        });
    }

    /**
     * Check if an employee has any unverified skills
     */
    public function employeeHasUnverifiedSkills(int $employeeId): bool
    {
        return $this->getEmployeeUnverifiedSkills($employeeId)->isNotEmpty();
    }

    /**
     * Check if an employee has any verified skills
     */
    public function employeeHasVerifiedSkills(int $employeeId): bool
    {
        return $this->getEmployeeVerifiedSkills($employeeId)->isNotEmpty();
    }

    /**
     * Get verification history for a skill
     */
    public function getSkillVerificationHistory(EmployeeSkill $skill): array
    {
        return [
            'skill_id' => $skill->id,
            'skill_name' => $skill->skill_name,
            'is_verified' => $skill->is_verified,
            'verified_by' => $skill->verified_by,
            'verified_at' => $skill->verified_at,
            'verification_status' => $skill->is_verified ? 'Verified' : 'Unverified',
            'verifier_name' => $skill->verifiedBy ? $skill->verifiedBy->name : null,
        ];
    }

    /**
     * Get verification summary for an employee
     */
    public function getEmployeeVerificationSummary(int $employeeId): array
    {
        $verifiedSkills = $this->getEmployeeVerifiedSkills($employeeId);
        $unverifiedSkills = $this->getEmployeeUnverifiedSkills($employeeId);
        
        return [
            'employee_id' => $employeeId,
            'total_skills' => $verifiedSkills->count() + $unverifiedSkills->count(),
            'verified_skills' => $verifiedSkills->count(),
            'unverified_skills' => $unverifiedSkills->count(),
            'verification_rate' => $this->getEmployeeVerificationStats($employeeId)['verification_rate'],
            'recently_verified' => $verifiedSkills->where('verified_at', '>=', now()->subDays(30))->count(),
            'pending_verification' => $unverifiedSkills->count(),
            'verification_status' => $this->getVerificationStatus($employeeId),
        ];
    }

    /**
     * Get verification status for an employee
     */
    private function getVerificationStatus(int $employeeId): string
    {
        $stats = $this->getEmployeeVerificationStats($employeeId);
        
        if ($stats['verification_rate'] >= 90) {
            return 'Excellent';
        } elseif ($stats['verification_rate'] >= 75) {
            return 'Good';
        } elseif ($stats['verification_rate'] >= 50) {
            return 'Fair';
        } else {
            return 'Needs Improvement';
        }
    }

    /**
     * Clear verification-related caches
     */
    private function clearVerificationCaches(int $employeeId): void
    {
        Cache::forget("employee_verified_skills_{$employeeId}");
        Cache::forget("employee_unverified_skills_{$employeeId}");
        Cache::forget('skills_pending_verification');
        Cache::forget('total_verified_skills_count');
    }
}
