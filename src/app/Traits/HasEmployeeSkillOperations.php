<?php

namespace App\Traits;

use App\DTOs\EmployeeSkillDTO;
use App\Models\EmployeeSkill;
use App\Repositories\Interfaces\EmployeeSkillRepositoryInterface;
use Illuminate\Support\Collection;

trait HasEmployeeSkillOperations
{
    protected EmployeeSkillRepositoryInterface $employeeSkillRepository;

    /**
     * Get all employee skills.
     */
    public function getAllEmployeeSkills(): Collection
    {
        return $this->employeeSkillRepository->all();
    }

    /**
     * Get employee skills by employee ID.
     */
    public function getEmployeeSkills(int $employeeId): Collection
    {
        return $this->employeeSkillRepository->findByEmployeeId($employeeId);
    }

    /**
     * Get employee skills by employee ID as DTOs.
     */
    public function getEmployeeSkillsDTO(int $employeeId): Collection
    {
        return $this->employeeSkillRepository->findByEmployeeIdDTO($employeeId);
    }

    /**
     * Find employee skill by ID.
     */
    public function findEmployeeSkill(int $skillId): ?EmployeeSkill
    {
        return $this->employeeSkillRepository->find($skillId);
    }

    /**
     * Find employee skill by ID as DTO.
     */
    public function findEmployeeSkillDTO(int $skillId): ?EmployeeSkillDTO
    {
        return $this->employeeSkillRepository->findDTO($skillId);
    }

    /**
     * Create a new employee skill.
     */
    public function createEmployeeSkill(array $data): EmployeeSkill
    {
        return $this->employeeSkillRepository->create($data);
    }

    /**
     * Create a new employee skill and return as DTO.
     */
    public function createEmployeeSkillDTO(array $data): EmployeeSkillDTO
    {
        return $this->employeeSkillRepository->createAndReturnDTO($data);
    }

    /**
     * Update an employee skill.
     */
    public function updateEmployeeSkill(EmployeeSkill $skill, array $data): bool
    {
        return $this->employeeSkillRepository->update($skill, $data);
    }

    /**
     * Update an employee skill and return as DTO.
     */
    public function updateEmployeeSkillDTO(EmployeeSkill $skill, array $data): ?EmployeeSkillDTO
    {
        return $this->employeeSkillRepository->updateAndReturnDTO($skill, $data);
    }

    /**
     * Delete an employee skill.
     */
    public function deleteEmployeeSkill(EmployeeSkill $skill): bool
    {
        return $this->employeeSkillRepository->delete($skill);
    }

    /**
     * Get employee skills by skill name.
     */
    public function getEmployeeSkillsByName(string $skillName): Collection
    {
        return $this->employeeSkillRepository->findBySkillName($skillName);
    }

    /**
     * Get employee skills by skill name as DTOs.
     */
    public function getEmployeeSkillsByNameDTO(string $skillName): Collection
    {
        return $this->employeeSkillRepository->findBySkillNameDTO($skillName);
    }

    /**
     * Get employee skills by category.
     */
    public function getEmployeeSkillsByCategory(string $skillCategory): Collection
    {
        return $this->employeeSkillRepository->findBySkillCategory($skillCategory);
    }

    /**
     * Get employee skills by category as DTOs.
     */
    public function getEmployeeSkillsByCategoryDTO(string $skillCategory): Collection
    {
        return $this->employeeSkillRepository->findBySkillCategoryDTO($skillCategory);
    }

    /**
     * Get employee skills by proficiency level.
     */
    public function getEmployeeSkillsByLevel(string $proficiencyLevel): Collection
    {
        return $this->employeeSkillRepository->findByProficiencyLevel($proficiencyLevel);
    }

    /**
     * Get employee skills by proficiency level as DTOs.
     */
    public function getEmployeeSkillsByLevelDTO(string $proficiencyLevel): Collection
    {
        return $this->employeeSkillRepository->findByProficiencyLevelDTO($proficiencyLevel);
    }

    /**
     * Get employee skills by employee and category.
     */
    public function getEmployeeSkillsByEmployeeAndCategory(int $employeeId, string $skillCategory): Collection
    {
        return $this->employeeSkillRepository->findByEmployeeAndCategory($employeeId, $skillCategory);
    }

    /**
     * Get employee skills by employee and category as DTOs.
     */
    public function getEmployeeSkillsByEmployeeAndCategoryDTO(int $employeeId, string $skillCategory): Collection
    {
        return $this->employeeSkillRepository->findByEmployeeAndCategoryDTO($employeeId, $skillCategory);
    }

    /**
     * Get active employee skills.
     */
    public function getActiveEmployeeSkills(): Collection
    {
        return $this->employeeSkillRepository->findActive();
    }

    /**
     * Get active employee skills as DTOs.
     */
    public function getActiveEmployeeSkillsDTO(): Collection
    {
        return $this->employeeSkillRepository->findActiveDTO();
    }

    /**
     * Get verified employee skills.
     */
    public function getVerifiedEmployeeSkills(): Collection
    {
        return $this->employeeSkillRepository->findVerified();
    }

    /**
     * Get verified employee skills as DTOs.
     */
    public function getVerifiedEmployeeSkillsDTO(): Collection
    {
        return $this->employeeSkillRepository->findVerifiedDTO();
    }

    /**
     * Get unverified employee skills.
     */
    public function getUnverifiedEmployeeSkills(): Collection
    {
        return $this->employeeSkillRepository->findUnverified();
    }

    /**
     * Get unverified employee skills as DTOs.
     */
    public function getUnverifiedEmployeeSkillsDTO(): Collection
    {
        return $this->employeeSkillRepository->findUnverifiedDTO();
    }

    /**
     * Get primary employee skills.
     */
    public function getPrimaryEmployeeSkills(): Collection
    {
        return $this->employeeSkillRepository->findPrimary();
    }

    /**
     * Get primary employee skills as DTOs.
     */
    public function getPrimaryEmployeeSkillsDTO(): Collection
    {
        return $this->employeeSkillRepository->findPrimaryDTO();
    }

    /**
     * Get required employee skills.
     */
    public function getRequiredEmployeeSkills(): Collection
    {
        return $this->employeeSkillRepository->findRequired();
    }

    /**
     * Get required employee skills as DTOs.
     */
    public function getRequiredEmployeeSkillsDTO(): Collection
    {
        return $this->employeeSkillRepository->findRequiredDTO();
    }

    /**
     * Get certified employee skills.
     */
    public function getCertifiedEmployeeSkills(): Collection
    {
        return $this->employeeSkillRepository->findCertified();
    }

    /**
     * Get certified employee skills as DTOs.
     */
    public function getCertifiedEmployeeSkillsDTO(): Collection
    {
        return $this->employeeSkillRepository->findCertifiedDTO();
    }

    /**
     * Search employee skills.
     */
    public function searchEmployeeSkills(string $query): Collection
    {
        return $this->employeeSkillRepository->searchSkills($query);
    }

    /**
     * Search employee skills as DTOs.
     */
    public function searchEmployeeSkillsDTO(string $query): Collection
    {
        return $this->employeeSkillRepository->searchSkillsDTO($query);
    }

    /**
     * Search employee skills by employee.
     */
    public function searchEmployeeSkillsByEmployee(int $employeeId, string $query): Collection
    {
        return $this->employeeSkillRepository->searchSkillsByEmployee($employeeId, $query);
    }

    /**
     * Search employee skills by employee as DTOs.
     */
    public function searchEmployeeSkillsByEmployeeDTO(int $employeeId, string $query): Collection
    {
        return $this->employeeSkillRepository->searchSkillsByEmployeeDTO($employeeId, $query);
    }

    /**
     * Get employee skills by keywords.
     */
    public function getEmployeeSkillsByKeywords(array $keywords): Collection
    {
        return $this->employeeSkillRepository->findByKeywords($keywords);
    }

    /**
     * Get employee skills by keywords as DTOs.
     */
    public function getEmployeeSkillsByKeywordsDTO(array $keywords): Collection
    {
        return $this->employeeSkillRepository->findByKeywordsDTO($keywords);
    }

    /**
     * Get employee skills by tags.
     */
    public function getEmployeeSkillsByTags(array $tags): Collection
    {
        return $this->employeeSkillRepository->findByTags($tags);
    }

    /**
     * Get employee skills by tags as DTOs.
     */
    public function getEmployeeSkillsByTagsDTO(array $tags): Collection
    {
        return $this->employeeSkillRepository->findByTagsDTO($tags);
    }

    /**
     * Get employee skills by experience range.
     */
    public function getEmployeeSkillsByExperienceRange(int $minYears, int $maxYears): Collection
    {
        return $this->employeeSkillRepository->findByExperienceRange($minYears, $maxYears);
    }

    /**
     * Get employee skills by experience range as DTOs.
     */
    public function getEmployeeSkillsByExperienceRangeDTO(int $minYears, int $maxYears): Collection
    {
        return $this->employeeSkillRepository->findByExperienceRangeDTO($minYears, $maxYears);
    }

    /**
     * Get employee skills with expiring certifications.
     */
    public function getEmployeeSkillsWithExpiringCertifications(int $days = 30): Collection
    {
        return $this->employeeSkillRepository->findExpiringCertifications($days);
    }

    /**
     * Get employee skills with expiring certifications as DTOs.
     */
    public function getEmployeeSkillsWithExpiringCertificationsDTO(int $days = 30): Collection
    {
        return $this->employeeSkillRepository->findExpiringCertificationsDTO($days);
    }

    /**
     * Export employee skills data.
     */
    public function exportEmployeeSkillsData(array $filters = []): string
    {
        return $this->employeeSkillRepository->exportSkillsData($filters);
    }

    /**
     * Import employee skills data.
     */
    public function importEmployeeSkillsData(string $data): bool
    {
        return $this->employeeSkillRepository->importSkillsData($data);
    }
}
