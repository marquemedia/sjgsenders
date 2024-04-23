<div class="col-lg-auto">
    <div class="vertical-tab card sticky-item">
        <div class="nav flex-column nav-pills gap-2" id="v-pills-tab" role="tablist" aria-orientation="vertical">
            <a class="nav-link {{ request()->routeIs('admin.template.own') ? 'active' : ''}}" href="{{ route('admin.template.own') }}">{{ translate('Admin')}}
                <span><i class="las la-angle-right"></i></span>
            </a>
            <a class="nav-link {{ request()->routeIs('admin.template.user') ? 'active' : ''}}" href="{{ route('admin.template.user') }}">{{ translate('User')}}
                <span><i class="las la-angle-right"></i></span>
            </a>
        </div>
    </div>
</div>
