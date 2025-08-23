<?php

namespace App\Repositories;

use App\DTOs\EmployeeSkillDTO;
use App\Enums\ProficiencyLevel;
use App\Enums\SkillCategory;
use App\Models\EmployeeSkill;
use App\Repositories\Interfaces\EmployeeSkillRepositoryInterface;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Employee; // Added missing import
use Carbon\Carbon; // Added missing import

class EmployeeSkillRepository implements EmployeeSkillRepositoryInterface
{
    public function __construct(
        private EmployeeSkill $model
    ) {
    }

    // Basic CRUD operations
    public function all(): Collection
    {
        return Cache::remember('employee_skills_all', 3600, function () {
            return $this->model->with(['employee', 'verifiedBy'])->get();
        });
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['employee', 'verifiedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function simplePaginate(int $perPage = 15): Paginator
    {
        return $this->model->with(['employee', 'verifiedBy'])
            ->orderBy('created_at', 'desc')
            ->simplePaginate($perPage);
    }

    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator
    {
        return $this->model->with(['employee', 'verifiedBy'])
            ->orderBy('id')
            ->cursorPaginate($perPage, ['*'], 'cursor', $cursor);
    }

    public function find(int $id): ?EmployeeSkill
    {
        return Cache::remember("employee_skill_{$id}", 3600, function () use ($id) {
            return $this->model->with(['employee', 'verifiedBy'])->find($id);
        });
    }

    public function findDTO(int $id): ?EmployeeSkillDTO
    {
        $skill = $this->find($id);
        return $skill ? EmployeeSkillDTO::fromModel($skill) : null;
    }

    public function create(array $data): EmployeeSkill
    {
        try {
            DB::beginTransaction();
            
            $skill = $this->model->create($data);
            
            // Clear relevant caches
            $this->clearCaches($skill->employee_id);
            
            DB::commit();
            
            return $skill->load(['employee', 'verifiedBy']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create employee skill', [
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function createAndReturnDTO(array $data): EmployeeSkillDTO
    {
        $skill = $this->create($data);
        return EmployeeSkillDTO::fromModel($skill);
    }

    public function update(EmployeeSkill $skill, array $data): bool
    {
        try {
            DB::beginTransaction();
            
            $updated = $skill->update($data);
            
            if ($updated) {
                // Clear relevant caches
                $this->clearCaches($skill->employee_id);
            }
            
            DB::commit();
            
            return $updated;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update employee skill', [
                'skill_id' => $skill->id,
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function updateAndReturnDTO(EmployeeSkill $skill, array $data): ?EmployeeSkillDTO
    {
        $updated = $this->update($skill, $data);
        return $updated ? EmployeeSkillDTO::fromModel($skill->fresh(['employee', 'verifiedBy'])) : null;
    }

    public function delete(EmployeeSkill $skill): bool
    {
        try {
            DB::beginTransaction();
            
            $deleted = $skill->delete();
            
            if ($deleted) {
                // Clear relevant caches
                $this->clearCaches($skill->employee_id);
            }
            
            DB::commit();
            
            return $deleted;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete employee skill', [
                'skill_id' => $skill->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    // Helper methods
    private function clearCaches(int $employeeId): void
    {
        Cache::forget('employee_skills_all');
        Cache::forget("employee_skills_employee_{$employeeId}");
        Cache::forget("employee_skill_count_{$employeeId}");
        Cache::forget("employee_total_experience_{$employeeId}");
        Cache::forget("employee_avg_proficiency_{$employeeId}");
        Cache::forget("employee_primary_skills_{$employeeId}");
        Cache::forget("employee_certified_skills_{$employeeId}");
        Cache::forget('total_skill_count');
        Cache::forget('total_certified_skills_count');
        Cache::forget('total_verified_skills_count');
    }

    // Complete implementation of all interface methods
    public function findByEmployeeId(int $employeeId): Collection 
    { 
        return Cache::remember("employee_skills_employee_{$employeeId}", 3600, function () use ($employeeId) {
            return $this->model->with(['employee', 'verifiedBy'])
                ->where('employee_id', $employeeId)
                ->orderBy('is_primary', 'desc')
                ->orderBy('proficiency_level', 'desc')
                ->get();
        });
    }

    public function findByEmployeeIdDTO(int $employeeId): Collection 
    { 
        return $this->findByEmployeeId($employeeId)->map(function ($skill) {
            return EmployeeSkillDTO::fromModel($skill);
        });
    }

    public function findBySkillName(string $skillName): Collection 
    { 
        return $this->model->with(['employee', 'verifiedBy'])
            ->where('skill_name', 'like', "%{$skillName}%")
            ->get();
    }

    public function findBySkillNameDTO(string $skillName): Collection 
    { 
        return $this->findBySkillName($skillName)->map(function ($skill) {
            return EmployeeSkillDTO::fromModel($skill);
        });
    }

    public function findBySkillCategory(string $skillCategory): Collection 
    { 
        return $this->model->with(['employee', 'verifiedBy'])
            ->where('skill_category', $skillCategory)
            ->get();
    }

    public function findBySkillCategoryDTO(string $skillCategory): Collection 
    { 
        return $this->findBySkillCategory($skillCategory)->map(function ($skill) {
            return EmployeeSkillDTO::fromModel($skill);
        });
    }

    public function findByProficiencyLevel(string $proficiencyLevel): Collection 
    { 
        return $this->model->with(['employee', 'verifiedBy'])
            ->where('proficiency_level', $proficiencyLevel)
            ->get();
    }

    public function findByProficiencyLevelDTO(string $proficiencyLevel): Collection 
    { 
        return $this->findByProficiencyLevel($proficiencyLevel)->map(function ($skill) {
            return EmployeeSkillDTO::fromModel($skill);
        });
    }

    public function findByEmployeeAndCategory(int $employeeId, string $skillCategory): Collection 
    { 
        return $this->model->with(['employee', 'verifiedBy'])
            ->where('employee_id', $employeeId)
            ->where('skill_category', $skillCategory)
            ->get();
    }

    public function findByEmployeeAndCategoryDTO(int $employeeId, string $skillCategory): Collection 
    { 
        return $this->findByEmployeeAndCategory($employeeId, $skillCategory)->map(function ($skill) {
            return EmployeeSkillDTO::fromModel($skill);
        });
    }

    public function findActive(): Collection 
    { 
        return $this->model->with(['employee', 'verifiedBy'])
            ->active()
            ->get();
    }

    public function findActiveDTO(): Collection 
    { 
        return $this->findActive()->map(function ($skill) {
            return EmployeeSkillDTO::fromModel($skill);
        });
    }

    public function findVerified(): Collection 
    { 
        return $this->model->with(['employee', 'verifiedBy'])
            ->verified()
            ->get();
    }

    public function findVerifiedDTO(): Collection 
    { 
        return $this->findVerified()->map(function ($skill) {
            return EmployeeSkillDTO::fromModel($skill);
        });
    }

    public function findUnverified(): Collection 
    { 
        return $this->model->with(['employee', 'verifiedBy'])
            ->unverified()
            ->get();
    }

    public function findUnverifiedDTO(): Collection 
    { 
        return $this->findUnverified()->map(function ($skill) {
            return EmployeeSkillDTO::fromModel($skill);
        });
    }

    public function findPrimary(): Collection 
    { 
        return $this->model->with(['employee', 'verifiedBy'])
            ->primary()
            ->get();
    }

    public function findPrimaryDTO(): Collection 
    { 
        return $this->findPrimary()->map(function ($skill) {
            return EmployeeSkillDTO::fromModel($skill);
        });
    }

    public function findRequired(): Collection 
    { 
        return $this->model->with(['employee', 'verifiedBy'])
            ->required()
            ->get();
    }

    public function findRequiredDTO(): Collection 
    { 
        return $this->findRequired()->map(function ($skill) {
            return EmployeeSkillDTO::fromModel($skill);
        });
    }

    public function findCertified(): Collection 
    { 
        return $this->model->with(['employee', 'verifiedBy'])
            ->certified()
            ->get();
    }

    public function findCertifiedDTO(): Collection 
    { 
        return $this->findCertified()->map(function ($skill) {
            return EmployeeSkillDTO::fromModel($skill);
        });
    }

    public function findExpiringCertifications(int $days = 30): Collection 
    { 
        return $this->model->with(['employee', 'verifiedBy'])
            ->expiringCertifications($days)
            ->get();
    }

    public function findExpiringCertificationsDTO(int $days = 30): Collection 
    { 
        return $this->findExpiringCertifications($days)->map(function ($skill) {
            return EmployeeSkillDTO::fromModel($skill);
        });
    }

    public function findByKeywords(array $keywords): Collection 
    { 
        $query = $this->model->with(['employee', 'verifiedBy']);
        
        foreach ($keywords as $keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('skill_name', 'like', "%{$keyword}%")
                  ->orWhere('skill_description', 'like', "%{$keyword}%")
                  ->orWhereJsonContains('keywords', $keyword);
            });
        }
        
        return $query->get();
    }

    public function findByKeywordsDTO(array $keywords): Collection 
    { 
        return $this->findByKeywords($keywords)->map(function ($skill) {
            return EmployeeSkillDTO::fromModel($skill);
        });
    }

    public function findByTags(array $tags): Collection 
    { 
        $query = $this->model->with(['employee', 'verifiedBy']);
        
        foreach ($tags as $tag) {
            $query->whereJsonContains('tags', $tag);
        }
        
        return $query->get();
    }

    public function findByTagsDTO(array $tags): Collection 
    { 
        return $this->findByTags($tags)->map(function ($skill) {
            return EmployeeSkillDTO::fromModel($skill);
        });
    }

    public function findByExperienceRange(int $minYears, int $maxYears): Collection 
    { 
        return $this->model->with(['employee', 'verifiedBy'])
            ->whereBetween('years_experience', [$minYears, $maxYears])
            ->get();
    }

    public function findByExperienceRangeDTO(int $minYears, int $maxYears): Collection 
    { 
        return $this->findByExperienceRange($minYears, $maxYears)->map(function ($skill) {
            return EmployeeSkillDTO::fromModel($skill);
        });
    }

    public function verify(EmployeeSkill $skill, int $verifiedBy): bool 
    { 
        try {
            $skill->update([
                'is_verified' => true,
                'verified_by' => $verifiedBy,
                'verified_at' => now(),
            ]);
            
            $this->clearCaches($skill->employee_id);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to verify employee skill', [
                'skill_id' => $skill->id,
                'verified_by' => $verifiedBy,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function unverify(EmployeeSkill $skill): bool 
    { 
        try {
            $skill->update([
                'is_verified' => false,
                'verified_by' => null,
                'verified_at' => null,
            ]);
            
            $this->clearCaches($skill->employee_id);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to unverify employee skill', [
                'skill_id' => $skill->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function activate(EmployeeSkill $skill): bool 
    { 
        try {
            $skill->update(['is_active' => true]);
            $this->clearCaches($skill->employee_id);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to activate employee skill', [
                'skill_id' => $skill->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function deactivate(EmployeeSkill $skill): bool 
    { 
        try {
            $skill->update(['is_active' => false]);
            $this->clearCaches($skill->employee_id);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to deactivate employee skill', [
                'skill_id' => $skill->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function setAsPrimary(EmployeeSkill $skill): bool 
    { 
        try {
            DB::beginTransaction();
            
            // Remove primary from other skills of the same employee
            $this->model->where('employee_id', $skill->employee_id)
                ->where('id', '!=', $skill->id)
                ->update(['is_primary' => false]);
            
            // Set this skill as primary
            $skill->update(['is_primary' => true]);
            
            DB::commit();
            $this->clearCaches($skill->employee_id);
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to set employee skill as primary', [
                'skill_id' => $skill->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function removePrimary(EmployeeSkill $skill): bool 
    { 
        try {
            $skill->update(['is_primary' => false]);
            $this->clearCaches($skill->employee_id);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to remove primary from employee skill', [
                'skill_id' => $skill->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function addCertification(EmployeeSkill $skill, array $certData): bool 
    { 
        try {
            $skill->update([
                'certification_name' => $certData['certification_name'] ?? null,
                'certification_date' => $certData['certification_date'] ?? null,
                'certification_expiry' => $certData['certification_expiry'] ?? null,
                'certification_required' => true,
            ]);
            
            $this->clearCaches($skill->employee_id);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to add certification to employee skill', [
                'skill_id' => $skill->id,
                'cert_data' => $certData,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function updateCertification(EmployeeSkill $skill, array $certData): bool 
    { 
        try {
            $skill->update([
                'certification_name' => $certData['certification_name'] ?? $skill->certification_name,
                'certification_date' => $certData['certification_date'] ?? $skill->certification_date,
                'certification_expiry' => $certData['certification_expiry'] ?? $skill->certification_expiry,
            ]);
            
            $this->clearCaches($skill->employee_id);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to update certification for employee skill', [
                'skill_id' => $skill->id,
                'cert_data' => $certData,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function removeCertification(EmployeeSkill $skill): bool 
    { 
        try {
            $skill->update([
                'certification_name' => null,
                'certification_date' => null,
                'certification_expiry' => null,
                'certification_required' => false,
            ]);
            
            $this->clearCaches($skill->employee_id);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to remove certification from employee skill', [
                'skill_id' => $skill->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function addTags(EmployeeSkill $skill, array $tags): bool 
    { 
        try {
            $currentTags = $skill->tags ?? [];
            $newTags = array_unique(array_merge($currentTags, $tags));
            
            $skill->update(['tags' => $newTags]);
            $this->clearCaches($skill->employee_id);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to add tags to employee skill', [
                'skill_id' => $skill->id,
                'tags' => $tags,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function removeTags(EmployeeSkill $skill, array $tags): bool 
    { 
        try {
            $currentTags = $skill->tags ?? [];
            $newTags = array_diff($currentTags, $tags);
            
            $skill->update(['tags' => array_values($newTags)]);
            $this->clearCaches($skill->employee_id);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to remove tags from employee skill', [
                'skill_id' => $skill->id,
                'tags' => $tags,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function clearTags(EmployeeSkill $skill): bool 
    { 
        try {
            $skill->update(['tags' => null]);
            $this->clearCaches($skill->employee_id);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to clear tags from employee skill', [
                'skill_id' => $skill->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function getEmployeeSkillCount(int $employeeId): int 
    { 
        return Cache::remember("employee_skill_count_{$employeeId}", 3600, function () use ($employeeId) {
            return $this->model->where('employee_id', $employeeId)->count();
        });
    }

    public function getEmployeeSkillCountByCategory(int $employeeId, string $skillCategory): int 
    { 
        return $this->model->where('employee_id', $employeeId)
            ->where('skill_category', $skillCategory)
            ->count();
    }

    public function getEmployeeSkillCountByLevel(int $employeeId, string $proficiencyLevel): int 
    { 
        return $this->model->where('employee_id', $employeeId)
            ->where('proficiency_level', $proficiencyLevel)
            ->count();
    }

    public function getEmployeeTotalExperience(int $employeeId): int 
    { 
        return Cache::remember("employee_total_experience_{$employeeId}", 3600, function () use ($employeeId) {
            return $this->model->where('employee_id', $employeeId)
                ->sum('years_experience');
        });
    }

    public function getEmployeeAverageProficiency(int $employeeId): float 
    { 
        return Cache::remember("employee_avg_proficiency_{$employeeId}", 3600, function () use ($employeeId) {
            $skills = $this->model->where('employee_id', $employeeId)->get();
            
            if ($skills->isEmpty()) {
                return 0.0;
            }
            
            $totalProficiency = $skills->sum(function ($skill) {
                return $skill->getProficiencyNumericValue();
            });
            
            return round($totalProficiency / $skills->count(), 2);
        });
    }

    public function getEmployeePrimarySkills(int $employeeId): Collection 
    { 
        return Cache::remember("employee_primary_skills_{$employeeId}", 3600, function () use ($employeeId) {
            return $this->model->with(['employee', 'verifiedBy'])
                ->where('employee_id', $employeeId)
                ->where('is_primary', true)
                ->get();
        });
    }

    public function getEmployeePrimarySkillsDTO(int $employeeId): Collection 
    { 
        return $this->getEmployeePrimarySkills($employeeId)->map(function ($skill) {
            return EmployeeSkillDTO::fromModel($skill);
        });
    }

    public function getEmployeeCertifiedSkills(int $employeeId): Collection 
    { 
        return Cache::remember("employee_certified_skills_{$employeeId}", 3600, function () use ($employeeId) {
            return $this->model->with(['employee', 'verifiedBy'])
                ->where('employee_id', $employeeId)
                ->whereNotNull('certification_name')
                ->get();
        });
    }

    public function getEmployeeCertifiedSkillsDTO(int $employeeId): Collection 
    { 
        return $this->getEmployeeCertifiedSkills($employeeId)->map(function ($skill) {
            return EmployeeSkillDTO::fromModel($skill);
        });
    }

    public function getTotalSkillCount(): int 
    { 
        return Cache::remember('total_skill_count', 3600, function () {
            return $this->model->count();
        });
    }

    public function getTotalSkillCountByCategory(string $skillCategory): int 
    { 
        return $this->model->where('skill_category', $skillCategory)->count();
    }

    public function getTotalSkillCountByLevel(string $proficiencyLevel): int 
    { 
        return $this->model->where('proficiency_level', $proficiencyLevel)->count();
    }

    public function getTotalCertifiedSkillsCount(): int 
    { 
        return Cache::remember('total_certified_skills_count', 3600, function () {
            return $this->model->whereNotNull('certification_name')->count();
        });
    }

    public function getTotalVerifiedSkillsCount(): int 
    { 
        return Cache::remember('total_verified_skills_count', 3600, function () {
            return $this->model->where('is_verified', true)->count();
        });
    }

    public function getExpiringCertificationsCount(int $days = 30): int 
    { 
        return $this->model->expiringCertifications($days)->count();
    }

    public function searchSkills(string $query): Collection 
    { 
        return $this->model->with(['employee', 'verifiedBy'])
            ->where(function ($q) use ($query) {
                $q->where('skill_name', 'like', "%{$query}%")
                  ->orWhere('skill_description', 'like', "%{$query}%")
                  ->orWhereJsonContains('keywords', $query)
                  ->orWhereJsonContains('tags', $query);
            })
            ->get();
    }

    public function searchSkillsDTO(string $query): Collection 
    { 
        return $this->searchSkills($query)->map(function ($skill) {
            return EmployeeSkillDTO::fromModel($skill);
        });
    }

    public function searchSkillsByEmployee(int $employeeId, string $query): Collection 
    { 
        return $this->model->with(['employee', 'verifiedBy'])
            ->where('employee_id', $employeeId)
            ->where(function ($q) use ($query) {
                $q->where('skill_name', 'like', "%{$query}%")
                  ->orWhere('skill_description', 'like', "%{$query}%")
                  ->orWhereJsonContains('keywords', $query)
                  ->orWhereJsonContains('tags', $query);
            })
            ->get();
    }

    public function searchSkillsByEmployeeDTO(int $employeeId, string $query): Collection 
    { 
        return $this->searchSkillsByEmployee($employeeId, $query)->map(function ($skill) {
            return EmployeeSkillDTO::fromModel($skill);
        });
    }

    public function exportSkillsData(array $filters = []): string 
    { 
        try {
            $query = $this->model->with(['employee', 'verifiedBy']);
            
            // Apply filters
            if (isset($filters['employee_id'])) {
                $query->where('employee_id', $filters['employee_id']);
            }
            
            if (isset($filters['skill_category'])) {
                $query->where('skill_category', $filters['skill_category']);
            }
            
            if (isset($filters['proficiency_level'])) {
                $query->where('proficiency_level', $filters['proficiency_level']);
            }
            
            if (isset($filters['is_verified'])) {
                $query->where('is_verified', $filters['is_verified']);
            }
            
            $skills = $query->get();
            
            // Convert to CSV format
            $csv = "ID,Employee ID,Skill Name,Category,Proficiency Level,Years Experience,Certification Required,Certification Name,Certification Date,Certification Expiry,Is Verified,Verified By,Verified At,Is Active,Is Primary,Is Required,Description,Keywords,Tags,Notes,Created At,Updated At\n";
            
            foreach ($skills as $skill) {
                $csv .= implode(',', [
                    $skill->id,
                    $skill->employee_id,
                    '"' . str_replace('"', '""', $skill->skill_name) . '"',
                    $skill->skill_category->value,
                    $skill->proficiency_level->value,
                    $skill->years_experience,
                    $skill->certification_required ? 'Yes' : 'No',
                    '"' . str_replace('"', '""', $skill->certification_name ?? '') . '"',
                    $skill->certification_date?->format('Y-m-d') ?? '',
                    $skill->certification_expiry?->format('Y-m-d') ?? '',
                    $skill->is_verified ? 'Yes' : 'No',
                    $skill->verified_by ?? '',
                    $skill->verified_at?->format('Y-m-d H:i:s') ?? '',
                    $skill->is_active ? 'Yes' : 'No',
                    $skill->is_primary ? 'Yes' : 'No',
                    $skill->is_required ? 'Yes' : 'No',
                    '"' . str_replace('"', '""', $skill->skill_description ?? '') . '"',
                    '"' . str_replace('"', '""', $skill->getKeywordsString()) . '"',
                    '"' . str_replace('"', '""', $skill->getTagsString()) . '"',
                    '"' . str_replace('"', '""', $skill->notes ?? '') . '"',
                    $skill->created_at->format('Y-m-d H:i:s'),
                    $skill->updated_at->format('Y-m-d H:i:s'),
                ]) . "\n";
            }
            
            return $csv;
        } catch (\Exception $e) {
            Log::error('Failed to export skills data', [
                'filters' => $filters,
                'error' => $e->getMessage()
            ]);
            return '';
        }
    }

    public function importSkillsData(string $data): bool 
    { 
        try {
            DB::beginTransaction();
            
            $lines = explode("\n", trim($data));
            $headers = str_getcsv(array_shift($lines));
            
            foreach ($lines as $line) {
                if (empty(trim($line))) continue;
                
                $row = array_combine($headers, str_getcsv($line));
                
                // Find employee by ID or name
                $employee = Employee::find($row['Employee ID']) ?? 
                           Employee::where('first_name', 'like', "%{$row['Employee ID']}%")->first();
                
                if (!$employee) {
                    Log::warning('Employee not found for skill import', ['employee_id' => $row['Employee ID']]);
                    continue;
                }
                
                // Create or update skill
                $skillData = [
                    'employee_id' => $employee->id,
                    'skill_name' => $row['Skill Name'],
                    'skill_category' => $row['Category'],
                    'proficiency_level' => $row['Proficiency Level'],
                    'years_experience' => (int) ($row['Years Experience'] ?? 0),
                    'certification_required' => $row['Certification Required'] === 'Yes',
                    'certification_name' => $row['Certification Name'] ?: null,
                    'certification_date' => $row['Certification Date'] ? Carbon::parse($row['Certification Date']) : null,
                    'certification_expiry' => $row['Certification Expiry'] ? Carbon::parse($row['Certification Expiry']) : null,
                    'is_verified' => $row['Is Verified'] === 'Yes',
                    'is_active' => $row['Is Active'] === 'Yes',
                    'is_primary' => $row['Is Primary'] === 'Yes',
                    'is_required' => $row['Is Required'] === 'Yes',
                    'skill_description' => $row['Description'] ?: null,
                    'keywords' => $row['Keywords'] ? explode(',', $row['Keywords']) : null,
                    'tags' => $row['Tags'] ? explode(',', $row['Tags']) : null,
                    'notes' => $row['Notes'] ?: null,
                ];
                
                $this->model->updateOrCreate(
                    ['employee_id' => $employee->id, 'skill_name' => $row['Skill Name']],
                    $skillData
                );
            }
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to import skills data', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function getSkillsStatistics(int $employeeId = null): array 
    { 
        try {
            $query = $this->model;
            
            if ($employeeId) {
                $query->where('employee_id', $employeeId);
            }
            
            $totalSkills = $query->count();
            $verifiedSkills = $query->where('is_verified', true)->count();
            $certifiedSkills = $query->whereNotNull('certification_name')->count();
            $primarySkills = $query->where('is_primary', true)->count();
            $requiredSkills = $query->where('is_required', true)->count();
            
            $categoryStats = $query->selectRaw('skill_category, COUNT(*) as count')
                ->groupBy('skill_category')
                ->pluck('count', 'skill_category')
                ->toArray();
            
            $levelStats = $query->selectRaw('proficiency_level, COUNT(*) as count')
                ->groupBy('proficiency_level')
                ->pluck('count', 'proficiency_level')
                ->toArray();
            
            $avgExperience = $query->avg('years_experience');
            
            return [
                'total_skills' => $totalSkills,
                'verified_skills' => $verifiedSkills,
                'certified_skills' => $certifiedSkills,
                'primary_skills' => $primarySkills,
                'required_skills' => $requiredSkills,
                'category_distribution' => $categoryStats,
                'proficiency_distribution' => $levelStats,
                'average_experience' => round($avgExperience, 2),
                'verification_rate' => $totalSkills > 0 ? round(($verifiedSkills / $totalSkills) * 100, 2) : 0,
                'certification_rate' => $totalSkills > 0 ? round(($certifiedSkills / $totalSkills) * 100, 2) : 0,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get skills statistics', [
                'employee_id' => $employeeId,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    public function getDepartmentSkillsStatistics(int $departmentId): array 
    { 
        try {
            $employeeIds = Employee::where('department_id', $departmentId)->pluck('id');
            
            if ($employeeIds->isEmpty()) {
                return [];
            }
            
            $query = $this->model->whereIn('employee_id', $employeeIds);
            
            return $this->getSkillsStatistics(null, $query);
        } catch (\Exception $e) {
            Log::error('Failed to get department skills statistics', [
                'department_id' => $departmentId,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    public function getCompanySkillsStatistics(): array 
    { 
        try {
            return $this->getSkillsStatistics();
        } catch (\Exception $e) {
            Log::error('Failed to get company skills statistics', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    public function getSkillsGapAnalysis(int $employeeId = null): array 
    { 
        try {
            $query = $this->model;
            
            if ($employeeId) {
                $query->where('employee_id', $employeeId);
            }
            
            // Get required skills vs actual skills
            $requiredSkills = $query->where('is_required', true)->get();
            $actualSkills = $query->get();
            
            $gaps = [];
            
            foreach ($requiredSkills as $required) {
                $actual = $actualSkills->where('skill_name', $required->skill_name)->first();
                
                if (!$actual) {
                    $gaps[] = [
                        'skill_name' => $required->skill_name,
                        'category' => $required->skill_category->value,
                        'required_level' => $required->proficiency_level->value,
                        'actual_level' => null,
                        'gap_type' => 'missing',
                        'gap_score' => 100,
                    ];
                } elseif ($actual->getProficiencyNumericValue() < $required->getProficiencyNumericValue()) {
                    $gapScore = (($required->getProficiencyNumericValue() - $actual->getProficiencyNumericValue()) / $required->getProficiencyNumericValue()) * 100;
                    
                    $gaps[] = [
                        'skill_name' => $required->skill_name,
                        'category' => $required->skill_category->value,
                        'required_level' => $required->proficiency_level->value,
                        'actual_level' => $actual->proficiency_level->value,
                        'gap_type' => 'proficiency',
                        'gap_score' => round($gapScore, 2),
                    ];
                }
            }
            
            return [
                'total_gaps' => count($gaps),
                'missing_skills' => count(array_filter($gaps, fn($g) => $g['gap_type'] === 'missing')),
                'proficiency_gaps' => count(array_filter($gaps, fn($g) => $g['gap_type'] === 'proficiency')),
                'gaps' => $gaps,
                'average_gap_score' => count($gaps) > 0 ? round(array_sum(array_column($gaps, 'gap_score')) / count($gaps), 2) : 0,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get skills gap analysis', [
                'employee_id' => $employeeId,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    public function getSkillsTrends(string $startDate = null, string $endDate = null): array 
    { 
        try {
            $query = $this->model;
            
            if ($startDate) {
                $query->where('created_at', '>=', Carbon::parse($startDate));
            }
            
            if ($endDate) {
                $query->where('created_at', '<=', Carbon::parse($endDate));
            }
            
            $trends = $query->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->pluck('count', 'date')
                ->toArray();
            
            $categoryTrends = $query->selectRaw('DATE(created_at) as date, skill_category, COUNT(*) as count')
                ->groupBy('date', 'skill_category')
                ->orderBy('date')
                ->get()
                ->groupBy('date')
                ->map(function ($items) {
                    return $items->pluck('count', 'skill_category')->toArray();
                })
                ->toArray();
            
            return [
                'daily_trends' => $trends,
                'category_trends' => $categoryTrends,
                'total_period' => array_sum($trends),
                'average_daily' => count($trends) > 0 ? round(array_sum($trends) / count($trends), 2) : 0,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get skills trends', [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }
}
