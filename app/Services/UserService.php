<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class UserService
{
    public function __construct(protected UserRepository $userRepository) {}

    public function getPaginatedUsers(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->userRepository->paginate($filters, $perPage);
    }

    public function getUserById(int $userId): User
    {
        return $this->userRepository->findByIdOrFail($userId);
    }

    public function loadUser(User $user): User
    {
        return $this->userRepository->loadRelations($user);
    }

    public function createUser(array $attributes): User
    {
        $user = $this->userRepository->create($attributes)->loadCount(['orders', 'reviews']);
        $this->flushUserCaches();

        return $user;
    }

    public function updateUser(User $user, array $attributes): User
    {
        $updatedUser = $this->userRepository->update($user, $attributes);
        $this->flushUserCaches();

        return $updatedUser;
    }

    public function deleteUser(User $user): void
    {
        $this->userRepository->delete($user);
        $this->flushUserCaches();
    }

    protected function flushUserCaches(): void
    {
        Cache::forget('admin.orders.customer-options');
    }
}
