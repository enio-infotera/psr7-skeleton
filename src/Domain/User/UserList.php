<?php

namespace App\Domain\User;

use App\Domain\Service\DomainServiceInterface;

/**
 * Service.
 */
final class UserList implements DomainServiceInterface
{
    /**
     * @var UserListRepository
     */
    private $repository;

    /**
     * Constructor.
     *
     * @param UserListRepository $repository The repository
     */
    public function __construct(UserListRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Find all users.
     *
     * @param array $params The parameters
     *
     * @return array the result
     */
    public function listAllUsers(array $params): array
    {
        return $this->repository->getTableData($params);
    }
}
