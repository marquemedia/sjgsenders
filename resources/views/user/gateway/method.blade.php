<div class="col-lg-auto">
    <div class="vertical-tab card sticky-item">
        <div class="nav flex-column nav-pills gap-2" id="v-pills-tab" role="tablist" aria-orientation="vertical">
            <a class="nav-link {{ request()->routeIs('user.sms.gateway.sendmethod.gateway') ? 'active' : ''}}" href="{{ route('user.sms.gateway.sendmethod.gateway') }}">{{ translate('Gateway Method')}}
                <span><i class="las la-angle-right"></i></span>
            </a>
            <a class="nav-link {{ request()->routeIs('user.sms.gateway.sendmethod.api') ? 'active' : ''}}" href="{{ route('user.sms.gateway.sendmethod.api') }}">{{ translate('Api Gateway')}}
                <span><i class="las la-angle-right"></i></span>
            </a>
            <a class="nav-link {{ request()->routeIs('user.gateway.sendmethod.android') ? 'active' : ''}}" href="{{ route('user.gateway.sendmethod.android') }}"> {{ translate('Android Gateway')}}
                <span><i class="las la-angle-right"></i></span>
            </a>
        </div>
    </div>
</div>