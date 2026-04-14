<?php

namespace App\Http\Controllers\Web\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\CustomerCheckoutRequest;
use App\Http\Requests\StoreCustomerReviewRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Services\CategoryService;
use App\Services\CustomerCartService;
use App\Services\OrderService;
use App\Services\PaymentMethodService;
use App\Services\ProductService;
use App\Services\ReviewService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CustomerWebController extends Controller
{
    public function __construct(
        protected CategoryService $categoryService,
        protected ProductService $productService,
        protected OrderService $orderService,
        protected CustomerCartService $cartService,
        protected ReviewService $reviewService,
        protected PaymentMethodService $paymentMethodService,
    ) {}

    public function index(): View
    {
        $featuredProducts = $this->productService->getCachedFeaturedProducts(8);
        $categories = $this->categoryService->getCachedCategoryOptions();
        $otherProducts = $this->productService->getHomeProductsExcluding(
            $featuredProducts->pluck('id')->all(),
            8,
        );

        return view('customer.home', [
            'featuredProducts' => $featuredProducts,
            'otherProducts' => $otherProducts,
            'categories' => $categories,
        ]);
    }

    public function about(): View
    {
        return view('customer.about');
    }

    public function contact(): View
    {
        return view('customer.contact');
    }

    public function products(Request $request): View
    {
        $filters = $request->only(['q', 'category_id', 'min_price', 'max_price']);
        $filters['status'] = 'active';

        $perPage = (int) $request->integer('per_page', 12);
        $products = $this->productService->getPaginatedProducts($filters, $perPage);
        $categories = $this->categoryService->getCachedCategoryOptions();

        return view('customer.products.index', [
            'products' => $products,
            'categories' => $categories,
            'filters' => $filters,
            'perPage' => $perPage,
        ]);
    }

    public function showProduct(Product $product): View
    {
        $product = $this->productService->loadProductWithReviews($product);

        return view('customer.products.show', [
            'product' => $product,
        ]);
    }

    public function storeReview(StoreCustomerReviewRequest $request, Product $product): RedirectResponse
    {
        $this->authorize('create', Review::class);

        $customerId = (int) request()->user()->id;

        $alreadyReviewed = Review::query()
            ->where('product_id', $product->id)
            ->where('user_id', $customerId)
            ->exists();

        if ($alreadyReviewed) {
            return redirect()
                ->route('customer.products.show', $product)
                ->withErrors(['review' => 'Bạn đã đánh giá sản phẩm này rồi.']);
        }

        $this->reviewService->createReview([
            'product_id' => $product->id,
            'user_id' => $customerId,
            'rating' => (int) $request->validated()['rating'],
            'comment' => $request->validated()['comment'] ?? null,
        ]);

        return redirect()
            ->route('customer.products.show', $product)
            ->with('success', 'Gửi đánh giá thành công.');
    }

    public function cart(): View
    {
        return view('customer.cart', [
            'items' => $this->cartService->items(),
            'totalAmount' => $this->cartService->totalAmount(),
        ]);
    }

    public function addToCart(AddToCartRequest $request, Product $product): RedirectResponse
    {
        $this->cartService->addItem($product, (int) $request->validated()['quantity']);

        return redirect()
            ->back()
            ->with('success', 'Đã thêm sản phẩm vào giỏ hàng.');
    }

    public function updateCartItem(UpdateCartItemRequest $request, Product $product): RedirectResponse
    {
        $this->cartService->updateItem($product, (int) $request->validated()['quantity']);

        return redirect()
            ->route('customer.cart.show')
            ->with('success', 'Đã cập nhật số lượng trong giỏ hàng.');
    }

    public function removeCartItem(Product $product): RedirectResponse
    {
        $this->cartService->removeItem($product->id);

        return redirect()
            ->route('customer.cart.show')
            ->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
    }

    public function checkout(): View|RedirectResponse
    {
        if ($this->cartService->itemCount() === 0) {
            return redirect()
                ->route('customer.cart.show')
                ->withErrors(['cart' => 'Giỏ hàng đang trống. Vui lòng thêm sản phẩm trước khi thanh toán.']);
        }

        $totalAmount = $this->cartService->totalAmount();
        $paymentMethods = $this->paymentMethodService->getCheckoutMethods($totalAmount);
        $selectedPaymentMethod = (string) old('payment_method', Order::PAYMENT_METHOD_CASH);

        if (! in_array($selectedPaymentMethod, Order::paymentMethods(), true)) {
            $selectedPaymentMethod = Order::PAYMENT_METHOD_CASH;
        }

        return view('customer.checkout', [
            'items' => $this->cartService->items(),
            'totalAmount' => $totalAmount,
            'customer' => request()->user(),
            'paymentMethods' => $paymentMethods,
            'selectedPaymentMethod' => $selectedPaymentMethod,
        ]);
    }

    public function placeOrder(CustomerCheckoutRequest $request): RedirectResponse
    {
        if ($this->cartService->itemCount() === 0) {
            return redirect()
                ->route('customer.cart.show')
                ->withErrors(['cart' => 'Giỏ hàng đang trống. Vui lòng thêm sản phẩm trước khi thanh toán.']);
        }

        $validated = $request->validated();

        try {
            $order = $this->orderService->createOrder(
                (int) request()->user()->id,
                $this->cartService->toOrderItems(),
                $validated['shipping_address'],
                $validated['shipping_phone'],
                $validated['payment_method'],
            );
        } catch (ValidationException $exception) {
            return redirect()
                ->back()
                ->withErrors($exception->errors())
                ->withInput();
        }

        $this->cartService->clear();

        return redirect()
            ->route('customer.orders.show', $order)
            ->with('success', 'Đặt hàng thành công.');
    }

    public function orders(Request $request): View
    {
        $statuses = Order::statuses();
        $selectedStatus = (string) $request->query('status', '');
        $perPage = (int) $request->integer('per_page', 10);

        $filters = [
            'user_id' => (int) request()->user()->id,
        ];

        if (in_array($selectedStatus, $statuses, true)) {
            $filters['status'] = $selectedStatus;
        }

        $orders = $this->orderService->getPaginatedOrders($filters, $perPage);

        return view('customer.orders.index', [
            'orders' => $orders,
            'statuses' => $statuses,
            'selectedStatus' => $selectedStatus,
        ]);
    }

    public function showOrder(Order $order): View
    {
        if ((int) $order->user_id !== (int) request()->user()->id) {
            abort(403, 'Bạn không thể xem đơn hàng này.');
        }

        $order = $this->orderService->loadOrder($order);

        return view('customer.orders.show', [
            'order' => $order,
        ]);
    }
}
