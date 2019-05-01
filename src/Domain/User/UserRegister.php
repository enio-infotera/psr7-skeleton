<?php

namespace App\Domain\User;

use App\Service\ServiceInterface;

/**
 * Service.
 */
final class UserRegister implements ServiceInterface
{
    /** @var UserRegisterRepository */
    private $repository;

    /**
     * Constructor.
     *
     * @param UserRegisterRepository $repository The repository
     */
    public function __construct(UserRegisterRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Create a new user.
     *
     * @param array $data user data
     *
     * @return int the new user ID
     */
    public function registerUser(array $data): int
    {
        $userData = [
            'username' => $data['username'],
            'email' => $data['email'],
        ];

        return $this->repository->insertUser($userData);
    }
}
