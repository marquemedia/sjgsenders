<div class="col-lg-auto">
    <div class="vertical-tab card sticky-item">
        <div class="nav flex-column nav-pills gap-2" id="v-pills-tab" role="tablist" aria-orientation="vertical">
           
            <a class="nav-link {{ request()->routeIs('admin.sms.gateway.sms.api')  ? 'active' : ''}}" href="{{ route('admin.sms.gateway.sms.api') }}" >{{ translate('SMS API Gateway')}}
                <span><i class="las la-angle-right"></i></span>
            </a>
            <a class="nav-link {{ request()->routeIs('admin.sms.gateway.android')  ? 'active' : ''}}" href="{{ route('admin.sms.gateway.android') }}" >{{ translate('Android Gateway')}}
                <span><i class="las la-angle-right"></i></span>
            </a>
            <a class="nav-link {{ request()->routeIs('admin.gateway.whatsapp.device') ? 'active' : ''}}" href="{{ route('admin.gateway.whatsapp.device') }}" > {{ translate('WhatsApp Device')}}
                <span><i class="las la-angle-right"></i></span>
            </a>
        </div>
    </div>
</div>