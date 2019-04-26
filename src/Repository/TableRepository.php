<?php

namespace App\Repository;

/**
 * Repository.
 */
final class TableRepository implements RepositoryInterface
{
    /**
     * @var QueryFactory
     */
    private $queryFactory;

    /**
     * Constructor.
     *
     * @param QueryFactory $queryFactory The query factory
     */
    public function __construct(QueryFactory $queryFactory)
    {
        $this->queryFactory = $queryFactory;
    }

    /**
     * Fetch row by id.
     *
     * @param string $table The table name
     * @param int $id The primary key
     *
     * @return array Result set
     */
    public function fetchById(string $table, int $id): array
    {
        return $this->queryFactory->newSelect($table)
            ->select('*')
            ->where(['id' => $id])
            ->execute()
            ->fetch('assoc') ?: [];
    }

    /**
     * Fetch all rows.
     *
     * @param string $table Table name
     *
     * @return array Result set
     */
    public function fetchAll(string $table): array
    {
        return $this->queryFactory->newSelect($table)->select('*')->execute()->fetchAll('assoc') ?: [];
    }

    /**
     * Check if the given ID exists in the table.
     *
     * @param string $table The table name
     * @param int $id The primary key
     *
     * @return bool True If the id exists
     */
    public function existsById(string $table, int $id): bool
    {
        return $this->queryFactory->newSelect($table)
            ->select('id')
            ->andWhere(['id' => $id])
            ->execute()
            ->fetch('assoc') ? true : false;
    }
}
