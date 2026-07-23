<?php 

namespace App\Traits;

trait BlockedActions
{
    public const INSERT    = 'INSERT';
    public const UPDATE    = 'UPDATE';
    public const DELETE    = 'DELETE';
    public const TRUNCATE  = 'TRUNCATE';
    public const ALTER     = 'ALTER';
    public const DROP      = 'DROP';
    public const CREATE    = 'CREATE';
    public const GRANT     = 'GRANT';
    public const REVOKE    = 'REVOKE';
    public const VACUUM    = 'VACUUM';
    public const ANALYZE   = 'ANALYZE';
    public const REINDEX   = 'REINDEX';
    public const CLUSTER   = 'CLUSTER';
    public const COPY      = 'COPY';
    public const CALL      = 'CALL';
    public const EXECUTE   = 'EXECUTE';
    public const DEALLOCATE = 'DEALLOCATE';
    public const MERGE     = 'MERGE';

    /**
     * @return string[]
     */
    public static function blockedActions(): array
    {
        return [
            self::INSERT,
            self::UPDATE,
            self::DELETE,
            self::TRUNCATE,
            self::ALTER,
            self::DROP,
            self::CREATE,
            self::GRANT,
            self::REVOKE,
            self::VACUUM,
            self::ANALYZE,
            self::REINDEX,
            self::CLUSTER,
            self::COPY,
            self::CALL,
            self::EXECUTE,
            self::DEALLOCATE,
            self::MERGE,
        ];
    }
}