<?php

declare(strict_types=1);

namespace App\Enums;

enum PermissionEnum: string
{
    // Organization
    case VIEW_ORGANIZATION = 'view.organization';
    case UPDATE_ORGANIZATION = 'update.organization';
    case DELETE_ORGANIZATION = 'delete.organization';

    // Users
    case VIEW_USERS = 'view.users';
    case CREATE_USERS = 'create.users';
    case UPDATE_USERS = 'update.users';
    case DELETE_USERS = 'delete.users';

    // Roles
    case VIEW_ROLES = 'view.roles';
    case CREATE_ROLES = 'create.roles';
    case UPDATE_ROLES = 'update.roles';
    case DELETE_ROLES = 'delete.roles';

    // Elections
    case VIEW_ELECTIONS = 'view.elections';
    case CREATE_ELECTIONS = 'create.elections';
    case UPDATE_ELECTIONS = 'update.elections';
    case DELETE_ELECTIONS = 'delete.elections';

    // Positions
    case VIEW_POSITIONS = 'view.positions';
    case CREATE_POSITIONS = 'create.positions';
    case UPDATE_POSITIONS = 'update.positions';
    case DELETE_POSITIONS = 'delete.positions';

    // Candidates
    case VIEW_CANDIDATES = 'view.candidates';
    case CREATE_CANDIDATES = 'create.candidates';
    case UPDATE_CANDIDATES = 'update.candidates';
    case DELETE_CANDIDATES = 'delete.candidates';

    // Registration Form Fields
    case VIEW_REGISTRATION_FIELDS = 'view.registration_fields';
    case CREATE_REGISTRATION_FIELDS = 'create.registration_fields';
    case UPDATE_REGISTRATION_FIELDS = 'update.registration_fields';
    case DELETE_REGISTRATION_FIELDS = 'delete.registration_fields';

    // Voters
    case VIEW_VOTERS = 'view.voters';
    case IMPORT_VOTERS = 'import.voters';
    case UPDATE_VOTERS = 'update.voters';
    case DELETE_VOTERS = 'delete.voters';
    case EXPORT_VOTERS = 'export.voters';
    case VIEW_IMPORT_LOGS = 'view.import_logs';

    // Voter Validation
    case APPROVE_VOTERS = 'approve.voters';
    case REJECT_VOTERS = 'reject.voters';

    // Voting
    case VIEW_VOTES = 'view.votes';
    case RESET_VOTES = 'reset.votes';
    case EXPORT_RESULTS = 'export.results';

    // Election Settings
    case VIEW_ELECTION_SETTINGS = 'view.election_settings';
    case UPDATE_ELECTION_SETTINGS = 'update.election_settings';

    // Audit Logs
    case VIEW_AUDIT_LOGS = 'view.audit_logs';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function getPermissionFor(RoleEnum $role): array
    {
        return match ($role) {
            RoleEnum::System_Admin => self::cases(),
            RoleEnum::Admin => [
                self::VIEW_ORGANIZATION,
                self::UPDATE_ORGANIZATION,
                self::VIEW_USERS,
                self::CREATE_USERS,
                self::UPDATE_USERS,
                self::DELETE_USERS,
                self::VIEW_ROLES,
                self::CREATE_ROLES,
                self::UPDATE_ROLES,
                self::DELETE_ROLES,
                self::VIEW_ELECTIONS,
                self::CREATE_ELECTIONS,
                self::UPDATE_ELECTIONS,
                self::DELETE_ELECTIONS,
                self::VIEW_POSITIONS,
                self::CREATE_POSITIONS,
                self::UPDATE_POSITIONS,
                self::DELETE_POSITIONS,
                self::VIEW_CANDIDATES,
                self::CREATE_CANDIDATES,
                self::UPDATE_CANDIDATES,
                self::DELETE_CANDIDATES,
                self::VIEW_REGISTRATION_FIELDS,
                self::CREATE_REGISTRATION_FIELDS,
                self::UPDATE_REGISTRATION_FIELDS,
                self::DELETE_REGISTRATION_FIELDS,
                self::VIEW_VOTERS,
                self::IMPORT_VOTERS,
                self::UPDATE_VOTERS,
                self::DELETE_VOTERS,
                self::EXPORT_VOTERS,
                self::VIEW_IMPORT_LOGS,
                self::APPROVE_VOTERS,
                self::REJECT_VOTERS,
                self::VIEW_VOTES,
                self::RESET_VOTES,
                self::EXPORT_RESULTS,
                self::VIEW_ELECTION_SETTINGS,
                self::UPDATE_ELECTION_SETTINGS,
                self::VIEW_AUDIT_LOGS,
            ],
            default => [],
        };
    }


}