@extends('admin.layouts.app')

@section('title', 'Tổng Quan')
@section('meta_description', 'Trang tổng quan quản trị HomeSpace: thống kê nhanh sản phẩm, đơn hàng, doanh thu và đánh giá.')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/pages/home/index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/pages/home/inventory-warnings.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush


@section('content')
    <div class="toolbar">
        <div>
            <h1 class="title">Tổng quan</h1>
            <p class="subtitle">Thống kê nhanh sản phẩm, danh mục, đơn hàng và đánh giá.</p>
        </div>
    </div>

    {{-- Thông kê kinh doanh --}}
    <section class="grid stats a-shared-section-gap">
        <article class="card">
            <div class="subtitle">Tông doanh thu</div>
            <h2 class="title">{{ number_format($stats['revenue'], 0) }} VNÐ</h2>
            @if($comparisonStats['revenueGrowth'] != 0)
                <div class="trend {{ $comparisonStats['revenueGrowth'] > 0 ? 'up' : 'down' }}">
                    {{ $comparisonStats['revenueGrowth'] > 0 ? 'tăng' : 'giảm' }} {{ abs($comparisonStats['revenueGrowth']) }}%
                </div>
            @endif
        </article>
        <article class="card">
            <div class="subtitle">Đơn hàng</div>
            <h2 class="title">{{ number_format($stats['orders']) }}</h2>
            <div class="sub-info">
                {{ number_format($stats['pendingOrders']) }} chờ xác nhận · {{ number_format($stats['processingOrders']) }} đang xử lý
            </div>
        </article>
        <article class="card">
            <div class="subtitle">Giá trị trung bình/ĐH</div>
            <h2 class="title">{{ number_format($stats['avgOrderValue'], 0) }} VNÐ</h2>
        </article>
        <article class="card">
            <div class="subtitle">Sản phẩm</div>
            <h2 class="title">{{ number_format($stats['products']) }}</h2>
            <div class="sub-info">{{ number_format($stats['categories']) }} danh mục</div>
        </article>
    </section>

   
    <section class="card">
        <div class="toolbar">
            <div>
                <h2 class="title">Top 5 sản phẩm bán chạy</h2>
                <p class="subtitle">30 ngày gần đây</p>
            </div>
            <a class="btn muted" href="{{ route('admin.products.index') }}">Xem tất cả</a>
        </div>

        <div class="best-selling-grid">
            @forelse ($bestSellingProducts as $product)
                <div class="best-selling-item">
                    <div class="product-info">
                        <h4>{{ $product->name }}</h4>
                        <div class="stats">
                            <span class="sold">{{ number_format($product->total_sold) }} bán</span>
                            <span class="revenue">{{ number_format($product->total_revenue, 0) }} VNÐ</span>
                        </div>
                    </div>
                </div>
            @empty
                <p>Chưa có dữ liệu bán hàng.</p>
            @endforelse
        </div>
    </section>

    {{-- Cảnh báo tồn kho --}}
    @if($lowStockProducts->count() > 0)
        <section class="card warning">
            <div class="toolbar">
                <h2 class="title">Cảnh báo tồn kho</h2>
                <a class="btn muted" href="{{ route('admin.products.index') }}">Quản lý sản phẩm</a>
            </div>

            <div class="warning-message">
                <strong>Cảnh báo:</strong> Có {{ $lowStockProducts->count() }} sản phẩm sắp hết tồn kho!
            </div>

            <table>
                <thead>
                <tr>
                    <th>Tên sản phẩm</th>
                    <th>Danh mục</th>
                    <th>Số lượng tồn</th>
                    <th class="text-right">Tác vụ</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($lowStockProducts as $product)
                    <tr class="warning-row">
                        <td data-label="Tên sản phẩm">{{ $product->name }}</td>
                        <td data-label="Danh mục">{{ $product->category?->name ?? 'Khong co' }}</td>
                        <td data-label="Số lượng tồn">
                            <span class="stock-badge {{ $product->stock <= 5 ? 'critical' : 'low' }}">
                                {{ $product->stock }}
                            </span>
                        </td>
                        <td data-label="Tác vụ" class="text-right">
                            <a class="btn muted" href="{{ route('admin.products.edit', $product) }}">Cap nhat</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </section>
    @endif

    {{-- Biểu doanh thu và phân tích --}}
    <section class="card">
        <div class="toolbar">
            <div>
                <h2 class="title">Phân tích doanh thu</h2>
                <p class="subtitle">Thống kê doanh thu và số lượng bán theo thời gian</p>
            </div>
            <div class="chart-controls">
                <select id="periodSelector" class="form-field">
                    <option value="today" {{ $selectedPeriod === 'today' ? 'selected' : '' }}>Hôm nay</option>
                    <option value="7days" {{ $selectedPeriod === '7days' ? 'selected' : '' }}>7 ngày qua</option>
                    <option value="30days" {{ $selectedPeriod === '30days' ? 'selected' : '' }}>30 ngày qua</option>
                    <option value="3months" {{ $selectedPeriod === '3months' ? 'selected' : '' }}>3 tháng qua</option>
                    <option value="6months" {{ $selectedPeriod === '6months' ? 'selected' : '' }}>6 tháng qua</option>
                    <option value="1year" {{ $selectedPeriod === '1year' ? 'selected' : '' }}>1 năm qua</option>
                    <option value="custom" {{ $selectedPeriod === 'custom' ? 'selected' : '' }}>Tùy chọn</option>
                </select>
            </div>
        </div>
        
        <div id="customDateRange" class="custom-date-range" style="{{ $selectedPeriod !== 'custom' ? 'display: none;' : '' }}">
            <div class="date-inputs">
                <div class="form-field">
                    <label for="startDate">Từ ngày</label>
                    <input type="date" id="startDate" value="{{ $startDate->format('Y-m-d') }}" class="form-field">
                </div>
                <div class="form-field">
                    <label for="endDate">Đến ngày</label>
                    <input type="date" id="endDate" value="{{ $endDate->format('Y-m-d') }}" class="form-field">
                </div>
                <button type="button" id="applyCustomRange" class="btn primary">Áp dụng</button>
            </div>
        </div>
        
        <div class="chart-tabs">
            <button class="tab-btn active" data-chart="revenue">Doanh thu</button>
            <button class="tab-btn" data-chart="quantity">Số lượng bán</button>
            <button class="tab-btn" data-chart="combined">Tổng hợp</button>
        </div>
        
        <div class="chart-container">
            <canvas id="revenueChart"></canvas>
        </div>
    </section>

    {{-- Biểu đồ trạng thái đơn hàng --}}
    <section class="card">
        <div class="toolbar">
            <h2 class="title">Biểu đồ trạng thái đơn hàng</h2>
        </div>
        <div class="chart-container">
            <canvas id="orderStatusChart"></canvas>
        </div>
    </section>

    {{-- Bảng don hàng mới nhất --}}
    <section class="card">
        <div class="toolbar">
            <h2 class="title">Đơn hàng mới nhất</h2>
            <a class="btn muted" href="{{ route('admin.orders.index') }}">Xem tất cả đơn hàng</a>
        </div>

        <table>
            <thead>
            <tr>
                <th>STT</th>
                <th>Người đặt</th>
                <th>Trạng thái</th>
                <th>Tổng tiền</th>
                <th>Ngày tạo</th>
                <th class="text-right">Tác vụ</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($latestOrders as $order)
                <tr>
                    <td data-label="STT">{{ $loop->iteration }}</td>
                    <td data-label="Người đặt">{{ $order->user?->name ?? 'Không có' }}</td>
                    {{-- Gọi method static để lấy tên trạng thái thay vì chỉ lưu giá trị --}}
                    <td data-label="Trạng thái">{{ \App\Models\Order::labelForStatus($order->status) }}</td>
                    {{-- Format giá tiền: chuyển string thành float, rồi format với 2 chữ số thập phân --}}
                    <td data-label="Tổng tiền">{{ number_format((float) $order->total_price, 2) }}</td>
                    {{-- Format ngày giờ hoặc hiển thị rỗng nếu null --}}
                    <td data-label="Ngày tạo">{{ $order->created_at?->format('Y-m-d H:i') }}</td>
                    <td data-label="Tác vụ" class="text-right">
                        <a class="btn muted" href="{{ route('admin.orders.show', $order) }}">Chi tiết</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Chưa có đơn hàng nào.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </section>
@endsection

@push('scripts')
<script>
    // Biêu doanh thu nâng cao
    let revenueChart;
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    let currentChartType = 'revenue';
    
    // Du lieu ban dau
    const revenueData = @json($revenueData);
    const productSalesData = @json($productSalesData);
    
    function formatCurrency(value) {
        return new Intl.NumberFormat('vi-VN', { 
            style: 'currency', 
            currency: 'VND',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(value);
    }
    
    function formatNumber(value) {
        return new Intl.NumberFormat('vi-VN').format(value);
    }
    
    function initChart(chartType = 'revenue') {
        if (revenueChart) {
            revenueChart.destroy();
        }
        
        currentChartType = chartType;
        let datasets = [];
        let yTitle = '';
        
        switch(chartType) {
            case 'revenue':
                yTitle = 'Doanh thu (VNÐ)';
                datasets = [{
                    label: 'Doanh thu',
                    data: revenueData.map(item => item.revenue),
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1,
                    fill: true
                }];
                break;
                
            case 'quantity':
                yTitle = 'Số lượng bán';
                datasets = [{
                    label: 'Số lượng bán',
                    data: productSalesData.map(item => item.quantity || 0),
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    tension: 0.1,
                    fill: true
                }];
                break;
                
            case 'combined':
                yTitle = 'Doanh thu (VNÐ) / Sô luong';
                datasets = [
                    {
                        label: 'Doanh thu',
                        data: revenueData.map(item => item.revenue),
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.1,
                        fill: false,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Sô luong bán',
                        data: productSalesData.map(item => item.quantity || 0),
                        borderColor: 'rgb(255, 99, 132)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        tension: 0.1,
                        fill: false,
                        yAxisID: 'y1'
                    }
                ];
                break;
        }
        
        const config = {
            type: 'line',
            data: {
                labels: revenueData.map(item => item.period),
                datasets: datasets
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: chartType === 'combined' ? {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Doanh thu (VNÐ)'
                        },
                        ticks: {
                            callback: function(value) {
                                return formatCurrency(value);
                            }
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Số lượng bán'
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
                    },
                } : {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: yTitle
                        },
                        ticks: {
                            callback: function(value) {
                                return chartType === 'revenue' ? formatCurrency(value) : formatNumber(value);
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.dataset.label.includes('Doanh thu')) {
                                    label += formatCurrency(context.parsed.y);
                                } else {
                                    label += formatNumber(context.parsed.y) + ' sản phẩm';
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        };
        
        revenueChart = new Chart(revenueCtx, config);
    }
    
    // Khoi tao biêu
    initChart('revenue');
    
    // Biểu trang thái don hàng
    const orderStatusCtx = document.getElementById('orderStatusChart').getContext('2d');
    const orderStatusData = @json($orderStatusStats);
    const statusLabels = {
        'pending': 'Chờ xác nhận',
        'confirmed': 'Đã xác nhận',
        'shipping': 'Đang giao hàng',
        'delivered': 'Đã giao hàng',
        'cancelled': 'Đã hủy'
    };

    const orderStatusChart = new Chart(orderStatusCtx, {
        type: 'doughnut',
        data: {
            labels: orderStatusData.map(item => statusLabels[item.status] || item.status),
            datasets: [{
                data: orderStatusData.map(item => item.count),
                backgroundColor: [
                    '#FF6384',
                    '#36A2EB', 
                    '#FFCE56',
                    '#4BC0C0',
                    '#9966FF'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return label + ': ' + formatNumber(value) + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });

    // Tab switching
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            initChart(this.dataset.chart);
        });
    });

    // Date range selection functionality
    const periodSelector = document.getElementById('periodSelector');
    const customDateRange = document.getElementById('customDateRange');
    const applyCustomRange = document.getElementById('applyCustomRange');
    
    periodSelector.addEventListener('change', function() {
        const selectedPeriod = this.value;
        
        if (selectedPeriod === 'custom') {
            customDateRange.style.display = 'block';
        } else {
            customDateRange.style.display = 'none';
            updateChart(selectedPeriod);
        }
    });
    
    applyCustomRange.addEventListener('click', function() {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        
        if (startDate && endDate) {
            if (new Date(startDate) > new Date(endDate)) {
                alert('Ngày bắt đầu phải nhỏ hơn ngày kết thúc!');
                return;
            }
            updateChart('custom', startDate, endDate);
        } else {
            alert('Vui lòng chọn ngày bắt đầu và ngày kết thúc!');
        }
    });
    
    function updateChart(period, startDate = null, endDate = null) {
        const url = new URL(window.location);
        url.searchParams.set('period', period);
        
        if (period === 'custom' && startDate && endDate) {
            url.searchParams.set('start_date', startDate);
            url.searchParams.set('end_date', endDate);
        }
        
        // Show loading state
        const chartContainer = document.querySelector('.chart-container');
        chartContainer.style.opacity = '0.5';
        
        fetch(url.toString())
            .then(response => response.text())
            .then(html => {
                // Create a temporary element to parse the response
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = html;
                
                // Extract new chart data from the response
                const scriptTag = tempDiv.querySelector('script[id="revenueData"]');
                if (scriptTag) {
                    const newRevenueData = JSON.parse(scriptTag.textContent);
                    // Update global data and reinitialize chart
                    window.revenueData = newRevenueData;
                    initChart(currentChartType);
                }
                
                // Update URL without page reload
                window.history.pushState({}, '', url.toString());
                
                // Restore opacity
                chartContainer.style.opacity = '1';
            })
            .catch(error => {
                console.error('Lỗi khi cập nhật biểu đồ:', error);
                chartContainer.style.opacity = '1';
                alert('Không thể cập nhật dữ liệu. Vui lòng thử lại!');
            });
    }
    
    // Add hidden data for JavaScript access
    const revenueDataElement = document.createElement('script');
    revenueDataElement.id = 'revenueData';
    revenueDataElement.textContent = JSON.stringify(revenueData);
    revenueDataElement.style.display = 'none';
    document.head.appendChild(revenueDataElement);
    
    // Make data globally accessible
    window.revenueData = revenueData;
    window.productSalesData = productSalesData;
</script>
@endpush


