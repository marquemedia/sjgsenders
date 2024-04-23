<?php

use App\Http\Controllers\Admin\FrontendSectionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\NewPasswordController;
use App\Http\Controllers\Admin\Auth\ResetPasswordController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CurrencyController;
use App\Http\Controllers\Admin\GeneralSettingController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\PricingPlanController;
use App\Http\Controllers\Admin\SupportTicketController;
use App\Http\Controllers\Admin\MailConfigurationController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\SmsGatewayController;
use App\Http\Controllers\Admin\PhoneBookController;
use App\Http\Controllers\Admin\SmsController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\AndroidApiController;
use App\Http\Controllers\Admin\CampaignController;
use App\Http\Controllers\Admin\ManageEmailController;
use App\Http\Controllers\Admin\TemplateController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\ManualPaymentGatewayController;
use App\Http\Controllers\Admin\WhatsappController;
use App\Http\Controllers\Admin\WhatsappDeviceController;
use App\Http\Controllers\Admin\GlobalWorldController;
use App\Http\Controllers\Admin\Contact\ContactController;
use App\Http\Controllers\Admin\Contact\ContactGroupController;
use App\Http\Controllers\Admin\Contact\ContactSettingsController;
use App\Http\Controllers\UpgradeVersionMigrateController;




Route::prefix('admin')->name('admin.')->group(function () {

    
    Route::middleware(['upgrade'])->group(function () { 

        Route::get('/', [LoginController::class, 'showLogin'])->name('login');
        Route::post('authenticate', [LoginController::class, 'authenticate'])->name('authenticate');
        Route::get('logout', [LoginController::class, 'logout'])->name('logout');
    
        Route::get('forgot-password', [NewPasswordController::class, 'create'])->name('password.request');
        Route::post('password/email', [NewPasswordController::class, 'store'])->name('password.email');
        Route::get('password/verify/code', [NewPasswordController::class, 'passwordResetCodeVerify'])->name('password.verify.code');
        Route::post('password/code/verify', [NewPasswordController::class, 'emailVerificationCode'])->name('email.password.verify.code');
        Route::get('reset-password/{token}', [ResetPasswordController::class, 'create'])->name('password.reset');
        Route::post('reset/password', [ResetPasswordController::class, 'store'])->name('password.reset.update');

        //Upgrade version
        Route::get('update/verify', [UpgradeVersionMigrateController::class, 'verify'])->name('update.verify');
        Route::post('update/verify', [UpgradeVersionMigrateController::class, 'store'])->name('update.verify.store');
        Route::get('update/index', [UpgradeVersionMigrateController::class, 'index'])->name('update.index');
        Route::get('update/version', [UpgradeVersionMigrateController::class, 'update'])->name('update.version');


        Route::middleware(['admin','demo.mode'])->group(function () {
        
            //Dashboard
            Route::get('dashboard', [AdminController::class, 'index'])->name('dashboard');
    
            Route::get('/select/search', [AdminController::class, 'selectSearch'])->name('email.select2');
            Route::get('/select/gateway/{type?}', [AdminController::class, 'selectGateway'])->name('gateway.select2');
            Route::get('profile', [AdminController::class, 'profile'])->name('profile');
            Route::post('profile/update', [AdminController::class, 'profileUpdate'])->name('profile.update');
            Route::get('password', [AdminController::class, 'password'])->name('password');
            Route::post('password/update', [AdminController::class, 'passwordUpdate'])->name('password.update');
            Route::get('generate/api-key', [AdminController::class, 'generateApiKey'])->name('generate.api.key');
            Route::post('save/generate/api-key', [AdminController::class, 'saveGenerateApiKey'])->name('save.generate.api.key');
    
            //Manage Customer
            Route::prefix('users')->name('user.')->group(function () {
                Route::get('/', [CustomerController::class, 'index'])->name('index');
                Route::get('/detail/{id}', [CustomerController::class, 'details'])->name('details');
                Route::get('/login/{id}', [CustomerController::class, 'login'])->name('login');
                Route::post('/update/{id}', [CustomerController::class, 'update'])->name('update');
                Route::get('/search', [CustomerController::class, 'search'])->name('search');
                Route::get('/sms/contact/log/{id}', [CustomerController::class, 'contact'])->name('sms.contact');
                Route::get('/sms/log/{id}', [CustomerController::class, 'sms'])->name('sms');
                Route::get('/email/contact/log/{id}', [CustomerController::class, 'emailContact'])->name('email.contact');
                Route::get('/email/log/{id}', [CustomerController::class, 'emailLog'])->name('email');
                Route::post('/store/', [CustomerController::class, 'store'])->name('store');
                Route::post('/add/return/', [CustomerController::class, 'addReturnCredit'])->name('add.return');
            });
    
            //Pricing Plan
            Route::prefix('plans')->name('plan.')->group(function () {
                Route::get('/', [PricingPlanController::class, 'index'])->name('index');
                Route::get('/status', [PricingPlanController::class, 'status'])->name('status');
                Route::get('create', [PricingPlanController::class, 'create'])->name('create');
                Route::get('/select/gateways', [PricingPlanController::class, 'selectGateway'])->name('select2');
                Route::get('edit/{id}', [PricingPlanController::class, 'edit'])->name('edit');
                Route::post('/store', [PricingPlanController::class, 'store'])->name('store');
                Route::post('/update', [PricingPlanController::class, 'update'])->name('update');
                Route::post('/delete', [PricingPlanController::class, 'delete'])->name('delete');
                Route::get('/subscription', [PricingPlanController::class, 'subscription'])->name('subscription');
                Route::get('/subscription/search', [PricingPlanController::class, 'search'])->name('subscription.search');
                Route::post('/subscription/approved', [PricingPlanController::class, 'subscriptionApproved'])->name('subscription.approved');
            });
    
    
            //SMS Gateway
            Route::prefix('sms/gateways/')->name('sms.gateway.')->group(function () {
                Route::get('sms-api', [SmsGatewayController::class, 'smsApi'])->name('sms.api');
                Route::get('android', [SmsGatewayController::class, 'android'])->name('android');
                Route::get('default/status/update', [SmsGatewayController::class, 'defaultStatus'])->name('default.status');
                Route::get('delete', [SmsGatewayController::class, 'delete'])->name('delete');
                Route::post('create', [SmsGatewayController::class, 'create'])->name('create');
                Route::post('update', [SmsGatewayController::class, 'update'])->name('update');
                Route::post('default', [SmsGatewayController::class, 'defaultGateway'])->name('default');
            });
    
            //whatsapp Gateway
            Route::prefix('whatsapp/gateway/')->name('gateway.whatsapp.')->group(function () {
                
                Route::get('device', [WhatsappDeviceController::class, 'whatsAppDevices'])->name('device');
                Route::post('store', [WhatsappDeviceController::class, 'store'])->name('store');
                Route::get('edit/{id}', [WhatsappDeviceController::class, 'edit'])->name('edit');
                Route::post('update', [WhatsappDeviceController::class, 'update'])->name('update');
                Route::post('status-update', [WhatsappDeviceController::class, 'statusUpdate'])->name('status-update');
                Route::post('delete', [WhatsappDeviceController::class, 'delete'])->name('delete');
                Route::post('qr-code', [WhatsappDeviceController::class, 'whatsappQRGenerate'])->name('qrcode');
                Route::post('device/status', [WhatsappDeviceController::class, 'getDeviceStatus'])->name('device.status');
                Route::post('server/update', [WhatsappDeviceController::class, 'updateServer'])->name('server.update');
            });
    
    
            //sms log
            Route::prefix('sms/')->name('sms.')->group(function () {
                Route::get('', [SmsController::class, 'index'])->name('index');
                Route::get('create', [SmsController::class, 'create'])->name('create');
                Route::post('store', [SmsController::class, 'store'])->name('store');
                Route::get('search', [SmsController::class, 'search'])->name('search');
                Route::post('status/update', [SmsController::class, 'smsStatusUpdate'])->name('status.update');
                Route::post('delete', [SmsController::class, 'delete'])->name('delete');
            });
    
    
    
            //General Setting
            Route::prefix('general/setting')->name('general.setting.')->group(function () {
                Route::get('', [GeneralSettingController::class, 'index'])->name('index');
                Route::post('/store', [GeneralSettingController::class, 'store'])->name('store');
                Route::get('/cache/clear', [GeneralSettingController::class, 'cacheClear'])->name('cache.clear');
                Route::get('/passport/key', [GeneralSettingController::class, 'installPassportKey'])->name('passport.key');
                Route::get('system-info', [GeneralSettingController::class, 'systemInfo'])->name('system.info');
                Route::get('social-login', [GeneralSettingController::class, 'socialLogin'])->name('social.login');
                Route::get('recaptcha', [GeneralSettingController::class, 'recaptcha'])->name('recaptcha');
                Route::post('recaptcha/update', [GeneralSettingController::class, 'recaptchaUpdate'])->name('recaptcha.update');
                Route::post('social-login/update', [GeneralSettingController::class, 'socialLoginUpdate'])->name('social.login.update');
                Route::get('webhook/config', [GeneralSettingController::class, 'webhookConfig'])->name('webhook.config');
                Route::post('webhook/update', [GeneralSettingController::class, 'webhookUpdate'])->name('webhook.update');
    
                Route::get('frontend/section', [GeneralSettingController::class, 'frontendSection'])->name('frontend.section');
                Route::post('frontend/section/store', [GeneralSettingController::class, 'frontendSectionStore'])->name('frontend.section.store');
    
                //Currency
                Route::prefix('currencies')->name('currency.')->group(function () {
                    Route::get('/', [CurrencyController::class, 'index'])->name('index');
                    Route::post('/store', [CurrencyController::class, 'store'])->name('store');
                    Route::post('/update', [CurrencyController::class, 'update'])->name('update');
                    Route::post('/delete', [CurrencyController::class, 'delete'])->name('delete');
                });
            });
    
    
           //BEE FREE PLUGIN SETUP
            Route::controller(GeneralSettingController::class)->group(function(){
                Route::prefix('bee-plugin')->name('general.setting.beefree.')->group(function () {
                    Route::get('/','beefree')->name('plugin');
                    Route::post('/update','beefreeUpdate')->name('update');
                });
            });
    
            //CAMPAIGN ROUTE START
            Route::controller(CampaignController::class)->prefix('campaigns')->name('campaign.')->group(function(){
                Route::get('/sms','index')->name('sms');
                Route::get('/email','index')->name('email');
                Route::get('/whatsapp','index')->name('whatsapp');
                Route::get('/{type}/create','create')->name('create');
                Route::post('/store','store')->name('store');
                Route::post('/search','search')->name('search');
                Route::post('/delete','delete')->name('delete');
                Route::get('/contacts/{id}','contacts')->name('contacts');
                Route::get('/edit/{type}/{id}','edit')->name('edit');
                Route::post('/update','update')->name('update');
                Route::post('/contact/delete','contactDelete')->name('contact.delete');
            });
    
            //Support Ticket
            Route::prefix('support/tickets')->name('support.ticket.')->group(function () {
                Route::get('/', [SupportTicketController::class, 'index'])->name('index');
                Route::post('/reply/{id}', [SupportTicketController::class, 'ticketReply'])->name('reply');
                Route::post('/closed/{id}', [SupportTicketController::class, 'closedTicket'])->name('closeds');
                Route::get('/running', [SupportTicketController::class, 'running'])->name('running');
                Route::get('/replied', [SupportTicketController::class, 'replied'])->name('replied');
                Route::get('/answered', [SupportTicketController::class, 'answered'])->name('answered');
                Route::get('/closed', [SupportTicketController::class, 'closed'])->name('closed');
                Route::get('/details/{id}', [SupportTicketController::class, 'ticketDetails'])->name('details');
                Route::get('/download/{id}', [SupportTicketController::class, 'supportTicketDownload'])->name('download');
                Route::get('/search/{scope}', [SupportTicketController::class, 'search'])->name('search');
            });
    
            //Mail Configuration
            Route::prefix('mail/gateways/')->name('mail.')->group(function () {
                Route::get('', [MailConfigurationController::class, 'index'])->name('list');
                Route::post('update', [MailConfigurationController::class, 'gatewayUpdate'])->name('update');
                Route::post('create', [MailConfigurationController::class, 'create'])->name('create');
                Route::get('default/status/update', [MailConfigurationController::class, 'defaultStatus'])->name('default.status');
                Route::get('delete', [MailConfigurationController::class, 'delete'])->name('delete');
                Route::post('send', [MailConfigurationController::class, 'sendMailMethod'])->name('send.method');
                Route::post('global-template/update', [MailConfigurationController::class, 'globalTemplateUpdate'])->name('global.template.update');
                Route::post('test', [MailConfigurationController::class, 'mailTester'])->name('test');
            });
    
            //Email Templates
            Route::prefix('mail/templates/')->name('mail.templates.')->group(function () {
                Route::get('edit/old/{id}', [EmailTemplateController::class, 'edit'])->name('edit');
                Route::post('update/{id}', [EmailTemplateController::class, 'update'])->name('update');
            });
    
            //Payment Method
            Route::prefix('payment/methods/')->name('payment.method.')->group(function () {
                Route::get('', [PaymentMethodController::class, 'index'])->name('index');
                Route::post('update/{id}', [PaymentMethodController::class, 'update'])->name('update');
                Route::get('edit/{slug}/{id}', [PaymentMethodController::class, 'edit'])->name('edit');
            });
    
            //Manual Payment Method
            Route::prefix('manual/payment/')->name('manual.payment.')->group(function () {
                Route::get('methods', [ManualPaymentGatewayController::class, 'index'])->name('index');
                Route::get('create', [ManualPaymentGatewayController::class, 'create'])->name('create');
                Route::post('store', [ManualPaymentGatewayController::class, 'store'])->name('store');
                Route::get('edit/{id}', [ManualPaymentGatewayController::class, 'edit'])->name('edit');
                Route::post('update/{id}', [ManualPaymentGatewayController::class, 'update'])->name('update');
                Route::post('delete', [ManualPaymentGatewayController::class, 'delete'])->name('delete');
            });
    
            //Report and logs
            Route::prefix('reports')->name('report.')->group(function () {
                Route::get('transactions', [ReportController::class, 'transaction'])->name('transaction.index');
                Route::get('transactions/search', [ReportController::class, 'transactionSearch'])->name('transaction.search');
    
                Route::get('sms/credits', [ReportController::class, 'credit'])->name('credit.index');
                Route::get('sms/credit/search', [ReportController::class, 'creditSearch'])->name('credit.search');
    
                Route::get('whatsapp/credits', [ReportController::class, 'whatsappcredit'])->name('whatsapp.index');
                Route::get('whatsapp/credit/search', [ReportController::class, 'whatsappcreditSearch'])->name('whatsapp.search');
    
                Route::get('email/credits', [ReportController::class, 'emailCredit'])->name('email.credit.index');
                Route::get('email/credit/search', [ReportController::class, 'emailCreditSearch'])->name('email.credit.search');
    
                Route::get('payment/log', [ReportController::class, 'paymentLog'])->name('payment.index');
                Route::get('payment/detail/{id}', [ReportController::class, 'paymentDetail'])->name('payment.detail');
                Route::post('payment/approve', [ReportController::class, 'approve'])->name('payment.approve');
                Route::post('payment/reject', [ReportController::class, 'reject'])->name('payment.reject');
                Route::get('payment/search', [ReportController::class, 'paymentLogSearch'])->name('payment.search');
                Route::get('subscriptions', [ReportController::class, 'subscription'])->name('subscription.index');
                Route::get('subscription/search', [ReportController::class, 'subscriptionSearch'])->name('subscription.search');
            });
    
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
            });
    
    
    
            //Whatsapp log
            Route::prefix('whatsapp/')->name('whatsapp.')->group(function () {
                Route::get('', [WhatsappController::class, 'index'])->name('index');
                Route::get('create', [WhatsappController::class, 'create'])->name('create');
                Route::post('store', [WhatsappController::class, 'store'])->name('store');
                Route::get('search', [WhatsappController::class, 'search'])->name('search');
                Route::post('status/update', [WhatsappController::class, 'statusUpdate'])->name('status.update');
                Route::post('delete', [WhatsappController::class, 'delete'])->name('delete');
            });
    
            //Email log
            Route::prefix('email/')->name('email.')->group(function () {
                Route::get('', [ManageEmailController::class, 'index'])->name('index');
                Route::get('send', [ManageEmailController::class, 'create'])->name('send');
                Route::post('store', [ManageEmailController::class, 'store'])->name('store');
                Route::get('pending', [ManageEmailController::class, 'pending'])->name('pending');
                Route::get('delivered', [ManageEmailController::class, 'success'])->name('success');
                Route::get('schedule', [ManageEmailController::class, 'schedule'])->name('schedule');
                Route::get('failed', [ManageEmailController::class, 'failed'])->name('failed');
                Route::get('search', [ManageEmailController::class, 'search'])->name('search');
                Route::post('status/update', [ManageEmailController::class, 'emailStatusUpdate'])->name('status.update');
                Route::get('single/mail/send/{id}', [ManageEmailController::class, 'emailSend'])->name('single.mail.send');
                Route::get('view/{id}', [ManageEmailController::class, 'viewEmailBody'])->name('view');
                Route::post('delete', [ManageEmailController::class, 'delete'])->name('delete');
            });
    
            //android gateway
            Route::prefix('android/gateway/')->name('sms.gateway.android.')->group(function () {
                Route::get('gateway', [AndroidApiController::class, 'index'])->name('index');
                Route::post('store', [AndroidApiController::class, 'store'])->name('store');
                Route::post('update', [AndroidApiController::class, 'update'])->name('update');
                Route::get('sim/list/{id}', [AndroidApiController::class, 'simList'])->name('sim.index');
                Route::post('delete/', [AndroidApiController::class, 'delete'])->name('delete');
                Route::post('sim/delete/', [AndroidApiController::class, 'simNumberDelete'])->name('sim.delete');
            });
    
    
            //Template
            Route::controller(EmailTemplateController::class)->prefix('email/templates')->name('template.email.')->group(function (){
                Route::any('/list-user','userTemplates')->name('list.user');
                Route::any('/list-own','adminTemplates')->name('list.own');
                Route::any('/list-default','defaultTemplates')->name('list.default');
                Route::any('/list-global','globalTemplates')->name('list.global');
                Route::get('/create','create')->name('create');
                Route::post('/store','store')->name('store');
                Route::post('/update/templates','updateTemplates')->name('update');
    
                Route::get('/get/{id}','templateJson')->name('select');
                Route::get('/edit/{id}','editTemplate')->name('edit');
                Route::get('/edit/json/{id}','templateJsonEdit')->name('edit.json');
                Route::post('/delete','delete')->name('delete');
                Route::post('/status/update','statusUpdate')->name('status.update');
            });
    
    
            Route::prefix('sms/templates/')->name('template.')->group(function () {

                Route::get("index/{type?}/{id?}", [TemplateController::class, "index"])->name("index");
                Route::get('user-templates', [TemplateController::class, 'userTemplate'])->name('user');
                Route::get('admin-templates', [TemplateController::class, 'adminTemplate'])->name('own');
                Route::post('store', [TemplateController::class, 'store'])->name('store');
                Route::post('update', [TemplateController::class, 'update'])->name('update');
                Route::post('delete', [TemplateController::class, 'delete'])->name('delete');
                Route::get('user', [TemplateController::class, 'userIndex'])->name('user.index');
                Route::post('user/status', [TemplateController::class, 'updateStatus'])->name('userStatus.update');
            });

            Route::prefix('whatsapp/templates/')->name('template.')->group(function () {
                
                // Route::post('whatsapp/store', [TemplateController::class, 'store'])->name('whatsapp.store');
                // Route::post('whatsapp/update', [TemplateController::class, 'update'])->name('whatsapp.update');
                // Route::post('whatsapp/delete', [TemplateController::class, 'delete'])->name('whatsapp.delete');
                Route::get('whatsapp/refresh', [TemplateController::class, 'whatsAppRefresh'])->name('whatsapp.refresh');
                Route::get('fetch/{type?}', [TemplateController::class, 'fetch'])->name('fetch');
            });
    
            //Language
            Route::prefix('languages/')->name('language.')->group(function () {
                Route::get('', [LanguageController::class, 'index'])->name('index');
                Route::post('store', [LanguageController::class, 'store'])->name('store');
                Route::post('update', [LanguageController::class, 'update'])->name('update');
                Route::get('translate/{code}', [LanguageController::class, 'translate'])->name('translate');
                Route::post('data/store', [LanguageController::class, 'languageDataStore'])->name('data.store');
                Route::post('data/update', [LanguageController::class, 'languageDataUpdate'])->name('data.update');
                Route::post('delete', [LanguageController::class, 'languageDelete'])->name('delete');
                Route::post('data/delete', [LanguageController::class, 'languageDataUpDelete'])->name('data.delete');
                Route::post('default', [LanguageController::class, 'setDefaultLang'])->name('default');
            });
    
            // Global world
            Route::prefix('spam/word/')->name('spam.word.')->group(function () {
                Route::get('', [GlobalWorldController::class, 'index'])->name('index');
                Route::post('store', [GlobalWorldController::class, 'store'])->name('store');
                Route::post('update', [GlobalWorldController::class, 'update'])->name('update');
                Route::post('delete', [GlobalWorldController::class, 'delete'])->name('delete');
            });
    
            // Global world
            Route::prefix('frontend/section/')->name('frontend.sections.')->group(function () {
                Route::get('{section_key}', [FrontendSectionController::class, 'index'])->name('index');
                Route::post('/save/content/{section_key}', [FrontendSectionController::class, 'saveFrontendSectionContent'])->name('save.content');
                Route::get('/element/content/{section_key}/{id?}', [FrontendSectionController::class, 'getFrontendSectionElement'])->name('element.content');
                Route::post('/element/delete/', [FrontendSectionController::class, 'delete'])->name('element.delete');
            });
        });
    });

     


	
});
