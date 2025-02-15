<?php

use App\Http\Controllers\V1\Bill\InvoicedOrderController;
use App\Http\Controllers\V1\History\ManufacturerOrderController;
use App\Http\Controllers\V1\Manage\OrderInfoController;
use App\Http\Controllers\V1\Order\DesingerGetController;
use App\Http\Controllers\V1\Shipping\GetShippingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\V1\Auth\AuthController;
use App\Http\Controllers\V1\Auth\PasswordController;
use App\Http\Controllers\V1\Auth\RoleController;
use App\Http\Controllers\V1\Product\ProductCategoryController;
use App\Http\Controllers\V1\Product\StockController;
use App\Http\Controllers\V1\Product\CategoryProductController;
use App\Http\Controllers\V1\Manufacturer\ManufacturerController;
use App\Http\Controllers\V1\Order\StoreController;
use App\Http\Controllers\V1\Order\GetOrderController;
use App\Http\Controllers\V1\Bill\BillController;
use App\Http\Controllers\V1\Bill\UninvoicedOrderController;
use App\Http\Controllers\V1\History\OrderController;
use App\Http\Controllers\V1\Image\RevertImageController;
use App\Http\Controllers\V1\Image\ImageController;
use App\Http\Controllers\V1\Manage\OrderManageController;
use App\Http\Controllers\V1\Order\CustomerGetController;
use App\Http\Controllers\V1\Order\DeliveryGetController;
use App\Http\Controllers\V1\Product\ColorController;
use App\Http\Controllers\V1\Shipping\StoreShippingController;
use App\Http\Controllers\V1\Product\ProductTypeController;
use App\Http\Controllers\V1\User\CustomerController;
use App\Http\Controllers\V1\Manage\OrderItemController;
use App\Http\Controllers\V1\Order\EditOrderController;
use App\Http\Controllers\V1\User\UserController;

/**
 * API Routes
 * @apiGroup Auth
 * @apiVersion 1.0.0
 * @apiName Auth
 * @apiDescription Auth API
 * @apiPermission None
 */
Route::prefix('v1/auth')->group(function () {

   Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
   Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
   Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum')->name('auth.logout');

   Route::middleware('auth:sanctum')->post('verify-token', [AuthController::class, 'verifyToken']);

   Route::prefix('password')->middleware('auth:sanctum')->group(function () {
       Route::post('/update', [PasswordController::class, 'update'])->name('password.update');
       Route::post('/reset', [PasswordController::class, 'reset'])->name('password.reset');
   });

   Route::prefix('roles')->middleware('auth:sanctum')->group(function () {
       Route::get('/', [RoleController::class, 'getRoles'])->name('roles.getRoles'); 
       Route::post('/update-user-roles', [RoleController::class, 'updateUserRoles'])->name('roles.updateUserRoles');
   });
});



Route::prefix('v1/users')->group(function () {
    Route::get('', [UserController::class, 'index']);
});

Route::prefix('v1/product')->group(function () {
    Route::apiResource('categories', ProductCategoryController::class);
    Route::apiResource('types', ProductTypeController::class);
    Route::apiResource('colors', ColorController::class);
});

Route::prefix('v1/product')->group(function () {
    Route::get('categories-all', [CategoryProductController::class, 'getAllCategories']);
    Route::get('categories-all/{id}/product-types', [CategoryProductController::class, 'getProductTypesByCategory']);
    Route::post('stocks-items', [CategoryProductController::class, 'getStockByProductTypeAndColor']);
});

Route::prefix('v1/product')->group(function () {
    Route::options('stocks', [StockController::class, 'index']);
    Route::post('stocks', [StockController::class, 'store']);
    Route::put('stocks/{id}', [StockController::class, 'update']);
    Route::post('stocks/{id}/decrement', [StockController::class, 'decrement']);
    Route::post('stocks/{id}/increment', [StockController::class, 'increment']);
    Route::get('stocks/zero', [StockController::class, 'zeroStock']);
    Route::get('stocks/critical', [StockController::class, 'lowStock']);
    Route::delete('stocks/{id}', [StockController::class, 'destroy']);
});

Route::apiResource('v1/manufacturers', ManufacturerController::class);

Route::prefix('v1/orders')->group(function () {
    Route::post('/store', [StoreController::class, 'store'])->middleware('auth:sanctum');
    Route::post('/validate-forms', [StoreController::class, 'validateForms']);
    Route::post('/validate-order-item', [StoreController::class, 'validateOrderItem']);
    Route::post('/validate-bulk-order-items', [StoreController::class, 'validateBulkOrderItems']);
    Route::post('/validate-invoice', [StoreController::class, 'validateInvoice']);
    Route::post('/validate-shipping-address', [StoreController::class, 'validateShippingAddress']);

    Route::controller(GetOrderController::class)->group(function () {
        Route::get('/get-orders', 'index');
        Route::get('/get-orders/{id}', 'show');
    });

});

Route::prefix('v1/designer-orders')->group(function () {
    Route::get('/', [DesingerGetController::class, 'index']); 
    Route::get('/{id}', [DesingerGetController::class, 'show']); 
});

Route::prefix('v1/customer-orders')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [CustomerGetController::class, 'index']);
    Route::get('/{id}', [CustomerGetController::class, 'show']);
});

Route::prefix('v1/orders/manage')->group(function () {
    Route::post('/approve', [OrderManageController::class, 'approveOrder']);
    Route::post('/prepare-for-shipping', [OrderManageController::class, 'prepareForShipping']);
    Route::post('/assign-manufacturer', [OrderManageController::class, 'assignManufacturer']);
    Route::post('/{orderId}/shipping', [OrderManageController::class, 'storeOrderShipping']);
    Route::post('/{orderId}/take-away', [OrderManageController::class, 'storeOfficeDelivery']);
    Route::post('/{orderId}/images', [OrderManageController::class, 'storeOrderImages']);

});

Route::middleware('auth:sanctum')->prefix('v1/bill')->group(function () {
    Route::get('/uninvoiced-orders', [UninvoicedOrderController::class, 'getUninvoicedOrders']);
    Route::get('/uninvoiced-orders/{id}', [UninvoicedOrderController::class, 'getSingleUninvoicedOrder']);
    Route::post('/store/{orderId}', [BillController::class, 'store']);
    Route::put('/update/{orderId}', [BillController::class, 'update']);
    Route::get('/invoiced-orders', [InvoicedOrderController::class, 'getInvoicedOrders']);
    Route::get('/invoiced-orders/{id}', [InvoicedOrderController::class, 'getSingleInvoicedOrder']);
    Route::put('/update-invoice-status/{id}', [BillController::class, 'updateInvoiceStatusToC']);

});

Route::prefix('v1/delivery')->group(function () {
    Route::get('/orders', [DeliveryGetController::class, 'index'])->name('delivery.orders');
    Route::get('/orders/{id}', [DeliveryGetController::class, 'show'])->name('delivery.order.show');
    Route::get('/take-away-orders', [DeliveryGetController::class, 'getTakeAwayOrders']);
    Route::get('/shipped-orders-by-shipping', [DeliveryGetController::class, 'getShippedOrdersByShippingType']);
});

Route::prefix('v1/shipping')->group(function () {
    Route::post('/orders/{orderId}/details-with-images', [StoreShippingController::class, 'storeOrderDetails']);
    Route::put('/orders/{orderId}/update-details', [StoreShippingController::class, 'updateOrderDetails']);
    Route::post('/orders/{orderId}/add-image', [StoreShippingController::class, 'addOrderImage']);
    Route::delete('/orders/images/{imageId}', [StoreShippingController::class, 'removeOrderImage']);
});

Route::prefix('v1/shipping')->middleware('auth:sanctum')->group(function () {
    Route::get('/orders/pending', [GetShippingController::class, 'getPendingShippingOrders'])->name('shipping.pending');
    Route::get('/orders/shipped', [GetShippingController::class, 'getShippedOrders'])->name('shipping.shipped');
});

Route::prefix('v1/images')->group(function () {
    
    Route::post('/upload-order-logo', [ImageController::class, 'uploadOrderLogo']);
    Route::post('/upload-payment-receipt', [ImageController::class, 'uploadPaymentReceipt']);
    Route::post('/upload-shipping-image', [ImageController::class, 'uploadShippingImage']);
    
    Route::post('/revert-order-logo', [RevertImageController::class, 'revertOrderLogo']);
    Route::post('/revert-payment-receipt', [RevertImageController::class, 'revertPaymentReceipt']);
    Route::post('/revert-shipping-image', [RevertImageController::class, 'revertShippingImage']);
});

Route::middleware('auth:sanctum')->prefix('v1/history')->group(function () {
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/customer/{customerId}', [OrderController::class, 'getOrdersByCustomerId']);
    Route::get('/orders/manufacturer/{manufacturerId}', [ManufacturerOrderController::class, 'getOrdersByManufacturerId']);
 
});

Route::get('/v1/customers', [CustomerController::class, 'index']);

Route::prefix('v1/manage/orders')->group(function () {
    Route::delete('/{orderId}', [OrderInfoController::class, 'destroy']);
    Route::put('/{orderId}/shipping-address', [OrderInfoController::class, 'updateShippingAddress']);
    Route::put('/{orderId}/invoice-info', [OrderInfoController::class, 'updateInvoiceInfo']);
    Route::put('/{orderId}/customer-info', [OrderInfoController::class, 'updateCustomerInfo']);
});

Route::prefix('v1/manage/order-items')->group(function () {
    Route::delete('/{orderItemId}', [OrderItemController::class, 'deleteOrderItem']);
    Route::post('/', [OrderItemController::class, 'addOrderItem']);
    Route::put('/{orderItemId}', [OrderItemController::class, 'updateOrderItem']); 
    Route::put('/logos/{orderLogoId}', [OrderItemController::class, 'updateLogo']);
    Route::post('/logos', [OrderItemController::class, 'addOrderLogo']);
    Route::delete('/logos/{orderLogoId}', [OrderItemController::class, 'deleteOrderLogo']);
});

Route::prefix('v1/orders')->group(function () {
    Route::get('/edit/{id}', [EditOrderController::class, 'show']);
});