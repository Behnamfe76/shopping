<?php

namespace App\Traits;

use App\DTOs\EmployeeSkillDTO;
use App\Models\EmployeeSkill;
use App\Repositories\Interfaces\EmployeeSkillRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

trait HasEmployeeSkillCertificationManagement
{
    protected EmployeeSkillRepositoryInterface $employeeSkillRepository;

    /**
     * Add certification to an employee skill
     */
    public function addCertificationToSkill(EmployeeSkill $skill, array $certData): bool
    {
        try {
            $result = $this->employeeSkillRepository->addCertification($skill, $certData);

            if ($result) {
                $this->clearCertificationCaches($skill->employee_id);

                Log::info('Certification added to employee skill', [
                    'skill_id' => $skill->id,
                    'employee_id' => $skill->employee_id,
                    'certification_name' => $certData['certification_name'] ?? null,
                    'expiry_date' => $certData['certification_expiry'] ?? null,
                ]);
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to add certification to employee skill', [
                'skill_id' => $skill->id,
                'cert_data' => $certData,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Update certification for an employee skill
     */
    public function updateCertificationForSkill(EmployeeSkill $skill, array $certData): bool
    {
        try {
            $result = $this->employeeSkillRepository->updateCertification($skill, $certData);

            if ($result) {
                $this->clearCertificationCaches($skill->employee_id);

                Log::info('Certification updated for employee skill', [
                    'skill_id' => $skill->id,
                    'employee_id' => $skill->employee_id,
                    'certification_name' => $certData['certification_name'] ?? null,
                    'expiry_date' => $certData['certification_expiry'] ?? null,
                ]);
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to update certification for employee skill', [
                'skill_id' => $skill->id,
                'cert_data' => $certData,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Remove certification from an employee skill
     */
    public function removeCertificationFromSkill(EmployeeSkill $skill): bool
    {
        try {
            $result = $this->employeeSkillRepository->removeCertification($skill);

            if ($result) {
                $this->clearCertificationCaches($skill->employee_id);

                Log::info('Certification removed from employee skill', [
                    'skill_id' => $skill->id,
                    'employee_id' => $skill->employee_id,
                ]);
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to remove certification from employee skill', [
                'skill_id' => $skill->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get all certified skills for an employee
     */
    public function getEmployeeCertifiedSkills(int $employeeId): Collection
    {
        return Cache::remember("employee_certified_skills_{$employeeId}", 3600, function () use ($employeeId) {
            return $this->employeeSkillRepository->findCertified()
                ->where('employee_id', $employeeId);
        });
    }

    /**
     * Get all certified skills for an employee as DTOs
     */
    public function getEmployeeCertifiedSkillsDTO(int $employeeId): Collection
    {
        return $this->getEmployeeCertifiedSkills($employeeId)->map(function ($skill) {
            return EmployeeSkillDTO::fromModel($skill);
        });
    }

    /**
     * Get skills with expiring certifications
     */
    public function getSkillsWithExpiringCertifications(int $days = 30): Collection
    {
        return Cache::remember("expiring_certifications_{$days}", 1800, function () use ($days) {
            return $this->employeeSkillRepository->findExpiringCertifications($days);
        });
    }

    /**
     * Get skills with expiring certifications as DTOs
     */
    public function getSkillsWithExpiringCertificationsDTO(int $days = 30): Collection
    {
        return $this->getSkillsWithExpiringCertifications($days)->map(function ($skill) {
            return EmployeeSkillDTO::fromModel($skill);
        });
    }

    /**
     * Get skills with expiring certifications for a specific employee
     */
    public function getEmployeeExpiringCertifications(int $employeeId, int $days = 30): Collection
    {
        return $this->getSkillsWithExpiringCertifications($days)
            ->where('employee_id', $employeeId);
    }

    /**
     * Get skills with expiring certifications for a specific employee as DTOs
     */
    public function getEmployeeExpiringCertificationsDTO(int $employeeId, int $days = 30): Collection
    {
        return $this->getEmployeeExpiringCertifications($employeeId, $days)->map(function ($skill) {
            return EmployeeSkillDTO::fromModel($skill);
        });
    }

    /**
     * Get skills with expired certifications
     */
    public function getSkillsWithExpiredCertifications(): Collection
    {
        return Cache::remember('expired_certifications', 1800, function () {
            return $this->employeeSkillRepository->all()->filter(function ($skill) {
                return $skill->isCertificationExpired();
            });
        });
    }

    /**
     * Get skills with expired certifications as DTOs
     */
    public function getSkillsWithExpiredCertificationsDTO(): Collection
    {
        return $this->getSkillsWithExpiredCertifications()->map(function ($skill) {
            return EmployeeSkillDTO::fromModel($skill);
        });
    }

    /**
     * Get skills with expired certifications for a specific employee
     */
    public function getEmployeeExpiredCertifications(int $employeeId): Collection
    {
        return $this->getSkillsWithExpiredCertifications()
            ->where('employee_id', $employeeId);
    }

    /**
     * Get skills with expired certifications for a specific employee as DTOs
     */
    public function getEmployeeExpiredCertificationsDTO(int $employeeId): Collection
    {
        return $this->getEmployeeExpiredCertifications($employeeId)->map(function ($skill) {
            return EmployeeSkillDTO::fromModel($skill);
        });
    }

    /**
     * Check if a skill certification is expiring soon
     */
    public function isSkillCertificationExpiring(EmployeeSkill $skill, int $days = 30): bool
    {
        return $skill->isCertificationExpiring($days);
    }

    /**
     * Check if a skill certification has expired
     */
    public function isSkillCertificationExpired(EmployeeSkill $skill): bool
    {
        return $skill->isCertificationExpired();
    }

    /**
     * Get certification statistics for an employee
     */
    public function getEmployeeCertificationStats(int $employeeId): array
    {
        $totalSkills = $this->employeeSkillRepository->getEmployeeSkillCount($employeeId);
        $certifiedSkills = $this->getEmployeeCertifiedSkills($employeeId)->count();
        $expiringSkills = $this->getEmployeeExpiringCertifications($employeeId, 30)->count();
        $expiredSkills = $this->getEmployeeExpiredCertifications($employeeId)->count();

        return [
            'total_skills' => $totalSkills,
            'certified_skills' => $certifiedSkills,
            'expiring_soon' => $expiringSkills,
            'expired' => $expiredSkills,
            'certification_rate' => $totalSkills > 0 ? round(($certifiedSkills / $totalSkills) * 100, 2) : 0,
            'valid_certifications' => $certifiedSkills - $expiredSkills,
        ];
    }

    /**
     * Get certification statistics for all employees
     */
    public function getAllEmployeesCertificationStats(): array
    {
        $totalSkills = $this->employeeSkillRepository->getTotalSkillCount();
        $certifiedSkills = $this->employeeSkillRepository->getTotalCertifiedSkillsCount();
        $expiringSkills = $this->getSkillsWithExpiringCertifications(30)->count();
        $expiredSkills = $this->getSkillsWithExpiredCertifications()->count();

        return [
            'total_skills' => $totalSkills,
            'certified_skills' => $certifiedSkills,
            'expiring_soon' => $expiringSkills,
            'expired' => $expiredSkills,
            'certification_rate' => $totalSkills > 0 ? round(($certifiedSkills / $totalSkills) * 100, 2) : 0,
            'valid_certifications' => $certifiedSkills - $expiredSkills,
        ];
    }

    /**
     * Get certification summary for an employee
     */
    public function getEmployeeCertificationSummary(int $employeeId): array
    {
        $certifiedSkills = $this->getEmployeeCertifiedSkills($employeeId);
        $expiringSkills = $this->getEmployeeExpiringCertifications($employeeId, 30);
        $expiredSkills = $this->getEmployeeExpiredCertifications($employeeId);

        return [
            'employee_id' => $employeeId,
            'total_certified' => $certifiedSkills->count(),
            'expiring_soon' => $expiringSkills->count(),
            'expired' => $expiredSkills->count(),
            'valid_certifications' => $certifiedSkills->count() - $expiredSkills->count(),
            'certification_status' => $this->getCertificationStatus($employeeId),
            'next_expiry' => $this->getNextCertificationExpiry($employeeId),
        ];
    }

    /**
     * Get certification status for an employee
     */
    private function getCertificationStatus(int $employeeId): string
    {
        $stats = $this->getEmployeeCertificationStats($employeeId);

        if ($stats['expired'] > 0) {
            return 'Has Expired Certifications';
        } elseif ($stats['expiring_soon'] > 0) {
            return 'Has Expiring Certifications';
        } elseif ($stats['certification_rate'] >= 50) {
            return 'Well Certified';
        } else {
            return 'Limited Certifications';
        }
    }

    /**
     * Get next certification expiry date for an employee
     */
    private function getNextCertificationExpiry(int $employeeId): ?string
    {
        $expiringSkills = $this->getEmployeeExpiringCertifications($employeeId, 365);

        if ($expiringSkills->isEmpty()) {
            return null;
        }

        $nextExpiry = $expiringSkills->min('certification_expiry');

        return $nextExpiry ? $nextExpiry->format('Y-m-d') : null;
    }

    /**
     * Get certification timeline for an employee
     */
    public function getEmployeeCertificationTimeline(int $employeeId): array
    {
        $certifiedSkills = $this->getEmployeeCertifiedSkills($employeeId);

        $timeline = [];

        foreach ($certifiedSkills as $skill) {
            if ($skill->certification_date) {
                $timeline[] = [
                    'skill_name' => $skill->skill_name,
                    'certification_name' => $skill->certification_name,
                    'certification_date' => $skill->certification_date->format('Y-m-d'),
                    'expiry_date' => $skill->certification_expiry ? $skill->certification_expiry->format('Y-m-d') : null,
                    'status' => $skill->isCertificationExpired() ? 'Expired' :
                               ($skill->isCertificationExpiring(30) ? 'Expiring Soon' : 'Valid'),
                ];
            }
        }

        // Sort by certification date (newest first)
        usort($timeline, function ($a, $b) {
            return strtotime($b['certification_date']) - strtotime($a['certification_date']);
        });

        return $timeline;
    }

    /**
     * Clear certification-related caches
     */
    private function clearCertificationCaches(int $employeeId): void
    {
        Cache::forget("employee_certified_skills_{$employeeId}");
        Cache::forget('expiring_certifications_30');
        Cache::forget('expiring_certifications_60');
        Cache::forget('expiring_certifications_90');
        Cache::forget('expired_certifications');
        Cache::forget('total_certified_skills_count');
    }
}
