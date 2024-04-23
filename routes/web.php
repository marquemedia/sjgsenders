 <?php

use App\Http\Controllers\AuthorizationProcessController;
use App\Http\Controllers\User\AndroidApiController;
use App\Http\Controllers\User\EmailApiGatewayController;
use App\Http\Controllers\User\SmsApiGatewayController;
use App\Http\Controllers\User\WhatsappDeviceController;
use App\Http\Controllers\WebController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\CronController;
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\User\PhoneBookController;
use App\Http\Controllers\User\ManageSMSController;
use App\Http\Controllers\User\PlanController;
use App\Http\Controllers\User\EmailContactController;
use App\Http\Controllers\User\ManageEmailController;
use App\Http\Controllers\User\SupportTicketController;
use App\Http\Controllers\PaymentMethod\PaymentController;
use App\Http\Controllers\PaymentMethod\PaymentWithStripe;
use App\Http\Controllers\PaymentMethod\PaymentWithPaypal;
use App\Http\Controllers\PaymentMethod\PaymentWithPayStack;
use App\Http\Controllers\PaymentMethod\PaymentWithPaytm;
use App\Http\Controllers\PaymentMethod\PaymentWithFlutterwave;
use App\Http\Controllers\PaymentMethod\PaymentWithRazorpay;
use App\Http\Controllers\PaymentMethod\PaymentWithInstamojo;
use App\Http\Controllers\PaymentMethod\SslCommerzPaymentController;
use App\Http\Controllers\PaymentMethod\CoinbaseCommerce;
use App\Http\Controllers\User\CampaignController;
use App\Http\Controllers\User\EmailTemplateController;
use App\Http\Controllers\User\ManageWhatsappController;
use App\Http\Controllers\User\Contact\ContactController;
use App\Http\Controllers\User\Contact\ContactGroupController;
use App\Http\Controllers\User\Contact\ContactSettingsController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

Route::get('queue-work', function () {

    if (Session::get('queue_restart', true)) {
        \Illuminate\Support\Facades\Artisan::call('queue:restart');
        Session::forget('queue_restart');
    }
    Illuminate\Support\Facades\Artisan::call('queue:work', ['--stop-when-empty' => true]);
    Illuminate\Support\Facades\Artisan::call('whatsapp:send');
    Illuminate\Support\Facades\Artisan::call('email:send');
    Illuminate\Support\Facades\Artisan::call('sms:send');
  
})->name('queue.work');

Route::get('cron/run', [CronController::class, 'run'])->name('cron.run');
Route::get('/select/search', [FrontendController::class, 'selectSearch'])->name('email.select2');

Route::middleware(['auth','checkUserStatus','maintenance','demo.mode'])->prefix('user')->name('user.')->group(function () {
    
    Route::get('authorization', [AuthorizationProcessController::class, 'process'])->name('authorization.process');
    Route::get('email/verification', [AuthorizationProcessController::class, 'processEmailVerification'])->name('email.verification');
    Route::post('email/verification', [AuthorizationProcessController::class, 'emailVerification'])->name('store.email.verification');

    Route::middleware(['authorization', 'upgrade'])->group(function(){

        Route::get('/select/gateway/{type?}', [HomeController::class, 'selectGateway'])->name('gateway.select2');
        
        //Contacts
        Route::prefix('contacts/')->name('contact.')->group(function () {

            Route::get("index/{id?}", [ContactController::class, "index"])->name("index");
            Route::get("create", [ContactController::class, "create"])->name("create");
            Route::get("search", [ContactController::class, "search"])->name("search");
            Route::post("store", [ContactController::class, "store"])->name("store");
            Route::post("update", [ContactController::class, "update"])->name("update");
            Route::post("delete", [ContactController::class, "delete"])->name("delete");
            Route::post("upload/file", [ContactController::class, "uploadFile"])->name("upload.file");
            Route::post("delete/file", [ContactController::class, "deleteFile"])->name("delete.file");
            Route::post("parse/file", [ContactController::class, "parseFile"])->name("parse.file");
            
            Route::get("demo/file/{type?}", [ContactController::class, "demoFile"])->name("demo.file");

            Route::prefix('bulk/')->name('bulk.')->group(function () { 

                Route::post("status/update", [ContactController::class, "bulkStatusUpdate"])->name("status.update");
                Route::get("delete", [ContactController::class, "bulkDelete"])->name("delete");
            });
            
            Route::prefix('groups/')->name('group.')->group(function () { 

                Route::get("index/{id?}", [ContactGroupController::class, "index"])->name("index");
                Route::get("search", [ContactGroupController::class, "search"])->name("search");
                Route::post("store", [ContactGroupController::class, "store"])->name("store");
                Route::post("update", [ContactGroupController::class, "update"])->name("update");
                Route::post("delete", [ContactGroupController::class, "delete"])->name("delete");
                Route::post("fetch/{type?}", [ContactGroupController::class, "fetch"])->name("fetch");

                Route::prefix('bulk/')->name('bulk.')->group(function () { 

                    Route::post("status/update", [ContactGroupController::class, "bulkStatusUpdate"])->name("status.update");
                    Route::get("delete", [ContactGroupController::class, "groupBulkDelete"])->name("delete");
                });
            });

            Route::prefix('settings/')->name('settings.')->group(function () { 

                Route::get("index", [ContactSettingsController::class, "settings"])->name("index");
                Route::get("attribute/search", [ContactSettingsController::class, "attributeSearch"])->name("attribute.search");
                Route::post('attribute/store', [ContactSettingsController::class, 'attributeStore'])->name('store');
                Route::post("attribute/update", [ContactSettingsController::class, "attributeUpdate"])->name("update");
                Route::get("attribute/status/update", [ContactSettingsController::class, "attributeStatusUpdate"])->name("status.update");
                Route::post("attribute/delete", [ContactSettingsController::class, "attributeDelete"])->name("delete");
            });

            Route::get('sms', [PhoneBookController::class, 'smsContactIndex'])->name('sms.index');
            Route::get('sms/export', [PhoneBookController::class, 'contactExport'])->name('sms.export');
            Route::get('email', [PhoneBookController::class, 'emailContactIndex'])->name('email.index');
            Route::get('email/export', [PhoneBookController::class, 'emailContactExport'])->name('email.export');
        });

    	Route::get('dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
    	Route::get('profile', [HomeController::class, 'profile'])->name('profile');
    	Route::post('profile/update', [HomeController::class, 'profileUpdate'])->name('profile.update');
        Route::get('gateway/sms/send-method/api', [HomeController::class, 'defaultSmsMethod'])->name('sms.gateway.sendmethod.api')->middleware(['allow.access']);
        Route::get('gateway/sms/send-method/gateway', [HomeController::class, 'defaultSmsMethod'])->name('sms.gateway.sendmethod.gateway');
        Route::get('gateway/sms/send-method/android', [HomeController::class, 'defaultSmsMethod'])->name('gateway.sendmethod.android')->middleware(['allow.access']);
        Route::post('default/sms/gateway', [HomeController::class, 'defaultSmsGateway'])->name('default.sms.gateway')->middleware(['allow.access']);
    	Route::get('password', [HomeController::class, 'password'])->name('password');
    	Route::post('password/update', [HomeController::class, 'passwordUpdate'])->name('password.update');
        Route::get('generate/api-key', [HomeController::class, 'generateApiKey'])->name('generate.api.key');
        Route::post('save/generate/api-key', [HomeController::class, 'saveGenerateApiKey'])->name('save.generate.api.key');
        Route::post('save/generate/api-key', [HomeController::class, 'saveGenerateApiKey'])->name('save.generate.api.key');
        Route::get('fetch/{type?}', [HomeController::class, 'fetch'])->name('template.fetch');

        //SMS Gateway
        Route::middleware(['allow.access'])->prefix('sms/gateways/')->name('sms.gateway.')->group(function () {
            Route::get('edit/{id}', [SmsApiGatewayController::class, 'edit'])->name('edit');
            Route::post('update', [SmsApiGatewayController::class, 'update'])->name('update');
            Route::post('default', [SmsApiGatewayController::class, 'defaultGateway'])->name('default');
            Route::get('default/status/update', [SmsApiGatewayController::class, 'defaultStatus'])->name('default.status');
            Route::get('delete', [SmsApiGatewayController::class, 'delete'])->name('delete');
            Route::post('create', [SmsApiGatewayController::class, 'create'])->name('create');
        });

        //Email Gateway
        Route::middleware(['allow.access'])->prefix('mail/gateways/')->name('mail.gateway.')->group(function () {
            Route::get('', [EmailApiGatewayController::class, 'index'])->name('configuration');
            Route::post('update', [EmailApiGatewayController::class, 'update'])->name('update');
            Route::post('default', [EmailApiGatewayController::class, 'defaultGateway'])->name('default.method');
            Route::post('create', [EmailApiGatewayController::class, 'create'])->name('create');
            Route::get('default/status/update', [EmailApiGatewayController::class, 'defaultStatus'])->name('default.status');
            Route::get('delete', [EmailApiGatewayController::class, 'delete'])->name('delete');
        });

        //Transaction Log
        Route::get('transaction/log', [HomeController::class, 'transaction'])->name('transaction.history');
        Route::get('transaction/search', [HomeController::class, 'transactionSearch'])->name('transaction.search');

        //Payment Log
        Route::get('payment/log', [HomeController::class, 'payment'])->name('payment.history');
        Route::get('payment/search', [HomeController::class, 'paymentSearch'])->name('payment.search');

        //Credit Log
        Route::get('credit/log', [HomeController::class, 'credit'])->name('credit.history');
        Route::get('credit/search', [HomeController::class, 'creditSearch'])->name('credit.search');

        //Whatsapp credit Log
        Route::get('whatsapp/credit/log', [HomeController::class, 'whatsappCredit'])->name('whatsapp.credit.history');
        Route::get('whatsapp/credit/search', [HomeController::class, 'whatsappCreditSearch'])->name('whatsapp.credit.search');

        //Email credit Log
        Route::get('email/credit/log', [HomeController::class, 'emailCredit'])->name('credit.email.history');
        Route::get('email/credit/search', [HomeController::class, 'emailCreditSearch'])->name('credit.email.search');


        //CAMPAIGN ROUTE START
		Route::controller(CampaignController::class)->prefix('campaigns')->name('campaign.')->group(function(){
			Route::get('/sms','index')->name('sms')->middleware(['allow.access']);
			Route::get('/email','index')->name('email')->middleware(['allow.access']);
			Route::get('/whatsapp','index')->name('whatsapp')->middleware(['allow.access']);
			Route::get('/{type}/create','create')->name('create');
			Route::post('/store','store')->name('store');
			Route::post('/search','search')->name('search');
			Route::post('/delete','delete')->name('delete');
			Route::get('/contacts/{id}','contacts')->name('contacts');
			Route::get('/edit/{type}/{id}','edit')->name('edit');
			Route::post('/update','update')->name('update');
			Route::post('/contact/delete','contactDelete')->name('contact.delete');
        });

        //EMAIL TEMPLATE ROUTE START
		Route::controller(EmailTemplateController::class)->prefix('email/templates')->name('template.email.')->group(function (){
			Route::any('/list','templates')->name('list');
			Route::get('/create','create')->name('create');
			Route::post('/store','store')->name('store');
			Route::post('/update/templates','updateTemplates')->name('update');
			Route::get('/get/{id}','templateJson')->name('select');
			Route::get('/edit/{id}','editTemplate')->name('edit');
			Route::get('/edit/json/{id}','templateJsonEdit')->name('edit.json');
			Route::post('/delete','delete')->name('delete');
		});

    	//Phone book
    	Route::get('sms/groups', [PhoneBookController::class, 'groupIndex'])->name('phone.book.group.index');
        Route::get('sms/contact/group/{id}', [PhoneBookController::class, 'smsContactByGroup'])->name('phone.book.sms.contact.group');
    	Route::post('sms/group/store', [PhoneBookController::class, 'groupStore'])->name('phone.book.group.store');
    	Route::post('sms/group/update', [PhoneBookController::class, 'groupUpdate'])->name('phone.book.group.update');
    	Route::post('sms/group/delete', [PhoneBookController::class, 'groupdelete'])->name('phone.book.group.delete');




        //Sms Contacts
        Route::prefix('sms/contacts/')->name('phone.book.contact.')->group(function () {
            Route::get('', [PhoneBookController::class, 'contactIndex'])->name('index');
            Route::post('store', [PhoneBookController::class, 'contactStore'])->name('store');
            Route::post('update', [PhoneBookController::class, 'contactUpdate'])->name('update');
            Route::post('delete', [PhoneBookController::class, 'contactDelete'])->name('delete');
            Route::post('import', [PhoneBookController::class, 'contactImport'])->name('import');
            Route::get('export', [PhoneBookController::class, 'contactExport'])->name('export');
            Route::get('group/export/{id}', [PhoneBookController::class, 'contactGroupExport'])->name('group.export');
        });

        //SMS templates
        Route::prefix('sms/templates/')->name('phone.book.template.')->group(function () {
            Route::get('', [PhoneBookController::class, 'templateIndex'])->name('index');
            Route::post('store', [PhoneBookController::class, 'templateStore'])->name('store');
            Route::post('update', [PhoneBookController::class, 'templateUpdate'])->name('update');
            Route::post('delete', [PhoneBookController::class, 'templateDelete'])->name('delete');
        });

        //Email Groups
        Route::prefix('email/groups/')->name('email.group.')->group(function () {
            Route::get('', [EmailContactController::class, 'emailGroupIndex'])->name('index');
            Route::get('contact/{id}', [EmailContactController::class, 'emailContactByGroup'])->name('contact');
            Route::post('store', [EmailContactController::class, 'emailGroupStore'])->name('store');
            Route::post('update', [EmailContactController::class, 'emailGroupUpdate'])->name('update');
            Route::post('delete', [EmailContactController::class, 'emailGroupdelete'])->name('delete');
        });

        //Emails Log
        Route::middleware(['allow.access'])->prefix('emails')->name('manage.email.')->group(function () {
            Route::get('send', [ManageEmailController::class, 'create'])->name('send');
            Route::post('store', [ManageEmailController::class, 'store'])->name('store');
            Route::get('/', [ManageEmailController::class, 'index'])->name('index');
            Route::get('search', [ManageEmailController::class, 'search'])->name('search');
            Route::get('view/{id}', [ManageEmailController::class, 'view'])->name('view');
            Route::post('status/update', [ManageEmailController::class, 'emailStatusUpdate'])->name('status.update');
            
        });

        //Sms log
        Route::middleware(['allow.access'])->prefix('sms/')->name('sms.')->group(function () {
            Route::get('', [ManageSMSController::class, 'index'])->name('index');
            Route::post('store', [ManageSMSController::class, 'store'])->name('store');
            Route::get('send', [ManageSMSController::class, 'create'])->name('send');
            Route::get('search', [ManageSMSController::class, 'search'])->name('search');
            Route::post('status/update', [ManageSMSController::class, 'smsStatusUpdate'])->name('status.update');
            
        });

         //whatsapp log
        Route::middleware(['allow.access'])->prefix('whatsapp/')->name('whatsapp.')->group(function () {
            Route::get('', [ManageWhatsappController::class, 'index'])->name('index');
            Route::get('send', [ManageWhatsappController::class, 'create'])->name('send');
            Route::get('search', [ManageWhatsappController::class, 'search'])->name('search');
            Route::post('store', [ManageWhatsappController::class, 'store'])->name('store');
            
        });

        //Plan
        Route::prefix('plans/')->name('plan.')->group(function () {
            Route::get('', [PlanController::class, 'create'])->name('create');
            Route::post('store', [PlanController::class, 'store'])->name('store');
            Route::get('subscriptions', [PlanController::class, 'subscription'])->name('subscription');
            Route::post('renew', [PlanController::class, 'subscriptionRenew'])->name('renew');
        });

        //Payment
        Route::get('payment/preview', [PaymentController::class, 'preview'])->name('payment.preview');
        Route::get('payment/confirm', [PaymentController::class, 'paymentConfirm'])->name('payment.confirm');
        Route::get('manual/payment/confirm', [PaymentController::class, 'manualPayment'])->name('manual.payment.confirm');
        Route::post('manual/payment/update', [PaymentController::class, 'manualPaymentUpdate'])->name('manual.payment.update');

        //Payment Action
        Route::post('ipn/strip', [PaymentWithStripe::class, 'stripePost'])->name('payment.with.strip');
        Route::get('/strip/success', [PaymentWithStripe::class, 'success'])->name('payment.with.strip.success');
        Route::post('ipn/paypal', [PaymentWithPaypal::class, 'postPaymentWithpaypal'])->name('payment.with.paypal');
        Route::get('ipn/paypal/status', [PaymentWithPaypal::class, 'getPaymentStatus'])->name('payment.paypal.status');
        Route::get('ipn/paystack', [PaymentWithPayStack::class, 'store'])->name('payment.with.paystack');
        Route::post('ipn/pay/with/sslcommerz', [SslCommerzPaymentController::class, 'index'])->name('payment.with.ssl');
        Route::post('success', [SslCommerzPaymentController::class, 'success']);
        Route::post('fail', [SslCommerzPaymentController::class, 'fail']);
        Route::post('cancel', [SslCommerzPaymentController::class, 'cancel']);
        Route::post('/ipn', [SslCommerzPaymentController::class, 'ipn']);

        Route::post('ipn/paytm/process', [PaymentWithPaytm::class,'getTransactionToken'])->name('paytm.process');
        Route::post('ipn/paytm/callback', [PaymentWithPaytm::class,'ipn'])->name('paytm.ipn');

        Route::get('flutter-wave/{trx}/{type}', [PaymentWithFlutterwave::class,'callback'])->name('flutterwave.callback');
        Route::post('ipn/razorpay', [PaymentWithRazorpay::class,'ipn'])->name('razorpay');

        Route::get('instamojo', [PaymentWithInstamojo::class,'process'])->name('instamojo');
        Route::post('ipn/instamojo', [PaymentWithInstamojo::class,'ipn'])->name('ipn.instamojo');

        Route::get('ipn/coinbase', [CoinbaseCommerce::class, 'store'])->name('coinbase');
        Route::any('ipn/callback/coinbase', [CoinbaseCommerce::class, 'confirmPayment'])->name('callback.coinbase');

        //Support Ticket
        Route::prefix('support/tickets/')->name('ticket.')->group(function () {
            Route::get('', [SupportTicketController::class, 'index'])->name('index');
            Route::get('create', [SupportTicketController::class, 'create'])->name('create');
            Route::post('store', [SupportTicketController::class, 'store'])->name('store');
            Route::get('reply/{id}', [SupportTicketController::class, 'detail'])->name('detail');
            Route::post('reply/{id}', [SupportTicketController::class, 'ticketReply'])->name('reply');
            Route::post('closed/{id}', [SupportTicketController::class, 'closedTicket'])->name('closed');
            Route::get('file/download/{id}', [SupportTicketController::class, 'supportTicketDownloader'])->name('file.download');
        });

        //whatsapp Gateway
        Route::middleware(['allow.access'])->prefix('whatsapp/gateways/')->name('gateway.whatsapp.')->group(function () {

            Route::get('create', [WhatsappDeviceController::class, 'create'])->name('create');
            Route::post('store/{type?}', [WhatsappDeviceController::class, 'store'])->name('store');;
            Route::get('edit/{id}', [WhatsappDeviceController::class, 'edit'])->name('edit');
            Route::post('update/{type?}', [WhatsappDeviceController::class, 'update'])->name('update');
            Route::post('status-update', [WhatsappDeviceController::class, 'statusUpdate'])->name('status-update');
            Route::post('delete', [WhatsappDeviceController::class, 'delete'])->name('delete');
            Route::post('qr-code', [WhatsappDeviceController::class, 'getWaqr'])->name('qrcode');
            Route::post('device/status', [WhatsappDeviceController::class, 'getDeviceStatus'])->name('device.status');

            Route::get('cloud/template//{type?}/{id?}', [WhatsappDeviceController::class, 'cloudTemplate'])->name('cloud.template');
            Route::get('cloud/refresh/{type?}', [WhatsappDeviceController::class, 'cloudRefresh'])->name('cloud.refresh');
            Route::get('cloud/fetch/{type?}', [WhatsappDeviceController::class, 'cloudFetch'])->name('cloud.fetch');
        });

        //android gateway
        Route::middleware(['allow.access'])->prefix('android/gateways/')->name('gateway.sms.android.')->group(function () {
            Route::post('store', [AndroidApiController::class, 'store'])->name('store');
            Route::post('update', [AndroidApiController::class, 'update'])->name('update');
            Route::get('sim/list/{id}', [AndroidApiController::class, 'simList'])->name('sim.index');
            Route::post('delete/', [AndroidApiController::class, 'delete'])->name('delete');
            Route::post('sim/delete/', [AndroidApiController::class, 'simNumberDelete'])->name('sim.delete');
        });
    });
});
Route::middleware(['redirect.to.login'])->group(function () {
    Route::get('/', [WebController::class, 'index'])->name('home');
    Route::get('about/', [WebController::class, 'about'])->name('about');
    Route::get('pricing/', [WebController::class, 'pricing'])->name('pricing');
    Route::get('features/', [WebController::class, 'features'])->name('features');
    Route::get('contact/', [WebController::class, 'contact'])->name('contact');
    Route::get('faq/', [WebController::class, 'faq'])->name('faq');
});

Route::get('/pages/{key}/{id}', [WebController::class, 'pages'])->name('page');
Route::get('/language/change/{lang?}', [FrontendController::class, 'languageChange'])->name('language.change');
Route::get('/default/image/{size}', [FrontendController::class, 'defaultImageCreate'])->name('default.image');
Route::get('email/contact/demo/file', [FrontendController::class, 'demoImportFile'])->name('email.contact.demo.import');
Route::get('sms/demo/import/file', [FrontendController::class, 'demoImportFilesms'])->name('phone.book.demo.import.file');
Route::get('demo/file/download/{extension}/{type}', [FrontendController::class, 'demoFileDownloader'])->name('demo.file.download');
Route::get('api/document', [FrontendController::class, 'apiDocumentation'])->name('api.document');
Route::get('/default-captcha/{randCode}', [HomeController::class, 'defaultCaptcha'])->name('captcha.genarate');
Route::any('/webhook', [HomeController::class, 'postWebhook'])->name('webhook');