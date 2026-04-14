<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserRepository
{
    public function paginate(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = User::query()->withCount(['orders', 'reviews']);

        if (! empty($filters['q'])) {
            $keyword = trim((string) $filters['q']);

            $query->where(function ($builder) use ($keyword): void {
                $builder
                    ->where('name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        if (! empty($filters['role'])) {
            $query->where('role', $filters['role']);
        }

        $perPage = (int) max(1, min(100, $perPage));

        return $query->latest('id')->paginate($perPage);
    }

    public function findByIdOrFail(int $id): User
    {
        return User::query()->withCount(['orders', 'reviews'])->findOrFail($id);
    }

    public function loadRelations(User $user): User
    {
        return $user->loadCount(['orders', 'reviews']);
    }

    public function create(array $attributes): User
    {
        return User::create($attributes);
    }

    public function update(User $user, array $attributes): User
    {
        if (array_key_exists('password', $attributes) && empty($attributes['password'])) {
            unset($attributes['password']);
        }

        $user->update($attributes);

        return $user->refresh()->loadCount(['orders', 'reviews']);
    }

    public function delete(User $user): void
    {
        $user->delete();
    }
}