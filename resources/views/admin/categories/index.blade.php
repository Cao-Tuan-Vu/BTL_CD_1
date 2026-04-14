@extends('admin.layouts.app')

@section('title', 'Danh Mục')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/pages/categories/index.css') }}">
@endpush


@section('content')
    <div class="toolbar">
        <div>
            <h1 class="title">Danh mục</h1>
            <p class="subtitle">Quản lý danh mục sản phẩm.</p>
        </div>
        <a class="btn primary" href="{{ route('admin.categories.create') }}">Tạo danh mục</a>
    </div>

    <section class="card">
        <table>
            <thead>
            <tr>
                <th>STT</th>
                <th>Tên</th>
                <th>Sản phẩm</th>
                <th class="text-right">Tác vụ</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($categories as $category)
                <tr>
                    <td data-label="STT">{{ $loop->iteration }}</td>
                    <td data-label="Tên">{{ $category->name }}</td>
                    <td data-label="Sản phẩm">{{ $category->products_count ?? 0 }}</td>
                    <td data-label="Tác vụ" class="text-right">
                        <div class="actions a-shared-actions-end">
                            <a class="btn muted" href="{{ route('admin.categories.show', $category) }}">Xem</a>
                            <a class="btn muted" href="{{ route('admin.categories.edit', $category) }}">Sửa</a>
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="post" onsubmit="return confirm('Xóa danh mục này?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn danger" type="submit">Xóa</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Không tìm thấy danh mục nào.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div class="a-shared-pagination-top">
            {{ $categories->withQueryString()->links('components.pagination') }}
        </div>
    </section>
@endsection


