
@php
    $smsRouteNames = [
        'admin.sms.create',
        'admin.sms.search',
        'admin.campaign.sms',
        'admin.sms.index',
    ];

    $menuSmsActiveRoute = [
        'admin.campaign.sms'
    ];

    if (request()->route()->type == 'sms' ) {

        $smsRouteNames[4] = 'admin.campaign.create';
        $smsRouteNames[5] = 'admin.campaign.edit';
        $menuSmsActiveRoute[1] = 'admin.campaign.create';
        $menuSmsActiveRoute[2] = 'admin.campaign.edit';
    }

    $isSmsActive = request()->routeIs($smsRouteNames);

@endphp
<aside class="sidebar" id="sidebar">
    <div class="sidebar-top">
        <div class="site-logo">
            @php
                $panel_logo =  $general->panel_logo ?? "panel_logo.png";
                $site_icon  = $general->site_icon ?? "site_icon.png";
            @endphp

            <a href="{{route('admin.dashboard')}}">
                <img src="{{showImage(filePath()['panel_logo']['path'].'/'.$panel_logo,filePath()['panel_logo']['size'])}}" class="logo-lg" alt="">
                <img src="{{showImage(filePath()['site_logo']['path'].'/'.$site_icon)}}" class="logo-sm" alt="">
            </a>
        </div>

        <div class="menu-search-container">
            <input class=" form-control menu-search" placeholder="{{translate('Search Here')}}" type="search" name="" id="searchMenu">
        </div>
    </div>

    <div class="sidebar-menu-container" data-simplebar>
        <ul class="sidebar-menu">
            <li class="sidebar-menu-item">
                <a class="sidebar-menu-link {{request()->routeIs('admin.dashboard') ? "active" :""}}" href="{{route('admin.dashboard')}}">
                    <span><i class="las la-tachometer-alt"></i></span>
                    <p>{{ translate('Dashboard')}}</p>
                </a>
            </li>

            <!-- <li class="sidebar-menu-item">
                <a class="sidebar-menu-link {{menuActive(['admin.plan.index', 'admin.plan.create', 'admin.plan.edit'])}}" href="{{route('admin.plan.index')}}">
                    <span><i class="lab la-buffer"></i></span>
                    <p>{{ translate('Membership Plan')}}</p>
                </a>
            </li> -->

            <!-- <li class="sidebar-menu-item">
                <a class="sidebar-menu-link {{menuActive('admin.user.*')}}" href="{{route('admin.user.index')}}">
                    <span><i class="las la-users-cog"></i></span>
                    <p>{{ translate('Manage Users')}}</p>
                </a>
            </li> -->

            <li class="sidebar-menu-title" data-text="{{ translate('Gateway & Contacts')}}">{{ translate('Gateway & Contacts')}}</li>
            @php
                $isGatewayActive = request()->routeIs('admin.sms.gateway.sms.api', 'admin.sms.gateway.android', 'admin.gateway.whatsapp.device', 'admin.mail.list',  'admin.mail.edit', 'admin.template.index');
            @endphp
            <!-- <li class="sidebar-menu-item">
                <a class="sidebar-menu-link collapsed {{request()->routeIs('admin.payment.method.*') && !request()->routeIs('admin.manual.payment.')  ? "active" :"" }}" data-bs-toggle="collapse" href="#collapsePayment"
                    role="button" aria-expanded="true" aria-controls="collapsePayment">
                    <span><i class="las la-money-check"></i></span>
                    <p>{{ translate('Payment Gateway')}}  <small><i class="las la-angle-down"></i></small>
                    </p>
                </a>

                <div class="side-menu-dropdown collapse {{request()->routeIs('admin.payment.method.*') || request()->routeIs('admin.manual.payment.*') ? "show" :"" }}"  id="collapsePayment">
                    <ul class="sub-menu">
                        <li class="sub-menu-item">
                            <a class="sidebar-menu-link {{menuActive(['admin.payment.method.*'])}}" href="{{route('admin.payment.method.index')}}">

                                <p>{{ translate('Automatic Payment')}}</p>
                            </a>
                        </li>

                        <li class="sub-menu-item">
                            <a class="sidebar-menu-link {{menuActive('admin.manual.payment.*')}}" href="{{route('admin.manual.payment.index')}}">

                                <p>{{ translate('Manual Payment')}}</p>
                            </a>
                        </li>
                    </ul>
                </div>
            </li> -->
            <li class="sidebar-menu-item">
                <a class="sidebar-menu-link collapsed {{$isGatewayActive ? "active" :"" }}" data-bs-toggle="collapse" href="#collapseGatewaySetting"
                   role="button" aria-expanded="true" aria-controls="collapseGatewaySetting">
                    <span><i class="las la-cogs"></i></span>
                    <p>{{ translate('Gateway Setting')}}  <small><i class="las la-angle-down"></i></small></p>
                </a>

                <div class="side-menu-dropdown collapse {{$isGatewayActive ? "show" :"" }}"  id="collapseGatewaySetting">
                    <ul class="sub-menu">
                        <li class="sub-menu-item">
                            <a class="sidebar-menu-link {{menuActive(['admin.sms.gateway.*', 'admin.gateway.whatsapp.*', 'admin.template.index'])}}" href="{{route('admin.sms.gateway.sms.api')}}">
                                <p>{{ translate('SMS & Whatsapp')}}</p>
                            </a>
                        </li>

                        <li class="sub-menu-item">
                            <a class="sidebar-menu-link {{menuActive(['admin.mail.list', 'admin.mail.edit'])}}" href="{{route('admin.mail.list')}}">
                                <p>{{ translate('Email')}}</p>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            @php
                $contactRouteNames = [
                    'admin.contact.index',
                    'admin.contact.create',
                    'admin.contact.settings.index',
                    'admin.contact.group.index',
                ];
                $isContactActive = request()->routeIs($contactRouteNames);
            @endphp

            <li class="sidebar-menu-item">
                <a class="sidebar-menu-link collapsed {{$isContactActive ? "active" :"" }}" data-bs-toggle="collapse" href="#collapseContact"
                role="button" aria-expanded="true" aria-controls="collapseContact">
                    <span><i class="las la-address-book"></i></span>
                    <p>{{ translate('Contact')}}  <small><i class="las la-angle-down"></i></small>
                    </p>
                </a>

                <div class="side-menu-dropdown collapse {{$isContactActive ? "show" :"" }}" id="collapseContact">
                    <ul class="sub-menu">
                        <li class="sub-menu-item">
                            <a class="sidebar-menu-link {{menuActive(['admin.contact.group.index'])}}" href="{{route('admin.contact.group.index')}}">
                                <p>{{ translate('Groups')}}</p>
                            </a>
                        </li>

                        <li class="sub-menu-item">
                            <a class="sidebar-menu-link {{menuActive(['admin.contact.index', 'admin.contact.create'])}}" href="{{route('admin.contact.index')}}">
                                <p>{{ translate('List')}}</p>
                            </a>
                        </li>

                        <li class="sub-menu-item">
                            <a class="sidebar-menu-link {{menuActive(['admin.contact.settings.index'])}}" href="{{route('admin.contact.settings.index')}}">
                                <p>{{ translate('Settings')}}</p>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="sidebar-menu-title" data-text="{{ translate('SMS, Whatsapp & Email Activities')}}">{{ translate('SMS & WhatsApp & Email Activities')}}</li>

            @php
                $isTemplatesActive = request()->routeIs('admin.template.email.list.user', 'admin.template.email.list.own', 'admin.template.email.list.default', 'admin.template.email.list.global', 'admin.template.user', 'admin.template.own', 'admin.template.email.create', 'admin.template.email.edit', 'admin.mail.templates.edit');
            @endphp

            <!-- <li class="sidebar-menu-item">
                <a class="sidebar-menu-link collapsed {{$isTemplatesActive ? "active" :"" }}" data-bs-toggle="collapse" href="#collapseTemplatesTexting"
                role="button" aria-expanded="true" aria-controls="collapseTemplatesTexting">
                    <span><i class="las la-box"></i></span>
                    <p>{{ translate('Manage Templates')}}@if($sms_template_request > 0 || $mail_template_request > 0) <i class="las la-exclamation sidebar-batch-icon"></i>@endif<small><i class="las la-angle-down"></i></small>
                    </p>
                </a>

                <div class="side-menu-dropdown collapse {{$isTemplatesActive ? "show" :"" }}"  id="collapseTemplatesTexting">
                    <ul class="sub-menu">
                        <li class="sub-menu-item">
                            <a class="sidebar-menu-link {{menuActive('admin.template.own')}}" href="{{route('admin.template.own')}}">
                                <p>{{ translate('SMS')}}</p>
                                @if($sms_template_request > 0)
                                    <span class="badge bg-danger"> {{$sms_template_request}}</span>
                                @endif
                            </a>
                        </li>

                        <li class="sub-menu-item">
                            <a class="sidebar-menu-link {{menuActive(['admin.template.email.list.user', 'admin.template.email.list.own', 'admin.template.email.list.default', 'admin.template.email.list.global', 'admin.template.email.create', 'admin.template.email.edit', 'admin.mail.templates.edit'])}}" href="{{route('admin.template.email.list.own')}}">
                                <p>{{ translate('Email')}}</p>
                                @if($mail_template_request > 0)
                                    <span class="badge bg-danger"> {{$mail_template_request}}</span>
                                @endif
                            </a>
                        </li>
                    </ul>
                </div>
            </li> -->

            <li class="sidebar-menu-item">
                <a class="sidebar-menu-link collapsed {{$isSmsActive ? "active" :"" }}" data-bs-toggle="collapse" href="#collapseSmsTexting"
                   role="button" aria-expanded="true" aria-controls="collapseSmsTexting">
                    <span><i class="las la-sms"></i></span>
                    <p>{{ translate('SMS Message')}}  <small><i class="las la-angle-down"></i></small>
                    </p>
                </a>

                <div class="side-menu-dropdown collapse {{$isSmsActive ? "show" :"" }}"  id="collapseSmsTexting">
                    <ul class="sub-menu">
                        <li class="sub-menu-item">
                            <a class="sidebar-menu-link {{menuActive('admin.sms.create')}}" href="{{route('admin.sms.create')}}">
                                <p>{{ translate('Send Message')}}</p>
                            </a>
                        </li>

                        <li class="sub-menu-item">
                            <a class="sidebar-menu-link {{menuActive(['admin.sms.index', 'admin.sms.search'])}}" href="{{route('admin.sms.index')}}">
                                <p>{{ translate('History')}}</p>
                            </a>
                        </li>

                        <li class="sub-menu-item">
                            <a class="sidebar-menu-link {{menuActive($menuSmsActiveRoute)}}" href="{{route('admin.campaign.sms')}}">
                                <p>{{ translate('Campaign')}}</p>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            @php
                $routeNames = [
                    'admin.whatsapp.create',
                    'admin.whatsapp.search',
                    'admin.campaign.whatsapp',
                    'admin.whatsapp.index',
                ];

                $menuWhatsAppActiveRoute = [
                    'admin.campaign.whatsapp'
                ];

                if (request()->route()->type == 'whatsapp') {
                    $routeNames[4] = 'admin.campaign.create';
                    $routeNames[5] = 'admin.campaign.edit';
                    $menuWhatsAppActiveRoute[1] = 'admin.campaign.create';
                    $menuWhatsAppActiveRoute[2] = 'admin.campaign.edit';
                }

                $isWhatsappActive = request()->routeIs($routeNames);
            @endphp

            <li class="sidebar-menu-item">
                <a class="sidebar-menu-link collapsed {{$isWhatsappActive ? "active" :"" }}" data-bs-toggle="collapse" href="#collapseWhatsappSetting"
                   role="button" aria-expanded="true" aria-controls="collapseWhatsappSetting">
                    <span><i class="lab la-whatsapp"></i></span>
                    <p>{{ translate('WhatsApp Message')}}  <small><i class="las la-angle-down"></i></small>
                    </p>
                </a>

                <div class="side-menu-dropdown collapse {{$isWhatsappActive ? "show" :"" }}"  id="collapseWhatsappSetting">
                    <ul class="sub-menu">
                        <li class="sub-menu-item">
                            <a class="sidebar-menu-link {{menuActive('admin.whatsapp.create')}}" href="{{route('admin.whatsapp.create')}}">
                                <p>{{ translate('Send Message')}}</p>
                            </a>
                        </li>

                        <li class="sub-menu-item">
                            <a class="sidebar-menu-link {{menuActive(['admin.whatsapp.index', 'admin.whatsapp.search'])}}" href="{{route('admin.whatsapp.index')}}">
                                <p>{{ translate('History')}}</p>
                            </a>
                        </li>

                        <li class="sub-menu-item">
                            <a class="sidebar-menu-link {{menuActive($menuWhatsAppActiveRoute)}}" href="{{route('admin.campaign.whatsapp')}}">
                                <p>{{ translate('Campaign')}}</p>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            @php

                $routeNames = [
                    'admin.email.send',
                    'admin.email.search',
                    'admin.campaign.email',
                    'admin.email.index',
                ];

                $menuEmailActiveRoute = [
                    'admin.campaign.email'
                ];

                if (request()->route()->type == 'email') {
                    $routeNames[4] = 'admin.campaign.create';
                    $routeNames[5] = 'admin.campaign.edit';
                    $menuEmailActiveRoute[1] = 'admin.campaign.create';
                    $menuEmailActiveRoute[2] = 'admin.campaign.edit';
                }

                $isEmailActive = request()->routeIs($routeNames);
            @endphp

            <li class="sidebar-menu-item">
                <a class="sidebar-menu-link collapsed {{$isEmailActive ? "active" :"" }}" data-bs-toggle="collapse" href="#collapseEmailTexting"
                   role="button" aria-expanded="true" aria-controls="collapseEmailTexting">
                    <span><i class="las la-sms"></i></span>
                    <p>{{ translate('Mail Message')}}  <small><i class="las la-angle-down"></i></small>
                    </p>
                </a>

                <div class="side-menu-dropdown collapse {{$isEmailActive ? "show" :"" }}"  id="collapseEmailTexting">
                    <ul class="sub-menu">
                        <li class="sub-menu-item">
                            <a class="sidebar-menu-link {{menuActive('admin.email.send')}}" href="{{route('admin.email.send')}}">
                                <p>{{ translate('Send Mail')}}</p>
                            </a>
                        </li>

                        <li class="sub-menu-item">
                            <a class="sidebar-menu-link {{menuActive(['admin.email.index', 'admin.email.search'])}}" href="{{route('admin.email.index')}}">
                                <p>{{ translate('History')}}</p>
                            </a>
                        </li>

                        <li class="sub-menu-item">
                            <a class="sidebar-menu-link {{menuActive($menuEmailActiveRoute)}}" href="{{route('admin.campaign.email')}}">
                                <p>{{ translate('Campaign')}}</p>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- <li class="sidebar-menu-title" data-text="{{ translate('Settings & Frontend')}}">{{ translate('Settings & Frontend')}}</li> -->

            <!-- <li class="sidebar-menu-item">
                <a class="sidebar-menu-link collapsed {{request()->routeIs('admin.general.setting.*')}}" data-bs-toggle="collapse" href="#collapseSetting"
                    role="button" aria-expanded="true" aria-controls="collapseSetting">
                    <span><i class="las la-tools"></i></span>
                    <p>{{ translate('System Settings')}}  <small><i class="las la-angle-down"></i></small>
                    </p>
                </a>

                <div class="side-menu-dropdown collapse {{request()->routeIs('admin.general.setting.*') ? "show" :"" }}"  id="collapseSetting">
                    <ul class="sub-menu">
                        <li class="sub-menu-item">
                            <a class="sidebar-menu-link {{menuActive('admin.general.setting.index')}}" href="{{route('admin.general.setting.index')}}">

                                <p>{{ translate('Setting')}}</p>
                            </a>
                        </li>

                        <li class="sub-menu-item">
                            <a class="sidebar-menu-link {{menuActive('admin.general.setting.webhook.config')}}" href="{{route('admin.general.setting.webhook.config')}}">

                                <p>{{ translate('Webhook Configuration')}}</p>
                            </a>
                        </li>

                        <li class="sub-menu-item">
                            <a class="sidebar-menu-link {{menuActive('admin.general.setting.recaptcha')}}" href="{{route('admin.general.setting.recaptcha')}}">

                                <p>{{ translate('reCaptcha')}}</p>
                            </a>
                        </li>

                        <li class="sub-menu-item">
                            <a class="sidebar-menu-link {{menuActive('admin.general.setting.social.login')}}" href="{{route('admin.general.setting.social.login')}}">

                                <p>{{ translate('Google Login')}}</p>
                            </a>
                        </li>

                        <li class="sub-menu-item">
                            <a class="sidebar-menu-link {{menuActive('admin.general.setting.beefree.plugin')}}" href="{{route('admin.general.setting.beefree.plugin')}}">

                                <p>{{ translate('Bee Plugin')}}</p>
                            </a>
                        </li>

                        <li class="sub-menu-item">
                            <a class="sidebar-menu-link {{menuActive('admin.general.setting.currency.index')}}" href="{{route('admin.general.setting.currency.index')}}">

                                <p>{{ translate('Currencies')}}</p>
                            </a>
                        </li>

                        <li class="sub-menu-item">
                            <a class="sidebar-menu-link {{menuActive('admin.general.setting.frontend.section')}}" href="{{route('admin.general.setting.frontend.section')}}">
                                <p>{{ translate('Login Section')}}</p>
                            </a>
                        </li>
                    </ul>
                </div>
            </li> -->


            @php
                $isCreditLogsActive = request()->routeIs('admin.report.credit.index','admin.report.credit.search','admin.report.whatsapp.index','admin.report.whatsapp.search', 'admin.report.email.credit.index','admin.report.email.credit.search');
            @endphp

            <!-- <li class="sidebar-menu-item">
                <a class="sidebar-menu-link collapsed {{$isCreditLogsActive  ? "active" :""}} " data-bs-toggle="collapse" href="#collapseCreditLogs"
                   role="button" aria-expanded="true" aria-controls="collapseCreditLogs">
                    <span><i class="las la-history"></i></span>
                    <p>{{ translate('Credit Logs')}}  <small><i class="las la-angle-down"></i></small>
                    </p>
                </a>

                <div class="side-menu-dropdown collapse {{$isCreditLogsActive  ? "show" :""}}"  id="collapseCreditLogs">
                    <ul class="sub-menu">
                        <li class="sub-menu-item">
                            <a class="sidebar-menu-link {{menuActive(['admin.report.credit.index','admin.report.credit.search'])}}" href="{{route('admin.report.credit.index')}}">
                                <p>{{ translate('SMS')}}</p>
                            </a>
                        </li>

                        <li class="sub-menu-item">
                            <a class="sidebar-menu-link  {{menuActive(['admin.report.whatsapp.index','admin.report.whatsapp.search'])}}" href="{{route('admin.report.whatsapp.index')}}">
                                <p>{{ translate('WhatsApp')}}</p>
                            </a>
                        </li>


                        <li class="sub-menu-item">
                            <a class="sidebar-menu-link {{menuActive(['admin.report.email.credit.index','admin.report.email.credit.search'])}}" href="{{route('admin.report.email.credit.index')}}">
                                <p>{{ translate('Email')}}</p>
                            </a>
                        </li>
                    </ul>
                </div>
            </li> -->

            @php
                $isReportsActive = request()->routeIs('admin.report.transaction.index','admin.report.transaction.search','admin.report.subscription.index','admin.report.subscription.search','admin.report.subscription.search.date', 'admin.report.payment.index', 'admin.report.payment.detail');
            @endphp

            <!-- <li class="sidebar-menu-item">
                <a class="sidebar-menu-link collapsed {{$isReportsActive ? "active" :"" }}" data-bs-toggle="collapse" href="#collapseRecords"
                    role="button" aria-expanded="true" aria-controls="collapseRecords">
                    <span><i class="las la-bars"></i></span>
                    <p>{{ translate('Activity Records')}}  @if($pending_manual_payment_count > 0) <i class="las la-exclamation sidebar-batch-icon"></i>  @endif<small><i class="las la-angle-down"></i></small>
                    </p>
                </a>

                <div class="side-menu-dropdown collapse {{$isReportsActive ? "show" :"" }}"  id="collapseRecords">
                    <ul class="sub-menu">
                        <li class="sub-menu-item">
                            <a class="sidebar-menu-link {{menuActive(['admin.report.transaction.index','admin.report.transaction.search'])}}" href="{{route('admin.report.transaction.index')}}">
                                <p>{{ translate('Transaction History')}}</p>
                            </a>
                        </li>

                        <li class="sub-menu-item">
                            <a class="sidebar-menu-link {{menuActive(['admin.report.subscription.index','admin.report.subscription.search','admin.report.subscription.search.date'])}}" href="{{route('admin.report.subscription.index')}}">
                                <p>{{ translate('Subscription History')}}</p>
                            </a>
                        </li>

                        <li class="sub-menu-item">
                            <a class="sidebar-menu-link {{menuActive(['admin.report.payment.index', 'admin.report.payment.detail'])}}" href="{{route('admin.report.payment.index')}}">
                                <p>{{ translate('Payment History')}} </p>
                                @if($pending_manual_payment_count > 0)
                                    <span class="badge bg-danger"> {{$pending_manual_payment_count}}</span>
                                @endif

                            </a>
                        </li>
                    </ul>
                </div>
            </li> -->

            <!-- <li class="sidebar-menu-item">
                <a class="sidebar-menu-link collapsed {{request()->routeIs('admin.frontend.sections.*') ?"active" :""}}" data-bs-toggle="collapse" href="#collapseFrontend"
                    role="button" aria-expanded="true" aria-controls="collapseFrontend">
                    <span><i class="las la-globe-americas"></i></span>
                    <p>{{ translate('Frontend Section')}} <small><i class="las la-angle-down"></i></small></p>
                </a>

                <div class="side-menu-dropdown collapse {{request()->routeIs('admin.frontend.sections.*') ? "show" :"" }} "  id="collapseFrontend">
                    <ul class="sub-menu">
                        @php
                            $lastElement =  collect(request()->segments())->last();
                        @endphp
                            @foreach(getFrontendSection(true) as $key => $section)

                            <li class="sub-menu-item">
                                <a class="sidebar-menu-link @if($lastElement == $key) active @endif" href="{{ route('admin.frontend.sections.index',$key) }}">
                                    <p>{{__(\Illuminate\Support\Arr::get($section, 'name',''))}}</p>
                                </a>
                            </li>

                         @endforeach
                    </ul>
                </div>
            </li> -->

            <!-- <li class="sidebar-menu-title" data-text="{{ translate('SUPPORT & OTHERS')}}">{{ translate('SUPPORT & OTHERS')}}</li> -->

            <!-- <li class="sidebar-menu-item">
                <a class="sidebar-menu-link {{menuActive(['admin.language.*'])}}" href="{{route('admin.language.index')}}">
                    <span><i class="las la-language"></i></span>
                    <p>{{ translate('Manage Language')}}</p>
                </a>
            </li> -->

            <li class="sidebar-menu-item">
                <a class="sidebar-menu-link {{menuActive('admin.spam.word.index')}}" href="{{route('admin.spam.word.index')}}">
                    <span><i class="las la-file-word"></i></span>
                    <p>{{ translate('Spam Words')}}</p>
                </a>
            </li>


             <!-- <li class="sidebar-menu-item">
                <a class="sidebar-menu-link collapsed {{request()->routeIs('admin.support.ticket.*') ? "active" :""}} " data-bs-toggle="collapse" href="#collapseTicket"
                    role="button" aria-expanded="true" aria-controls="collapseTicket">
                    <span><i class="las la-ticket-alt"></i></span>
                    <p>{{ translate('Support Tickets')}}
                        @if($running_support_ticket_count > 0 || $replied_support_ticket_count > 0)
                            <i class="las la-exclamation sidebar-batch-icon"></i>
                        @endif
                     <small><i class="las la-angle-down"></i></small>
                    </p>
                </a>

                <div class="side-menu-dropdown collapse {{request()->routeIs('admin.support.*') ? "show" :"" }}"  id="collapseTicket">
                    <ul class="sub-menu">
                        <li class="sub-menu-item">
                            <a class="sidebar-menu-link {{menuActive(['admin.support.ticket.index', 'admin.support.ticket.search', 'admin.support.ticket.details'])}}" href="{{route('admin.support.ticket.index')}}">
                                <p>{{ translate('All Tickets')}}</p>
                            </a>
                        </li>

                        <li class="sub-menu-item">
                            <a class="sidebar-menu-link {{menuActive('admin.support.ticket.running')}}" href="{{route('admin.support.ticket.running')}}">
                                <p>{{ translate('Running Tickets')}}</p>
                                @if($running_support_ticket_count > 0)
                                    <span class="badge bg-danger"> {{$running_support_ticket_count}}</span>
                                @endif
                            </a>
                        </li>

                        <li class="sub-menu-item">
                            <a class="sidebar-menu-link {{menuActive('admin.support.ticket.answered')}}" href="{{route('admin.support.ticket.answered')}}">
                                <p>{{ translate('Answered Tickets')}} </p>
                                @if($answered_support_ticket_count > 0)
                                <span class="badge bg-danger"> {{$answered_support_ticket_count}}</span>
                            @endif
                            </a>
                        </li>

                        <li class="sub-menu-item">
                            <a class="sidebar-menu-link {{menuActive('admin.support.ticket.replied')}}" href="{{route('admin.support.ticket.replied')}}">
                                <p>{{ translate('Replied Tickets')}}</p>
                                @if($replied_support_ticket_count > 0)
                                <span class="badge bg-danger"> {{$replied_support_ticket_count}}</span>
                                @endif
                            </a>
                        </li>

                        <li class="sub-menu-item">
                            <a class="sidebar-menu-link {{menuActive('admin.support.ticket.closed')}}" href="{{route('admin.support.ticket.closed')}}">
                                <p>{{ translate('Closed Tickets')}}</p>
                                @if($closed_support_ticket_count > 0)
                                <span class="badge bg-danger"> {{$closed_support_ticket_count}}</span>
                                @endif
                            </a>
                        </li>

                    </ul>
                </div>
            </li> -->

            <!-- <li class="sidebar-menu-item">
                <a class="sidebar-menu-link {{menuActive('admin.generate.api.key')}}" href="{{route('admin.generate.api.key')}}">
                    <span><i class="las la-key"></i></span>
                    <p>{{ translate('Generate Key')}}</p>
                </a>
            </li> -->

            <!-- <li class="sidebar-menu-item">
                <a class="sidebar-menu-link {{menuActive('api.document')}}" href="{{route('api.document')}}">
                    <span><i class="las la-code"></i></span>
                    <p>{{ translate('API Document')}}</p>
                </a>
            </li> -->
            <!-- <li class="sidebar-menu-item">
                <a class="sidebar-menu-link {{menuActive('admin.general.setting.system.info') }}" href="{{route('admin.general.setting.system.info')}}">
                    <span><i class="las la-microchip"></i></span>
                    <p>{{ translate('System Info')}}</p>
                </a>
            </li> -->
        </ul>
    </div>
</aside>

@push('script-push')
    <script>
        (function(){
            "use strict";
            // Sidebar
            const htmlRoot = document.documentElement;
            const mainContent = document.getElementById('mainContent');
            const sidebar = document.querySelector('.sidebar');
            const sidebarControlBtn = document.querySelector('.sidebar-control-btn');
            const sidebarMenuLink = document.querySelectorAll('.sidebar-menu-link');
            const menuTitle = document.querySelectorAll('.sidebar-menu-title');

            // Create Overlay Div
            const overlay = document.createElement('div');
            overlay.classList.add('overlay');

            function handleSidebarToggle() {
                const currentSidebar = htmlRoot.getAttribute('data-sidebar');
                const newAttributes = currentSidebar === 'sm' ? 'lg' : 'sm';
                htmlRoot.setAttribute('data-sidebar', newAttributes);
                mainContent.classList.toggle('added');
                for (const title of menuTitle) {
                    const dataText = title.getAttribute('data-text');
                    title.innerHTML = newAttributes === 'sm' ? '<i class="las la-ellipsis-h"></i>' : dataText;
                }

                sidebarControlBtn.style.cssText = newAttributes === 'sm' ? 'fill: var(--primary-color)' : 'color: var(--text-primary)';
            }

            function handleOverlayClick() {
                overlay.classList.remove('d-block');
                sidebar.classList.remove('active');
            }

            function handleResize() {
                const windowWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
                if (windowWidth <= 991) {
                    htmlRoot.removeAttribute('data-sidebar');
                    sidebar.parentElement.append(overlay);
                    sidebar.classList.remove('active');
                    overlay.classList.remove('d-block');
                    sidebarControlBtn.addEventListener('click', () => {
                        sidebar.classList.add('active');
                        overlay.classList.add('d-block');
                        overlay.addEventListener('click', handleOverlayClick);
                    });
                } else {
                    htmlRoot.setAttribute('data-sidebar','lg');
                    if (document.querySelector('.overlay')) {
                        document.querySelector('.overlay').remove();
                    }
                    if (sidebar.classList.contains('active')) {
                        sidebar.classList.remove('active');
                    }
                    sidebarControlBtn.addEventListener('click', handleSidebarToggle);
                }
            }

            window.addEventListener('resize', handleResize);
            handleResize();

     // Sidebar Menu dropdown collapse
            const menuCollapse =document.querySelectorAll(".sidebar-menu .collapse")
            if (menuCollapse) {
                var collapses = menuCollapse;
                Array.from(collapses).forEach(function (collapse) {
                    // Init collapses
                    var collapseInstance = new bootstrap.Collapse(collapse, {
                        toggle: false,
                    });

  				// Hide sibling collapses on `show.bs.collapse`
				collapse.addEventListener("show.bs.collapse", function (e) {
					e.stopPropagation();
					var closestCollapse = collapse.parentElement.closest(".collapse");
					if (closestCollapse) {
						var siblingCollapses = closestCollapse.querySelectorAll(".collapse");
						Array.from(siblingCollapses).forEach(function (siblingCollapse) {
							var siblingCollapseInstance = bootstrap.Collapse.getInstance(siblingCollapse);
							if (siblingCollapseInstance === collapseInstance) {
								return;
							}
							siblingCollapseInstance.hide();
						});
					} else {
						var getSiblings = function (elem) {
							// Setup siblings array and get the first sibling
							var siblings = [];
							var sibling = elem.parentNode.firstChild;
							// Loop through each sibling and push to the array
							while (sibling) {
								if (sibling.nodeType === 1 && sibling !== elem) {
									siblings.push(sibling);
								}
								sibling = sibling.nextSibling;
							}
							return siblings;
						};
						var siblings = getSiblings(collapse.parentElement);
						Array.from(siblings).forEach(function (item) {
							if (item.childNodes.length > 2)
								item.firstElementChild.setAttribute("aria-expanded", "false");
							var ids = item.querySelectorAll("*[id]");
							Array.from(ids).forEach(function (item1) {
								item1.classList.remove("show");
								if (item1.childNodes.length > 2) {
									var val = item1.querySelectorAll("ul li a");
									Array.from(val).forEach(function (subitem) {
										if (subitem.hasAttribute("aria-expanded"))
											subitem.setAttribute("aria-expanded", "false");
									});
								}
							});
						});
					}
				});

				// Hide nested collapses on `hide.bs.collapse`
				collapse.addEventListener("hide.bs.collapse", function (e) {
					e.stopPropagation();
					var childCollapses = collapse.querySelectorAll(".collapse");
					Array.from(childCollapses).forEach(function (childCollapse) {
						childCollapseInstance = bootstrap.Collapse.getInstance(childCollapse);
						childCollapseInstance.hide();
					});
				});
                });
            }
            $('#searchMenu').keyup(function() {

			var value = $(this).val().toLowerCase();
			$('.sidebar-menu li').each(function() {

				var local = $(this).text().toLowerCase();

                if(local.indexOf(value)>-1) {

                    $(this).show();
                } else {

                    $(this).hide();
                }
			});
		});
        })();
    </script>
@endpush
