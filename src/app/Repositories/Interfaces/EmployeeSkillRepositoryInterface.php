<?php

namespace App\Repositories\Interfaces;

use App\DTOs\EmployeeSkillDTO;
use App\Enums\ProficiencyLevel;
use App\Enums\SkillCategory;
use App\Models\EmployeeSkill;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

interface EmployeeSkillRepositoryInterface
{
    // Basic CRUD operations
    public function all(): Collection;
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function simplePaginate(int $perPage = 15): Paginator;
    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator;
    public function find(int $id): ?EmployeeSkill;
    public function findDTO(int $id): ?EmployeeSkillDTO;
    public function create(array $data): EmployeeSkill;
    public function createAndReturnDTO(array $data): EmployeeSkillDTO;
    public function update(EmployeeSkill $skill, array $data): bool;
    public function updateAndReturnDTO(EmployeeSkill $skill, array $data): ?EmployeeSkillDTO;
    public function delete(EmployeeSkill $skill): bool;

    // Find by employee
    public function findByEmployeeId(int $employeeId): Collection;
    public function findByEmployeeIdDTO(int $employeeId): Collection;

    // Find by skill properties
    public function findBySkillName(string $skillName): Collection;
    public function findBySkillNameDTO(string $skillName): Collection;
    public function findBySkillCategory(string $skillCategory): Collection;
    public function findBySkillCategoryDTO(string $skillCategory): Collection;
    public function findByProficiencyLevel(string $proficiencyLevel): Collection;
    public function findByProficiencyLevelDTO(string $proficiencyLevel): Collection;
    public function findByEmployeeAndCategory(int $employeeId, string $skillCategory): Collection;
    public function findByEmployeeAndCategoryDTO(int $employeeId, string $skillCategory): Collection;

    // Find by status
    public function findActive(): Collection;
    public function findActiveDTO(): Collection;
    public function findVerified(): Collection;
    public function findVerifiedDTO(): Collection;
    public function findUnverified(): Collection;
    public function findUnverifiedDTO(): Collection;
    public function findPrimary(): Collection;
    public function findPrimaryDTO(): Collection;
    public function findRequired(): Collection;
    public function findRequiredDTO(): Collection;
    public function findCertified(): Collection;
    public function findCertifiedDTO(): Collection;

    // Find by certification
    public function findExpiringCertifications(int $days = 30): Collection;
    public function findExpiringCertificationsDTO(int $days = 30): Collection;

    // Find by keywords and tags
    public function findByKeywords(array $keywords): Collection;
    public function findByKeywordsDTO(array $keywords): Collection;
    public function findByTags(array $tags): Collection;
    public function findByTagsDTO(array $tags): Collection;

    // Find by experience range
    public function findByExperienceRange(int $minYears, int $maxYears): Collection;
    public function findByExperienceRangeDTO(int $minYears, int $maxYears): Collection;

    // Status management
    public function verify(EmployeeSkill $skill, int $verifiedBy): bool;
    public function unverify(EmployeeSkill $skill): bool;
    public function activate(EmployeeSkill $skill): bool;
    public function deactivate(EmployeeSkill $skill): bool;
    public function setAsPrimary(EmployeeSkill $skill): bool;
    public function removePrimary(EmployeeSkill $skill): bool;

    // Certification management
    public function addCertification(EmployeeSkill $skill, array $certData): bool;
    public function updateCertification(EmployeeSkill $skill, array $certData): bool;
    public function removeCertification(EmployeeSkill $skill): bool;

    // Tag management
    public function addTags(EmployeeSkill $skill, array $tags): bool;
    public function removeTags(EmployeeSkill $skill, array $tags): bool;
    public function clearTags(EmployeeSkill $skill): bool;

    // Employee statistics
    public function getEmployeeSkillCount(int $employeeId): int;
    public function getEmployeeSkillCountByCategory(int $employeeId, string $skillCategory): int;
    public function getEmployeeSkillCountByLevel(int $employeeId, string $proficiencyLevel): int;
    public function getEmployeeTotalExperience(int $employeeId): int;
    public function getEmployeeAverageProficiency(int $employeeId): float;
    public function getEmployeePrimarySkills(int $employeeId): Collection;
    public function getEmployeePrimarySkillsDTO(int $employeeId): Collection;
    public function getEmployeeCertifiedSkills(int $employeeId): Collection;
    public function getEmployeeCertifiedSkillsDTO(int $employeeId): Collection;

    // Global statistics
    public function getTotalSkillCount(): int;
    public function getTotalSkillCountByCategory(string $skillCategory): int;
    public function getTotalSkillCountByLevel(string $proficiencyLevel): int;
    public function getTotalCertifiedSkillsCount(): int;
    public function getTotalVerifiedSkillsCount(): int;
    public function getExpiringCertificationsCount(int $days = 30): int;

    // Search functionality
    public function searchSkills(string $query): Collection;
    public function searchSkillsDTO(string $query): Collection;
    public function searchSkillsByEmployee(int $employeeId, string $query): Collection;
    public function searchSkillsByEmployeeDTO(int $employeeId, string $query): Collection;

    // Import/Export
    public function exportSkillsData(array $filters = []): string;
    public function importSkillsData(string $data): bool;

    // Analytics and reporting
    public function getSkillsStatistics(int $employeeId = null): array;
    public function getDepartmentSkillsStatistics(int $departmentId): array;
    public function getCompanySkillsStatistics(): array;
    public function getSkillsGapAnalysis(int $employeeId = null): array;
    public function getSkillsTrends(string $startDate = null, string $endDate = null): array;
}
