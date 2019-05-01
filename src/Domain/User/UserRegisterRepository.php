<?php

namespace App\Domain\User;

use App\Repository\QueryFactory;
use App\Repository\RepositoryInterface;

/**
 * Repository.
 */
final class UserRegisterRepository implements RepositoryInterface
{
    /** @var QueryFactory */
    private $queryFactory;

    /**
     * Constructor.
     *
     * @param QueryFactory $queryFactory the query factory
     */
    public function __construct(QueryFactory $queryFactory)
    {
        $this->queryFactory = $queryFactory;
    }

    /**
     * Insert new user.
     *
     * @param array $data The user
     *
     * @return int The new ID
     */
    public function insertUser(array $data): int
    {
        return (int)$this->queryFactory->newInsert('users', $data)->execute()->lastInsertId();
    }
}
