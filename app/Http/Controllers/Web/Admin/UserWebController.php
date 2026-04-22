<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserWebController extends Controller
{
    public function __construct(protected UserService $userService) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['q', 'role']);
        $perPage = (int) $request->integer('per_page', 10);
        $users = $this->userService->getPaginatedUsers($filters, $perPage);

        return view('admin.users.index', [
            'users' => $users,
            'filters' => $filters,
            'perPage' => $perPage,
        ]);
    }


    public function show(User $user): View
    {
        $user = $this->userService->loadUser($user);
        $recentOrders = $user->orders()
            ->select(['id', 'user_id', 'total_price', 'status', 'created_at'])
            ->latest('id')
            ->limit(5)
            ->get();
        $recentReviews = $user->reviews()
            ->select(['id', 'user_id', 'product_id', 'rating', 'comment', 'created_at'])
            ->with('product:id,name')
            ->latest('id')
            ->limit(5)
            ->get();

        return view('admin.users.show', [
            'user' => $user,
            'recentOrders' => $recentOrders,
            'recentReviews' => $recentReviews,
        ]);
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'user' => $user,
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $updatedUser = $this->userService->updateUser($user, $request->validated());

        return redirect()
            ->route('admin.users.show', $updatedUser)
            ->with('success', 'Cập nhật người dùng thành công.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ((int) (Auth::id() ?? 0) === (int) $user->id) {
            return redirect()
                ->back()
                ->withErrors(['user' => 'Không thể xóa tài khoản đang đăng nhập.']);
        }

        $this->userService->deleteUser($user);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Xóa người dùng thành công.');
    }
}
