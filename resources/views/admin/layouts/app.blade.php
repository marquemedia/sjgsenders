@extends('admin.layouts.master')
@section('content')
	@include('admin.partials.sidebar')
    <div id="mainContent" class="main_content added">
		@include('admin.partials.topbar')
        <div class="dashboard_container">
        	@yield('panel')
        </div>

        <footer>
            <div class="footer-content">
                <p>{{$general->copyright}} &copy; {{carbon()->format('Y')}} </p>
                <div class="footer-right">
                    <ul>
                        <li><a href="https://support.igensolutionsltd.com">{{translate('Support')}}</a></li>
                    </ul>
                    <span>{{translate('App-Version')}}: {{config('requirements.core.appVersion')}}</span>
                </div>
            </div>
        </footer>
    </div>
@endsection
