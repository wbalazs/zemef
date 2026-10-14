<?php

use App\Http\Controllers\MasterController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\CustomerTypeController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerMarkingController;
use App\Http\Controllers\BusinessMarkingController;
use App\Http\Controllers\BusinessAliasController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ToolController;
use App\Http\Controllers\PostingController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseTicketController;
use App\Http\Controllers\PhotographyController;
use App\Http\Controllers\DeliveryNoteController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\StockEntryController;
use App\Http\Controllers\ConsignmentReceiptController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\WarehouseTransferController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\InventoryControlController;
use App\Http\Controllers\WebshopController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ItemListController;
use App\Http\Controllers\CustomerCardController;
use App\Http\Controllers\MobileCustomerController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\BillingoBillController;

use App\Http\Controllers\PackageSizeController;
use App\Http\Controllers\ShippingServiceController;
use App\Http\Controllers\DiscountPriceController;
use App\Http\Controllers\CashOnDeliveryController;
use App\Http\Controllers\Sam4sController;
use App\Http\Controllers\VatReportController;
use App\Http\Controllers\ItemListItemController;
use App\Http\Controllers\CashRegisterController;
use App\Http\Controllers\DenominationController;
use App\Http\Controllers\CashTransactionController;
use App\Http\Controllers\CommissionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();
Auth::routes(['verify' => true]);
Route::get('/', function () {
    return response()->redirectTo('/login');
});

Route::prefix('mobil/customer')->name('mobile.customer')->group(function () {
    Route::get('/', [MobileCustomerController::class, 'show']);
    Route::get('/login', [MobileCustomerController::class, 'login'])->name('.login');
    Route::post('/login', [MobileCustomerController::class, 'authenticate'])->middleware('throttle:20,1')->name('.authenticate');
    Route::post('/logout', [MobileCustomerController::class, 'logout'])->name('.logout');
});

// Supports both /pdf/purchase_tickets/file.pdf?timestamp=... and malformed /file.pdf&timestamp=...
Route::get('/pdf/purchase_tickets/{file}', function ($file) {
    $normalizedFile = preg_replace('/&timestamp=.*$/', '', $file);
    $safeFile = basename($normalizedFile);
    $path = public_path('pdf/purchase_tickets/' . $safeFile);

    abort_unless(is_file($path), 404);

    return response()->file($path);
})->where('file', '.*');


Route::get('printLabel', 'ProductController@printLabel')->name('printLabel');

Route::post('api/set_order', 'ApiController@storeOrder');
Route::get('api/test', 'ApiController@test');

//get-datatable-state
Route::get('api/get-datatable-state', 'EmployeeController@getDatatableState');
Route::post('api/save-datatable-state', 'EmployeeController@saveDatatableState');

Route::get('kenyszer_teszt', 'UserController@kenyszer_teszt')->name('kenyszer_teszt');

Route::get('dashboard', 'HomeController@index')->name('home');
Route::get('lock', 'PageController@lock')->name('page.lock')->middleware('guest');

Route::group(['middleware' => 'auth'], function () {

Route::prefix('commissions')->name('commissions.')->group(function () {
    Route::get('/', [CommissionController::class, 'index'])->name('index');
    Route::get('/create', [CommissionController::class, 'create'])->name('create');
    Route::post('/', [CommissionController::class, 'store'])->name('store');
    Route::get('/{commission}', [CommissionController::class, 'show'])->name('show');
    Route::post('/calculate', [CommissionController::class, 'calculate'])->name('calculate');
    Route::get('/performance', [CommissionController::class, 'performance'])->name('performance');
    Route::get('/payouts', [CommissionController::class, 'payouts'])->name('payouts');
    Route::post('/pay', [CommissionController::class, 'pay'])->name('pay');
    Route::delete('/{commission}', [CommissionController::class, 'destroy'])->name('destroy');
    Route::get('/report', [CommissionController::class, 'report'])->name('report');
});
Route::post('/employee/update-business', [EmployeeController::class, 'updateBusinessId'])->name('employee.update.business');
Route::post('/employee/update-company', [EmployeeController::class, 'updateCompanyId'])->name('employee.update.company');
// Or combined route:
Route::post('/employee/update-active-ids', [EmployeeController::class, 'updateActiveIds'])->name('employee.update.active');
// Pénztárak
Route::resource('cash-registers', CashRegisterController::class);

// Címletek
Route::resource('denominations', DenominationController::class);
Route::get('denominations-by-currency', [CashTransactionController::class, 'getDenominations'])
    ->name('denominations.by-currency');

// Pénztár tranzakciók
Route::resource('cash-transactions', CashTransactionController::class)
    ->except(['create', 'edit', 'update', 'destroy']);
Route::get('/cash-transactions/{cashTransaction}/print', [CashTransactionController::class, 'printVoucher'])
    ->name('cash-transactions.print');   
Route::get('/cash-registers/{cashRegister}/print-voucher', [CashRegisterController::class, 'printVoucher'])
    ->name('cash-registers.print-voucher')
    ->where('type', 'income|expense|both|period');
    
    Route::get('profile', 'ProfileController@edit')->name('profile.edit');
    Route::patch('profile/update', 'ProfileController@update')->name('profile.update');
    Route::patch('profile/password', 'ProfileController@password')->name('profile.password');
    Route::patch('profile/sign_code', 'ProfileController@sign_code')->name('profile.sign_code');
    
    Route::resource('user', 'UserController', ['except' => ['show']]);
    Route::resource('employee', 'EmployeeController', ['except' => ['show']]);

    Route::get('/product/printLabel', [ProductController::class, 'printLabel'])->name('product.printLabel');
    Route::get('showProduct/{id}', [ProductController::class, 'showProduct'])->name('product.showProduct');
    Route::get('product/getall', [ProductController::class, 'getAllProductList'])->name('product.getall');

    Route::resource('product', 'ProductController', ['except' => ['show']]);
    Route::resource('product_set', 'ProductSetController', ['except' => ['show']]);
    Route::resource('customer', 'CustomerController', ['except' => ['show']]);
    Route::resource('tool', 'ToolController', ['except' => ['show']]);
    Route::resource('business', 'BusinessController', ['except' => ['show']]);
    Route::resource('vehicle', 'VehicleController', ['except' => ['show']]);
    Route::resource('property', 'PropertyController', ['except' => ['show']]);
    Route::resource('company', 'CompanyController', ['except' => ['show']]);
    Route::resource('category', 'CategoryController', ['except' => ['show']]);
    Route::resource('customer_type', 'CustomerTypeController', ['except' => ['show']]);
    Route::resource('tag', 'TagController', ['except' => ['show']]);
    Route::resource('item', 'ItemController', ['except' => ['show']]);
    Route::resource('posting', 'PostingController', ['except' => ['show']]);
    Route::resource('role', 'RoleController', ['except' => ['show']]);
    Route::post('/role/{role}/permission/{permission}/toggle', [RoleController::class, 'togglePermission'])->name('role.permission.toggle');
    Route::resource('warehouse', 'WarehouseController', ['except' => ['show']]);
    Route::resource('purchase_ticket', 'PurchaseTicketController', ['except' => ['show']]);
    Route::resource('stock_entry', 'StockEntryController', ['except' => ['show']]);
    Route::post('/ajax/save_inventory_item_inline', [StockEntryController::class, 'saveInventoryItemInline'])->name('save.inventory.item.inline');
    Route::resource('consignment_receipt', 'ConsignmentReceiptController', ['except' => ['show']]);
    Route::post('/load_inventory_gallery', [StockEntryController::class, 'loadInventoryGallery']);
    Route::post('/upload_inventory_image', [StockEntryController::class, 'uploadInventoryImage'])->name('upload.inventory.image');
    Route::resource('inventory_control', 'InventoryControlController', ['except' => ['show']]);
    Route::get('/inventory_controls/print/{inventory_control_id}', [InventoryControlController::class, 'printInventoryControl'])->name('inventory_control.print');
    Route::get('/inventory_controls/discrepancies/{inventory_control_id}', [InventoryControlController::class, 'showInventoryDiscrepancies'])->name('inventory_control.discrepancies');
    Route::get('/inventory_controls/report/{inventory_control_id}', [InventoryControlController::class, 'printInventoryReport'])->name('inventory_control.report');

    //business calendar
    Route::post('/business/{business}/calendar/update', [App\Http\Controllers\BusinessController::class, 'updateCalendar'])->name('business.calendar.update');

    //Sale panel
    Route::resource('sale_panel', 'SaleController', ['except' => ['show']]);

    //WEBSHOP ROUTES
    Route::resource('webshop', 'WebshopController', ['except' => ['show']]);
    
    Route::resource('delivery_note', 'DeliveryNoteController', ['except' => ['show']]);
    Route::resource('actual_stock', 'StockController', ['except' => ['show']]);
    Route::get('/actual_stock/{actual_stock}', [StockController::class, 'show'])->name('actual_stock.show');
    //Fotózás
    Route::resource('photography', 'PhotographyController', ['except' => ['show']]);

    //Felhasználói naptár
    Route::get('userCalendar', [HolidayController::class, 'userCalendar'])->name('holiday.userCalendar');
    Route::get('getCalendarEvents', [HolidayController::class, 'getCalendarEvents']);
    Route::post('saveCalendarEvent', [HolidayController::class, 'saveCalendarEvent']);
    Route::post('updateCalendarEventFull/{id}', [HolidayController::class, 'updateCalendarEventFull']);
    Route::post('updateCalendarEvent/{id}', [HolidayController::class, 'updateCalendarEvent']);
    Route::post('deleteCalendarEvent/{id}', [HolidayController::class, 'deleteCalendarEvent']);

    //Szabadságok
    Route::get('holiday/wait_approve', [HolidayController::class, 'wait_approve'])->name('holiday.wait_approve');
    Route::resource('holiday', 'HolidayController', ['except' => ['show']]);


    //Bizonylatok
    Route::get('receipt/all_purchase_ticket', [ReceiptController::class, 'all_purchase_ticket'])->name('receipt.all_purchase_ticket');
    Route::get('receipt/all_delivery_note', [ReceiptController::class, 'all_delivery_note'])->name('receipt.all_delivery_note');
    Route::get('receipt/all_warehouse_transfer', [ReceiptController::class, 'all_warehouse_transfer'])->name('receipt.all_warehouse_transfer');
    Route::get('receipt/all_stock_entry', [ReceiptController::class, 'all_stock_entry'])->name('receipt.all_stock_entry');
    Route::get('receipt/all_consignment_receipt', [ReceiptController::class, 'all_consignment_receipt'])->name('receipt.all_consignment_receipt');
    Route::get('receipt/all_booking', [ReceiptController::class, 'all_booking'])->name('receipt.all_booking');


    Route::get('/actual_stock', [StockController::class, 'actual_stock'])->name('actual_stock');
    Route::get('/producthistory/{id}', [StockController::class, 'producthistory'])->name('producthistory');
    /*Route::get('/actual_stock_search', [StockController::class, 'actual_stock_search'])->name('actual_stock_search');*/
    
    Route::get('/customer_card_reg', [CustomerController::class, 'customerCardReg']);
    Route::get('/repair_list', [PurchaseTicketController::class, 'displayRepairList']);
    Route::get('/waited_list', [PurchaseTicketController::class, 'displayWaitedList']);
    Route::get('/extension_list', [PurchaseTicketController::class, 'displayExtensionList'])->name('purchase_tickets.extension_list');
    Route::get('/extension_list/{purchase_ticket}/edit', [PurchaseTicketController::class, 'editExtension'])->name('purchase_tickets.extension_edit');
    Route::patch('/extension_list/{purchase_ticket}', [PurchaseTicketController::class, 'updateExtension'])->name('purchase_tickets.extension_update');
    Route::get('/wait_to_accept_list', [PurchaseTicketController::class, 'displayWaitToAcceptList']);
    Route::get('/own_list', [PurchaseTicketController::class, 'displayOwnList']);
    Route::get('/edit_pti', [PurchaseTicketController::class, 'editPTI']);

    // ==================== PACKAGE ROUTES ====================
    Route::get('package/index', [PackageController::class, 'index'])->name('package.index');
    Route::get('package/create', [PackageController::class, 'create'])->name('package.create');
    Route::post('/package', [PackageController::class, 'store'])->name('package.store');
    Route::get('package/edit/{package}', [PackageController::class, 'edit'])->name('package.edit');
    Route::patch('/packageupdate/{package}', [PackageController::class, 'update'])->name('package.update');
    
    Route::delete('/package/{package}', [PackageController::class, 'destroy'])->name('package.destroy');

    Route::patch('package/{package}/mark-as-shipped', [PackageController::class, 'markAsShipped'])->name('package.mark-as-shipped');
    Route::patch('package/{package}/mark-as-delivered', [PackageController::class, 'markAsDelivered'])->name('package.mark-as-delivered');
    Route::get('package/order-items/{order}', [PackageController::class, 'getOrderItems'])->name('package.order-items'); 

    // ==================== PACKAGE SIZE ROUTES ====================
    Route::get('package-size/index', [PackageSizeController::class, 'index'])->name('package-size.index');
    Route::post('package-size', [PackageSizeController::class, 'store'])->name('package-size.store');
    Route::get('package-size/create', [PackageSizeController::class, 'create'])->name('package-size.create');
    Route::get('package-size/{package_size}', [PackageSizeController::class, 'show'])->name('package-size.show');
    Route::put('package-size/{package_size}', [PackageSizeController::class, 'update'])->name('package-size.update');
    Route::patch('package-size/{package_size}', [PackageSizeController::class, 'update']);
    Route::delete('package-size/{package_size}', [PackageSizeController::class, 'destroy'])->name('package-size.destroy');
    Route::get('package-size/{package_size}/edit', [PackageSizeController::class, 'edit'])->name('package-size.edit');
    Route::patch('package-size/{packageSize}/toggle-status', [PackageSizeController::class, 'toggleStatus'])->name('package-size.toggle-status');

        // ==================== SHIPPING SERVICE ROUTES ====================
        Route::get('shipping-service/index', [ShippingServiceController::class, 'index'])->name('shipping-service.index');
        Route::get('shipping-service/create', [ShippingServiceController::class, 'create'])->name('shipping-service.create');
        Route::post('shipping-service', [ShippingServiceController::class, 'store'])->name('shipping-service.store');
        Route::get('shipping-service/{shippingService}/edit', [ShippingServiceController::class, 'edit'])->name('shipping-service.edit');
        Route::patch('shipping-service/{shippingService}', [ShippingServiceController::class, 'update'])->name('shipping-service.update');
        Route::delete('shipping-service/{shippingService}', [ShippingServiceController::class, 'destroy'])->name('shipping-service.destroy');
        Route::patch('shipping-service/{shippingService}/toggle-status', [ShippingServiceController::class, 'toggleStatus'])->name('shipping-service.toggle-status');

    
      // ==================== DISCOUNT PRICE ROUTES ====================
    Route::get('discount-price/index', [DiscountPriceController::class, 'index'])->name('discount-price.index');
    Route::get('discount-price/create', [DiscountPriceController::class, 'create'])->name('discount-price.create');
    Route::post('discount-price', [DiscountPriceController::class, 'store'])->name('discount-price.store');
    Route::get('discount-price/{discount_price}', [DiscountPriceController::class, 'show'])->name('discount-price.show');
    Route::get('discount-price/{discount_price}/edit', [DiscountPriceController::class, 'edit'])->name('discount-price.edit');
    Route::patch('discount-price/{discount_price}', [DiscountPriceController::class, 'update'])->name('discount-price.update');
    Route::delete('discount-price/{discount_price}', [DiscountPriceController::class, 'destroy'])->name('discount-price.destroy');
    Route::patch('discount-price/{discountPrice}/toggle-status', [DiscountPriceController::class, 'toggleStatus'])->name('discount-price.toggle-status');
    Route::patch('discount-price/{discountPrice}/reset-usage', [DiscountPriceController::class, 'resetUsage'])->name('discount-price.reset-usage');
    Route::post('discount-price/{discountPrice}/duplicate', [DiscountPriceController::class, 'duplicate'])->name('discount-price.duplicate');
        
      // ==================== CASH ON DELIVERY ROUTES ====================
    Route::get('cash-on-delivery/index', [CashOnDeliveryController::class, 'index'])->name('cash-on-delivery.index');
    Route::get('cash-on-delivery/create', [CashOnDeliveryController::class, 'create'])->name('cash-on-delivery.create');
    Route::post('cash-on-delivery', [CashOnDeliveryController::class, 'store'])->name('cash-on-delivery.store');
    Route::get('cash-on-delivery/{cash_on_delivery}', [CashOnDeliveryController::class, 'show'])->name('cash-on-delivery.show');
    Route::get('cash-on-delivery/{cash_on_delivery}/edit', [CashOnDeliveryController::class, 'edit'])->name('cash-on-delivery.edit');
    Route::patch('cash-on-delivery/{cash_on_delivery}', [CashOnDeliveryController::class, 'update'])->name('cash-on-delivery.update');
    Route::delete('cash-on-delivery/{cash_on_delivery}', [CashOnDeliveryController::class, 'destroy'])->name('cash-on-delivery.destroy');
    Route::patch('cash-on-delivery/{cashOnDelivery}/mark-collected', [CashOnDeliveryController::class, 'markAsCollected'])->name('cash-on-delivery.mark-collected');
    Route::patch('cash-on-delivery/{cashOnDelivery}/mark-transferred', [CashOnDeliveryController::class, 'markAsTransferred'])->name('cash-on-delivery.mark-transferred');
    Route::get('cash-on-delivery/pending', [CashOnDeliveryController::class, 'pending'])->name('cash-on-delivery.pending');
    Route::get('cash-on-delivery/collected', [CashOnDeliveryController::class, 'collected'])->name('cash-on-delivery.collected');
    Route::get('cash-on-delivery/transferred', [CashOnDeliveryController::class, 'transferred'])->name('cash-on-delivery.transferred');
    Route::get('cash-on-delivery/statistics', [CashOnDeliveryController::class, 'statistics'])->name('cash-on-delivery.statistics');

    Route::prefix('billingo-bills')->name('billingo-bills.')->group(function () {
        Route::get('/search-customers', [BillingoBillController::class, 'searchCustomers'])->name('search-customers');
        Route::get('/index', [BillingoBillController::class, 'index'])->name('index');
        Route::get('/create', [BillingoBillController::class, 'create'])->name('create');
        Route::get('/{billingoBill}', [BillingoBillController::class, 'show'])->name('show');
        Route::post('/', [BillingoBillController::class, 'store'])->name('store');
        Route::get('/{billingoBill}', [BillingoBillController::class, 'show'])->name('show');
        Route::get('/{billingoBill}/download-pdf', [BillingoBillController::class, 'downloadPdf'])->name('download-pdf');
        Route::get('/{billingoBill}/sync', [BillingoBillController::class, 'sync'])->name('sync');
        Route::post('/{billingoBill}/sync', [BillingoBillController::class, 'sync'])->name('sync');
        Route::post('/{billingoBill}/update-payment-status', [BillingoBillController::class, 'updatePaymentStatus'])->name('update-payment-status');
        Route::post('/{billingoBill}/quick-action', [BillingoBillController::class, 'quickAction'])->name('quick-action');
    });

    Route::prefix('vat-reports')->name('vat-reports.')->group(function () {
        Route::get('/index', [VatReportController::class, 'index'])->name('index');
        Route::get('/detailed', [VatReportController::class, 'detailed'])->name('detailed');
        Route::get('/payment', [VatReportController::class, 'vatPayment'])->name('payment');
        Route::get('/export', [VatReportController::class, 'export'])->name('export');
    });
    
});

//Törzsadatok
Route::middleware('auth')->prefix('master')->group(function () {
    Route::get('/currencies', [MasterController::class, 'displayCurrencies']);
    Route::get('/currency', [MasterController::class, 'displayCurrency']);
    Route::post('/save_currency', [MasterController::class, 'saveCurrency']);
    Route::delete('/delete_currency', [MasterController::class, 'deleteCurrency']);
    Route::get('/delete_currency', [MasterController::class, 'deleteCurrency']);

    Route::get('/doc_types', [MasterController::class, 'displayDoc_types']);
    Route::get('/doc_type', [MasterController::class, 'displayDoc_type']);
    Route::post('/save_doc_type', [MasterController::class, 'saveDoc_type']);
    Route::delete('/delete_doc_type', [MasterController::class, 'deleteDoc_type']);
    Route::get('/delete_doc_type', [MasterController::class, 'deleteDoc_type']);

    Route::get('/unit_price_types', [MasterController::class, 'displayUnitPriceTypes']);
    Route::get('/unit_price_type', [MasterController::class, 'displayUnitPriceType']);
    Route::post('/save_unit_price_type', [MasterController::class, 'saveUnitPriceType']);
    Route::delete('/delete_unit_price_type', [MasterController::class, 'deleteUnitPriceType']);
    Route::get('/delete_unit_price_type', [MasterController::class, 'deleteUnitPriceType']);

    Route::get('/vat_types', [MasterController::class, 'displayVatTypes']);
    Route::get('/vat_type', [MasterController::class, 'displayVatType']);
    Route::post('/save_vat_type', [MasterController::class, 'saveVatType']);
    Route::delete('/delete_vat_type', [MasterController::class, 'deleteVatType']);
    Route::get('/delete_vat_type', [MasterController::class, 'deleteVatType']);

    Route::get('/banks', [MasterController::class, 'displayBanks']);
    Route::get('/bank', [MasterController::class, 'displayBank']);
    Route::post('/save_bank', [MasterController::class, 'saveBank']);
    Route::delete('/delete_bank', [MasterController::class, 'deleteBank']);
    Route::get('/delete_bank', [MasterController::class, 'deleteBank']);
    Route::get('/karats', [MasterController::class, 'displayKarats']);
    Route::get('/karat', [MasterController::class, 'displayKarat']);
    Route::post('/save_karat', [MasterController::class, 'saveKarat']);
    Route::delete('/delete_karat', [MasterController::class, 'deleteKarat']);
    Route::get('/delete_karat', [MasterController::class, 'deleteKarat']);
    Route::get('/category2s', [MasterController::class, 'displayCategory2s']);
    Route::get('/category2', [MasterController::class, 'displayCategory2']);
    Route::post('/save_category2', [MasterController::class, 'saveCategory2']);
    Route::delete('/delete_category2', [MasterController::class, 'deleteCategory2']);
    Route::get('/delete_category2', [MasterController::class, 'deleteCategory2']);
    Route::get('/bank_accounts', [MasterController::class, 'displayBankAccounts']);
    Route::get('/bank_account', [MasterController::class, 'displayBankAccount']);
    Route::post('/save_bank_account', [MasterController::class, 'saveBankAccount']);
    Route::delete('/delete_bank_account', [MasterController::class, 'deleteBankAccount']);
    Route::get('/delete_bank_account', [MasterController::class, 'deleteBankAccount']);
    Route::get('/tool_types', [MasterController::class, 'displayToolTypes']);
    Route::get('/tool_type', [MasterController::class, 'displayToolType']);
    Route::post('/save_tool_type', [MasterController::class, 'saveToolType']);
    Route::delete('/delete_tool_type', [MasterController::class, 'deleteToolType']);
    Route::get('/delete_tool_type', [MasterController::class, 'deleteToolType']);
    Route::get('/payments', [MasterController::class, 'displayPayments']);
    Route::get('/payment', [MasterController::class, 'displayPayment']);
    Route::post('/save_payment', [MasterController::class, 'savePayment']);
    Route::delete('/delete_payment', [MasterController::class, 'deletePayment']);
    Route::get('/delete_payment', [MasterController::class, 'deletePayment']);
    Route::get('/hallmarks', [MasterController::class, 'displayHallmarks']);
    Route::get('/hallmark', [MasterController::class, 'displayHallmark']);
    Route::post('/save_hallmark', [MasterController::class, 'saveHallmark']);
    Route::delete('/delete_hallmark', [MasterController::class, 'deleteHallmark']);
    Route::get('/delete_hallmark', [MasterController::class, 'deleteHallmark']);
    Route::get('/attributes', [MasterController::class, 'displayAttributes']);
    Route::get('/attribute', [MasterController::class, 'displayAttribute']);
    Route::post('/save_attribute', [MasterController::class, 'saveAttribute']);
    Route::delete('/delete_attribute', [MasterController::class, 'deleteAttribute']);
    Route::get('/delete_attribute', [MasterController::class, 'deleteAttribute']);
    Route::get('/product_statuses', [MasterController::class, 'displayProductStatuses']);
    Route::get('/product_status', [MasterController::class, 'displayProductStatus']);
    Route::post('/save_product_status', [MasterController::class, 'saveProductStatus']);
    Route::delete('/delete_product_status', [MasterController::class, 'deleteProductStatus']);
    Route::get('/delete_product_status', [MasterController::class, 'deleteProductStatus']);
    Route::get('/product_types', [MasterController::class, 'displayProductTypes']);
    Route::get('/product_type', [MasterController::class, 'displayProductType']);
    Route::post('/save_product_type', [MasterController::class, 'saveProductType']);
    Route::delete('/delete_product_type', [MasterController::class, 'deleteProductType']);
    Route::get('/delete_product_type', [MasterController::class, 'deleteProductType']);
    Route::get('/product_signs', [MasterController::class, 'displayProductSigns']);
    Route::get('/product_sign', [MasterController::class, 'displayProductSign']);
    Route::post('/save_product_sign', [MasterController::class, 'saveProductSign']);
    Route::delete('/delete_product_sign', [MasterController::class, 'deleteProductSign']);
    Route::get('/delete_product_sign', [MasterController::class, 'deleteProductSign']);
    Route::get('/product_colors', [MasterController::class, 'displayProductColors']);
    Route::get('/product_color', [MasterController::class, 'displayProductColor']);
    Route::post('/save_product_color', [MasterController::class, 'saveProductColor']);
    Route::delete('/delete_product_color', [MasterController::class, 'deleteProductColor']);
    Route::get('/delete_product_color', [MasterController::class, 'deleteProductColor']);
    Route::get('/activities', [MasterController::class, 'displayActivities']);
    Route::get('/activity', [MasterController::class, 'displayActivity']);
    Route::post('/save_activity', [MasterController::class, 'saveActivity']);
    Route::delete('/delete_activity', [MasterController::class, 'deleteActivity']);
    Route::get('/delete_activity', [MasterController::class, 'deleteActivity']);
    Route::get('/bookings', [MasterController::class, 'displayBookings']);
    Route::get('/booking', [MasterController::class, 'displayBooking']);
    Route::post('/save_booking', [MasterController::class, 'saveBooking']);
    Route::get('/nav_standard', [MasterController::class, 'displayNAVStandard']);
    Route::post('/save_nav_standard', [MasterController::class, 'saveNAVStandard']);
    Route::get('/nav_fuels', [MasterController::class, 'displayNAVFuels']);
    Route::get('/nav_fuel', [MasterController::class, 'displayNAVFuel']);
    Route::post('/save_nav_fuel', [MasterController::class, 'saveNAVFuel']);
    Route::delete('/delete_nav_fuel', [MasterController::class, 'deleteNAVFuel']);
    Route::get('/delete_nav_fuel', [MasterController::class, 'deleteNAVFuel']);

    //Települések
    Route::get('/settlements', [MasterController::class, 'displaySettlements'])->name('master.cities.index');
    Route::get('/settlement/create', [MasterController::class, 'createSettlement'])->name('master.cities.create');
    Route::get('/settlement/{id}/edit', [MasterController::class, 'editSettlement'])->name('master.cities.edit');
    Route::post('/settlement/store', [MasterController::class, 'storeSettlement'])->name('master.cities.store');
    Route::delete('/settlement/{id}', [MasterController::class, 'deleteSettlement'])->name('master.cities.destroy');


    //Naptár
    Route::get('/calendar', [CalendarController::class, 'index']);
    //Route::get('/fill-calendar-data', [CalendarController::class, 'fillCalendarData']);
    
});
    Route::post('/upload_inventory_image', [StockController::class, 'uploadInventoryImage']);
    Route::post('/load_inventory_gallery', [StockController::class, 'loadInventoryGallery']);
    Route::post('/delete_inventory_image', [StockController::class, 'deleteInventoryImage']);

//Ajax
Route::middleware('auth')->prefix('ajax')->group(function () {
    Route::post('/upload_employee_doc', [EmployeeController::class, 'uploadEmployeeDoc']);
    Route::post('/save_employee_doc_data', [EmployeeController::class, 'saveEmployeeDocData']);
    Route::post('/load_employee_docs', [EmployeeController::class, 'loadEmployeeDoc']);

    Route::post('/upload_company_doc', [CompanyController::class, 'uploadCompanyDoc']);
    Route::post('/save_company_doc_data', [CompanyController::class, 'saveCompanyDocData']);
    Route::post('/load_company_docs', [CompanyController::class, 'loadCompanyDoc']);
    //Cégekhez tartozó hitelek lekérdezése
    Route::post('/getCompanyLoansProgress', [CompanyController::class, 'getCompanyLoansProgress']);

    Route::post('/upload_tool_doc', [ToolController::class, 'uploadToolDoc']);
    Route::post('/save_tool_doc_data', [ToolController::class, 'saveToolDocData']);
    Route::post('/load_tool_docs', [ToolController::class, 'loadToolDoc']);

    Route::post('/upload_vehicle_doc', [VehicleController::class, 'uploadVehicleDoc']);
    Route::post('/save_vehicle_doc_data', [VehicleController::class, 'saveVehicleDocData']);
    Route::post('/load_vehicle_docs', [VehicleController::class, 'loadVehicleDoc']);

    Route::post('/save_vehicle_condition', [VehicleController::class, 'saveCondition']);
    Route::post('/load_vehicle_conditions', [VehicleController::class, 'loadConditions']);
    Route::post('/delete_condition', [VehicleController::class, 'deleteCondition']);

    Route::post('/save_address', [CustomerController::class, 'saveAddress']);
    Route::post('/load_addresses', [CustomerController::class, 'loadAddresses']);
    Route::post('/delete_address', [CustomerController::class, 'deleteAddress']);

    Route::post('/save_company_address', [CompanyController::class, 'saveAddress']);
    Route::post('/load_company_addresses', [CompanyController::class, 'loadAddresses']);
    Route::post('/delete_company_address', [CompanyController::class, 'deleteAddress']);

    Route::post('/save_contact', [CustomerController::class, 'saveContact']);
    Route::post('/load_contacts', [CustomerController::class, 'loadContacts']);
    Route::post('/delete_contact', [CustomerController::class, 'deleteContact']);

    Route::post('/save_customer_marking', [CustomerMarkingController::class, 'save']);
    Route::post('/load_customer_markings', [CustomerMarkingController::class, 'load']);
    Route::post('/delete_customer_marking', [CustomerMarkingController::class, 'delete']);
    Route::post('/save_business_marking', [BusinessMarkingController::class, 'save']);
    Route::post('/load_business_markings', [BusinessMarkingController::class, 'load']);
    Route::post('/delete_business_marking', [BusinessMarkingController::class, 'delete']);
    Route::post('/save_business_alias', [BusinessAliasController::class, 'save']);
    Route::post('/load_business_aliases', [BusinessAliasController::class, 'load']);
    Route::post('/delete_business_alias', [BusinessAliasController::class, 'delete']);

    Route::post('/save_property_contact', [PropertyController::class, 'saveContact']);
    Route::post('/load_property_contacts', [PropertyController::class, 'loadContacts']);
    Route::post('/delete_property_contact', [PropertyController::class, 'deleteContact']);

    Route::post('/save_company_contact', [CompanyController::class, 'saveContact']);
    Route::post('/load_company_contacts', [CompanyController::class, 'loadContacts']);
    Route::post('/delete_company_contact', [CompanyController::class, 'deleteContact']);

    Route::post('/save_education', [EmployeeController::class, 'saveEducation']);
    Route::post('/load_educations', [EmployeeController::class, 'loadEducations']);
    Route::post('/delete_education', [EmployeeController::class, 'deleteEducation']);

    Route::post('/save_loan', [CompanyController::class, 'saveLoan']);
    Route::post('/load_loans', [CompanyController::class, 'loadLoans']);
    Route::post('/delete_loan', [CompanyController::class, 'deleteLoan']);

    Route::post('/save_holiday', [EmployeeController::class, 'saveHoliday']);

    Route::post('/auto_save_vehicle_images', [VehicleController::class, 'autoSaveVehicleImage']);
    Route::post('/upload_vehicle_image', [VehicleController::class, 'uploadVehicleImage']);
    Route::post('/load_vehicle_gallery', [VehicleController::class, 'loadVehicleGallery']);
    Route::post('/delete_vehicle_image', [VehicleController::class, 'deleteImg']);


    Route::post('/auto_save_product_images', [ProductController::class, 'autoSaveProductImage']);
    Route::post('/upload_product_image', [ProductController::class, 'uploadProductImage']);
    Route::post('/load_product_gallery', [ProductController::class, 'loadProductGallery']);
    Route::post('/load_product_gallery_new', [ProductController::class, 'loadProductGalleryNew']);
    Route::post('/delete_product_image', [ProductController::class, 'deleteImg']);
    Route::post('/set_primary_product_image', [ProductController::class, 'setPrimaryImage']);

    Route::post('/upload_inventory_image', [StockController::class, 'uploadInventoryImage']);
    Route::post('/load_inventory_gallery', [StockController::class, 'loadInventoryGallery']);
    Route::post('/delete_inventory_image', [StockController::class, 'deleteInventoryImage']);

    Route::post('/upload_property_doc', [PropertyController::class, 'uploadPropertyDoc']);
    Route::post('/save_property_doc_data', [PropertyController::class, 'savePropertyDocData']);
    Route::post('/load_property_docs', [PropertyController::class, 'loadPropertyDoc']);

    Route::post('/auto_save_property_images', [PropertyController::class, 'autoSavePropertyImage']);
    Route::post('/upload_property_image', [PropertyController::class, 'uploadPropertyImage']);
    Route::post('/load_property_gallery', [PropertyController::class, 'loadPropertyGallery']);
    Route::post('/delete_property_image', [PropertyController::class, 'deleteImg']);
    Route::post('/delete_doc', [VehicleController::class, 'deleteDoc']);
    
    Route::post('/save_company_employee', [CompanyController::class, 'saveCompanyEmployee']);

    //NYOMTATÁSI RÉSZEK
    Route::post('/pdf/posting', [PostingController::class, 'printPosting']);
    Route::post('/pdf/purchase_ticket/{timestamp?}', [PurchaseTicketController::class, 'printPurchaseTicket']);
    Route::post('/pdf/delivery_note', [DeliveryNoteController::class, 'printDeliveryNote']);
    Route::post('/pdf/stock_entry', [StockEntryController::class, 'printStockEntry']);
    Route::post('/pdf/consignment_receipt', [ConsignmentReceiptController::class, 'printConsignmentReceipt']);
    //EGYÉB AJAX
    Route::post('/get_customer', [CustomerController::class, 'getCustomerData']);
    Route::post('/get_business', [BusinessController::class, 'getBusinessData']);
    Route::post('/save_purchase_ticket', [PurchaseTicketController::class, 'savePurchaseTicket']);
    Route::post('/save_extension_item_inline', [PurchaseTicketController::class, 'saveExtensionItemInline']);
    Route::post('/update_waited_ticket_storage', [PurchaseTicketController::class, 'updateWaitedTicketStorage']);
    Route::post('/save_purchase_ticket_item', [PurchaseTicketController::class, 'savePurchaseTicketItem']);
    Route::post('/auto_save_pti_images', [PurchaseTicketController::class, 'autoSavePTIImage']);
    Route::post('/upload_pti_image', [PurchaseTicketController::class, 'uploadPTIImage']);
    Route::post('/saveAndUploadPTIImage', [PurchaseTicketController::class, 'saveAndUploadPTIImage']);
    Route::post('/load_pti_gallery', [PurchaseTicketController::class, 'loadPTIGallery']);
    Route::post('/delete_pti_image', [PurchaseTicketController::class, 'deleteImg']);
    Route::post('/copy_purchase_ticket', [PurchaseTicketController::class, 'copyPurchaseTicket']);
    Route::post('/cancel_purchase_ticket', [PurchaseTicketController::class, 'cancelPurchaseTicket']);
    Route::post('/save_investigation', [PurchaseTicketController::class, 'saveInvestigation']);
    Route::post('/save_investigation_more', [PurchaseTicketController::class, 'saveInvestigationMore']);
    Route::post('/save_pti_product', [PurchaseTicketController::class, 'savePTIProduct']);
    Route::post('/receive_item', [PurchaseTicketController::class, 'receiveItem']);
    Route::post('/back_item', [PurchaseTicketController::class, 'backItem']);
    Route::post('/accept_item', [PurchaseTicketController::class, 'acceptItem']);
    Route::post('/save_pti_comment', [PurchaseTicketController::class, 'save_pti_comment']);
    Route::post('/save_pti_repair_price', [PurchaseTicketController::class, 'save_pti_repair_price']);
    Route::post('/get_storages_by_business_id_employee', [PurchaseTicketController::class, 'get_storages_by_business_id_employee']);
    Route::post('/get_storages_by_business_id_employee_inventory_control', [InventoryControlController::class, 'get_storages_by_business_id_employee_inventory_control']);
    Route::post('/get_storages_by_business_id_employee_consignment_receipt', [ConsignmentReceiptController::class, 'get_storages_by_business_id_employee']);
    Route::post('/check_login', [PurchaseTicketController::class, 'check_login']);
    Route::post('/transfer_items', [DeliveryNoteController::class, 'transfer_items']);
    Route::post('/accept_items', [DeliveryNoteController::class, 'accept_items']);
    //Ötvös RKOZ készítése visszaadáshoz
    Route::post('/return_item', [WarehouseTransferController::class, 'return_item']);
    Route::post('/get_warehouses_transfer_from_exclude_this', [StockController::class, 'get_warehouses_transfer_from_exclude_this']);
    Route::post('/get_items_by_warehouse_id', [StockController::class, 'get_items_by_warehouse_id']);
    Route::post('/warehouse_transfer', [WarehouseTransferController::class, 'warehouse_transfer']);
    Route::get('/wait_to_accept_list_data', [PurchaseTicketController::class, 'getWaitToAcceptListData'])->name('purchase_tickets.wait_to_accept_list_data');
    Route::get('/wait_to_accept_item_details', [PurchaseTicketController::class, 'getWaitToAcceptItemDetails'])->name('purchase_tickets.wait_to_accept_item_details');


    //Ügyfél AJAX
    Route::post('/customers/modal-store', [CustomerController::class, 'storeFromModal'])->name('customers.modal.store');

    //Sale AJAX
    //get-sale-summary
    Route::post('/get_sale_summary', [SaleController::class, 'getSaleSummary']);
    Route::post('/sale/customer-cards', [SaleController::class, 'getCustomerCards']);
    Route::post('/sale/validate-coupon', [SaleController::class, 'validateCoupon']);

    //Ez ideiglenes 
    Route::post('/sales/print-receipt', [SaleController::class, 'printReceipt'])->name('sales.print.receipt');
    Route::get('/sales/print-invoice', [SaleController::class, 'printInvoice'])->name('sales.print.invoice');
    Route::post('/sales/print-invoice', [SaleController::class, 'printInvoice'])->name('sales.print.invoice');
    Route::post('/sales/print-deposit', [SaleController::class, 'printDeposit'])->name('sales.print.deposit');
    Route::post('/create_sale', [SaleController::class, 'createTempSale']);


    //eszköz felülvizsgálat
    //delete
    Route::post('/delete_tool_investigation', [ToolController::class, 'deleteInvestigation']);
    //save_tool_investigation
    Route::post('/save_tool_investigation', [ToolController::class, 'saveInvestigation']);
    //load_tool_investigations
    Route::post('/load_tool_investigations', [ToolController::class, 'loadToolInvestigations']);

    //Árazás
    Route::post('/group_pricing', [StockController::class, 'group_pricing']);

    //Stock prod_sell_out
    Route::post('/prod_sell_out', [StockController::class, 'prod_sell_out']);

    //Ékszer vásárlás AJAX
    Route::post('/save_stock_entry', [StockEntryController::class, 'saveStockEntry']);
    Route::post('/save_stock_entry_item', [StockEntryController::class, 'saveStockEntryItem']);
    Route::post('/copy_stock_entry_item', [StockEntryController::class, 'copyStockEntryItem']);
    Route::post('/delete_stock_entry_item', [StockEntryController::class, 'deleteStockEntryItem']);
    Route::get('/stock_entry/{stock_entry}/settings', [StockEntryController::class, 'settings'])->name('stock_entry.settings');
    Route::post('/stock_entry/inventory/{inventory}/settings', [StockEntryController::class, 'saveInventorySettings'])->name('stock_entry.inventory.settings.save');
    Route::post('/auto_save_sei_images', [StockEntryController::class, 'autoSaveSEIImage']);
    Route::post('/upload_sei_image', [StockEntryController::class, 'uploadSEIImage']);
    Route::post('/saveAndUploadSEIImage', [StockEntryController::class, 'saveAndUploadSEIImage']);
    Route::post('/load_sei_gallery', [StockEntryController::class, 'loadSEIGallery']);
    Route::post('/delete_sei_image', [StockEntryController::class, 'deleteImg']);
    Route::post('/copy_stock_entry', [StockEntryController::class, 'copyStockEntry']);
    Route::post('/cancel_stock_entry', [StockEntryController::class, 'cancelStockEntry']);
    Route::post('/save_sei_product', [StockEntryController::class, 'saveSEIProduct']);
    Route::post('/update_fulfillment_date', [StockEntryController::class, 'update_fulfillment_date']);


    //Bizományos átvétel AJAX
    Route::post('/save_consignment_receipt', [ConsignmentReceiptController::class, 'saveConsignmentReceipt']);
    Route::post('/save_consignment_receipt_item', [ConsignmentReceiptController::class, 'saveConsignmentReceiptItem']);
    Route::post('/auto_save_cri_images', [ConsignmentReceiptController::class, 'autoSaveCRIImage']);
    Route::post('/upload_cri_image', [ConsignmentReceiptController::class, 'uploadCRIImage']);
    Route::post('/saveAndUploadCRIImage', [ConsignmentReceiptController::class, 'saveAndUploadCRIImage']);
    Route::post('/load_cri_gallery', [ConsignmentReceiptController::class, 'loadCRIGallery']);
    Route::post('/delete_cri_image', [ConsignmentReceiptController::class, 'deleteImg']);
    Route::post('/copy_consignment_receipt', [ConsignmentReceiptController::class, 'copyConsignmentReceipt']);
    Route::post('/cancel_consignment_receipt', [ConsignmentReceiptController::class, 'cancelConsignmentReceipt']);
    Route::post('/delete_consignment_receipt_item', [ConsignmentReceiptController::class, 'deleteConsignmentReceiptItem']);
    Route::get('/pdf/consignment_receipt/{consignment_receipt}/storno', [ConsignmentReceiptController::class, 'printStorno'])->name('consignment_receipt.storno.print');
    Route::post('/save_cri_product', [ConsignmentReceiptController::class, 'saveCRIProduct']);
    Route::post('/update_expire_date_cri', [ConsignmentReceiptController::class, 'update_expire_date']);

    //InventoryControl AJAX
    Route::post('/update_inventory_control', [InventoryControlController::class, 'update_inventory_control']);

    //Termékek ajax
    Route::post('/product_colors_ajax', [ProductController::class, 'product_colors_ajax']);
    Route::post('/product_statuses_ajax', [ProductController::class, 'product_statuses_ajax']);
    Route::post('/product_signs_ajax', [ProductController::class, 'product_signs_ajax']);
    Route::get('/get_all_product', [ProductController::class, 'get_all_product']);
    Route::post('/get_one_product_by_id', [ProductController::class, 'get_one_product_by_id']);

    //Employee ajax
    Route::post('/employee_business_change', [EmployeeController::class, 'employee_business_change']);

    //Deviza árfolyamok
    Route::post('/get_currency_by_id', [MasterController::class, 'get_currency_by_id']);

    //calendar_update
    Route::post('/calendar_update', [CalendarController::class, 'calendar_update']);
    //Szabadság AJAX hívások
    Route::post('/approve_holiday', [HolidayController::class, 'approve_holiday']);
    Route::post('/decline_holiday', [HolidayController::class, 'decline_holiday']);

    //Bizonylatok nyomtatása
    Route::post('/print_delivery_note', [ReceiptController::class, 'printDeliveryNote']);
    Route::post('/print_purchase_ticket', [ReceiptController::class, 'printPurchaseTicket']);
    Route::post('/print_warehouse_transfer', [ReceiptController::class, 'printWarehouseTransfer']);
    Route::post('/print_stock_entry', [ReceiptController::class, 'printStockEntry']);
    Route::post('/print_consignment_receipt', [ReceiptController::class, 'printConsignmentReceipt']);
    Route::post('/print_booking', [ReceiptController::class, 'printBooking']);

    //Bizonylat képek
    Route::post('/get_delivery_note_images', [DeliveryNoteController::class, 'get_delivery_note_images']);
    Route::post('/get_warehouse_transfer_images', [WarehouseTransferController::class, 'get_warehouse_transfer_images']);
    Route::post('/get_stock_entry_images', [StockEntryController::class, 'get_stock_entry_images']);

    //Bizonylat fájlok
    Route::post('/get_delivery_note_files', [DeliveryNoteController::class, 'get_delivery_note_files']);
    Route::post('/get_warehouse_transfer_files', [WarehouseTransferController::class, 'get_warehouse_transfer_files']);

    //képek törlése
    Route::post('/delete_image', [DeliveryNoteController::class, 'delete_image']);

    //Fájlok törlése
    Route::post('/delete_file', [DeliveryNoteController::class, 'delete_file']);

    //Címelemek AJAX
    Route::get('/get-cities/{zip}', [CustomerController::class,'getCitiesByZip'])->where(['zip' => '[0-9]+'])->name('get.cities.by.zip');
    Route::get('/get-zips', [CustomerController::class, 'getZips']);
    Route::get('/get-city', [CustomerController::class, 'getCities']);

    //import
    Route::post('/stock_entry/import', [StockEntryController::class, 'import'])->name('stock_entry.import');
   
    //item-list        
    Route::get('/item-list', [ItemListController::class, 'index'])->name('item_list.index');
    Route::get('/item-list/create', [ItemListController::class, 'create'])->name('item_list.create');
    Route::get('/item-list/subindex/{itemList}', [ItemListController::class, 'subindex'])->name('item_list.subindex');
    Route::get('/item-list/close/{itemList}', [ItemListController::class, 'close'])->name('item_list.close');
    Route::get('/item-list/{itemList}/edit', [ItemListController::class, 'edit'])->name('item_list.edit');
    Route::post('/item-list', [ItemListController::class, 'store'])->name('item_list.store');
    Route::post('/item-list/import', [ItemListController::class, 'import'])->name('item_list.import');
    Route::post('/item-list/import-pdf', [ItemListController::class, 'importPdf'])->name('item_list.import_pdf');
    Route::post('/item-list/status', [ItemListController::class, 'setStatus'])->name('item_list.status');
    Route::post('/item-list/substatus', [ItemListController::class, 'setItemStatus'])->name('item_list.substatus');
    Route::get('/item-list/{itemList}/tetel/{sor}', [ItemListController::class, 'showTetel'])->name('item_list.row.show');
    Route::get('/item-list/{itemList}/tetel/{sor}/edit', [ItemListController::class, 'editTetel'])->name('item_list.row.edit');
    Route::post('/item-list/{itemList}/tetel/{sor}', [ItemListController::class, 'updateTetel'])->name('item_list.row.update');
    Route::get('/item-list/{itemList}/tetel/{sor}/transfer', [ItemListController::class, 'transferTetel'])->name('item_list.row.transfer');
    Route::post('/item-list/subimport', [StockEntryController::class, 'createFromImport'])->name('item_list.subimport');
    Route::get('/get-itemlist-rows', [ItemListController::class, 'getAllItemListRows'])->name('get.itemlist.rows');
    Route::get('/item-list/{id}/print-receipt', [ItemListController::class, 'printReceipt'])
    ->name('item-list.print-receipt');

    Route::resource('customercard', CustomerCardController::class);

    // Additional routes
    Route::get('customercard/{customercard}/show', [CustomerCardController::class, 'show'])->name('customercard.show');
    Route::get('api/valid-cards', [CustomerCardController::class, 'getValidCards'])->name('customercard.valid-cards');
    Route::post('api/calculate-discount', [CustomerCardController::class, 'calculateDiscount'])->name('customercard.calculate-discount');

    Route::get('/stock/get-actual-stock-by-product/{product_id}', [StockController::class, 'get_actual_stock_by_product'])
    ->name('stock.get-actual-stock-by-product');   
    // In your routes/web.php
    Route::get('/api/products/search', [ProductController::class, 'searchProducts'])->name('api.products.search');

    Route::get('/stocks/ajax-data', [StockController::class, 'getActualStockData'])->name('stocks.ajax-data');

    // API endpoint for custom QR strings
    Route::post('/api/sam4s/send-receipt', [Sam4sController::class, 'sendReceipt']);

    // Convenience endpoint for simple sales
    Route::post('/api/sam4s/send-simple-sale', [Sam4sController::class, 'sendSimpleSale']);

    Route::prefix('item-list-items')->group(function () {
        Route::get('/', [ItemListItemController::class, 'index'])->name('item-list-items.index');
        Route::post('/', [ItemListItemController::class, 'store'])->name('item-list-items.store');
        Route::get('/by-item-list/{itemListId}', [ItemListItemController::class, 'getByItemList']);
        Route::get('/{id}', [ItemListItemController::class, 'show']);
        Route::put('/{id}', [ItemListItemController::class, 'update']);
        Route::patch('/{id}/investigation', [ItemListItemController::class, 'updateInvestigation']);
        Route::delete('/{id}', [ItemListItemController::class, 'destroy']);
    });

    // Route to get document data
    Route::get('/get_document/{id}', [EmployeeController::class, 'getDocument'])->name('ajax.get.document');

    // Route to update document (if using resource controller)
    Route::put('/documents/{id}', [EmployeeController::class, 'updateDocument'])->name('documents.update_document');

    Route::get('{page}', 'PageController@index')->name('page.index');
        
});
Route::post('/ajax/get_inventory_items_by_warehouse', [StockController::class, 'getInventoryItemsByWarehouse']);
Route::get('/stock_entry/{stock_entry}/settings', [StockEntryController::class, 'settings'])->name('stock_entry.settings');
Route::post('/stock_entry/inventory/{inventory}/settings', [StockEntryController::class, 'saveInventorySettings'])->name('stock_entry.inventory.settings.save');   
Route::get('/stock_entry/{stock_entry}/settings', [StockEntryController::class, 'settings'])->name('stock_entry.settings');
Route::post('/stock_entry/inventory/{inventory}/settings', [StockEntryController::class, 'saveInventorySettings'])->name('stock_entry.inventory.settings.save');
