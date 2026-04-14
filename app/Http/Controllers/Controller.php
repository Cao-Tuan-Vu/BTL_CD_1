<?php

namespace App\Http\Controllers;

// Trait hỗ trợ kiểm tra phân quyền (authorize)
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

// Trait hỗ trợ validate dữ liệu request
use Illuminate\Foundation\Validation\ValidatesRequests;

// Controller trừu tượng (base controller)
// Các controller khác sẽ kế thừa từ class này
abstract class Controller
{
    // Sử dụng trait để gọi các hàm authorize như: $this->authorize()
    use AuthorizesRequests;

    // Sử dụng trait để validate nhanh dữ liệu request
    use ValidatesRequests;
}