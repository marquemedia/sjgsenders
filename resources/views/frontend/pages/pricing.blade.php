@extends('frontend.layouts.main')
@section('content')
<section class="breadcrumb-banner">
    <div class="container">
        <div class="breadcrumb-content text-center">
            <h2>{{translate('Pricing')}}</h2>
            <div class="d-inline-block mt-4">
                <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{translate("Home")}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ translate("Pricing") }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="breadcrumb-bg">
        <img src="{{showImage(filePath()['frontend']['path'].'/'. @getArrayValue(@$plan_content->section_value, 'plan_breadcrumb_image'),'1250x830')}}" alt="{{@getArrayValue(@$plan_content->section_value,'plan_breadcrumb_image')}}">
    </div>
</section>
@include('sections.pricing_plan')
@include('sections.overview')
@include('sections.faq')
@endsection
