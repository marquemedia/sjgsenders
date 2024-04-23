@extends('user.layouts.master')
@section('content')
	@include('user.partials.sidebar')
    <div class="overlay"></div>
    <div id="mainContent" class="main_content added">
		@include('user.partials.topbar')
        <div class="dashboard_container">
        	@yield('panel')
        </div>

        <footer>
            <div class="footer-content">
                <p>{{$general->copyright}} &copy; {{carbon()->format('Y')}} </p>
                <div class="footer-right">
                    <span>{{translate('App-Version')}}: {{config('requirements.core.appVersion')}}</span>
                </div>
            </div>
        </footer>
    </div>
    
@endsection
