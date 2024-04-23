@extends('admin.layouts.app')
@section('panel')
<section>
    <div class="container-fluid p-0">
        <div class="row gy-4">

            @include('admin.sms_gateway.sms_gateway_tab')

            <div class="col">
                <div class="row align-items-center gy-3">
                    <div class="col-4">
                        <h5 class="card-title"> {{ $title}} </h5>
                    </div>
                    <div class="col-8">
                        <div class="file-tab">
                            <div class="d-flex justify-content-end align-items-end">

                                <ul class="nav nav-tabs mb-3 gap-2" id="myTabContent" role="tablist">
                                    <li class="nav-item whatsapp-business-setup" role="presentation">
                                        <button title="Setup Whatsappp with the cloud API features" class="nav-link active" id="whatsapp-with-cloud-api" data-bs-toggle="tab" data-bs-target="#whatsapp-with-cloud-api-pane" type="button" role="tab" aria-controls="whatsapp-with-cloud-api-pane" aria-selected="true"><i class="lab la-whatsapp"></i>{{ translate('Cloud API Setup') }}</button>
                                    </li>
                                    <li class="nav-item whatsapp-whatsapp-without-cloud-api" role="presentation">
                                        <button title="Setup Whatsappp without the cloud API features using Node Module" class="nav-link" id="whatsapp-without-cloud-api" data-bs-toggle="tab" data-bs-target="#whatsapp-without-cloud-api-pane" type="button" role="tab" aria-controls="whatsapp-without-cloud-api-pane" aria-selected="false"><i class="las la-microchip"></i> {{ translate('Node Module') }}</button>
                                    </li>
                                </ul>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-content" id="myTabContent">

                    <div class="tab-pane fade show active" id="whatsapp-with-cloud-api-pane" role="tabpanel" aria-labelledby="whatsapp-with-cloud-api" tabindex="0">
                        <div class="form-item">
                            <div>
                                <form action="{{route('admin.gateway.whatsapp.store')}}" method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <input type="text" name="whatsapp_business_api" value="true" hidden>

                                    <div class="card mb-3">
                                        <div class="card-header">
                                            {{ translate('Whatsapp Cloud API Setup')}}
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-12 mb-4">
                                                    <label for="name">{{ translate('Business Portfolio Name')}} <span class="text-danger">*</span></label>
                                                    <input type="text" class="mt-2 form-control @error('name') is-invalid @enderror" name="name" id="name" value="{{old('name')}}" placeholder="{{ translate('Add a name for your Business Portfolio')}}">
                                                    @error('name')
                                                        <span class="text-danger">{{$message}}</span>
                                                    @enderror
                                                </div>
                                                @foreach($credentials["required"] as $creds_key => $creds_value)
                                                    <div class="{{ $loop->last ? 'col-12' : 'col-md-6' }} mb-4">
                                                        <label for="{{ $creds_key }}">{{translate(textFormat(['_'], $creds_key))}} <span class="text-danger">*</span></label>
                                                        <input type="text" class="mt-2 form-control" name="credentials[{{$creds_key}}]" value="{{old($creds_key)}}" placeholder="Enter the {{translate(textFormat(['_'], $creds_key))}}">
                                                    </div>
                                                @endforeach
                                               <sup class="mb-3">{{ translate("Now to set up your webhook please click here to collect credentials: ")}}<a class="fw-bold text-dark text-decoration-underline " target="_blank" href="{{ route('admin.general.setting.webhook.config') }}">{{ translate("Webhook Configuration") }} <i class="fa-solid fa-arrow-up-right-from-square"></i></a></sup>
                                            </div>
                                            <button type="submit" class="i-btn primary--btn btn--md">{{ translate('Submit')}}</button>
                                        </div>
                                    </div>
                                </form>
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">
                                            {{translate('WhatsApp Business Account List')}}
                                        </h4>

                                    </div>
                                    <div class="card-body px-0">
                                        <div class="responsive-table">
                                            <table>
                                                <thead>
                                                <tr>
                                                    <th>{{ translate('Session Name')}}</th>
                                                    <th>{{ translate('Templates')}}</th>
                                                    <th>{{ translate('Action')}}</th>
                                                </tr>
                                                </thead>
                                                @forelse ($whatsappBusinesses as $item)
                                                    <tbody>
                                                        <tr>
                                                            
                                                            <td data-label="{{translate('Session Name')}}">{{$item->name}}</td>
                                                            <td data-label="{{translate('Templates')}}">
                                                                <a href="{{route('admin.template.index', ['type' => 'whatsapp', 'id' => $item->id])}}" class="badge badge--primary p-2"> {{ translate('view templates ')}} ({{count($item->template)}})</a>
                                                            </td>
                                                            <td data-label="{{translate('Action')}}">
                                                                <div class="d-flex align-items-center justify-content-md-start justify-content-end gap-3">
                                                                    <a title="Edit" href="javascript:void(0)" class="i-btn primary--btn btn--sm whatsappBusinessApiEdit"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#whatsappBusinessApiEdit"
                                                                    data-id="{{$item->id}}"
                                                                    data-name="{{$item->name}}"
                                                                    data-credentials="{{json_encode($item->credentials)}}"><i class="las la-pen"></i>{{translate('Edit')}}</a>

                                                                    <a title="Sync Templates" href="" class="i-btn success--btn btn--sm sync" value="{{$item->id}}"><i class="fa-solid fa-rotate"></i>{{translate('Sync Templates')}}</a>
                                                                    <a title="Delete" href="" class="i-btn danger--btn btn--sm whatsappDelete" value="{{$item->id}}"><i class="fas fa-trash-alt"></i>{{translate('Trash')}}</a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                @empty
                                                    <tbody>
                                                        <tr>
                                                            <td colspan="5" class="text-center py-4"><span class="text-danger fw-medium">{{ translate('No data Available')}}</span></td>
                                                        </tr>
                                                    </tbody>
                                                @endforelse
                                            </table>
                                        </div>
                                        <div class="m-3">
                                            {{$whatsappBusinesses->appends(request()->all())->onEachSide(1)->links()}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="whatsapp-without-cloud-api-pane" role="tabpanel" aria-labelledby="whatsapp-without-cloud-api-tab" tabindex="0">
                        <div class="form-item">
                            @if($checkWhatsAppServer)
                                <div>
                                    <form action="{{route('admin.gateway.whatsapp.store')}}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="text" name="whatsapp_node_module" value="true" hidden>
                                        <div class="card mb-3">
                                            <div class="card-header ">
                                                {{ translate('Whatsapp Node Module Setup')}}
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-12 mb-4">
                                                        <label for="name">{{ translate('Session/Device Name')}} <span  class="text-danger">*</span>  </label>
                                                        <input type="text" class="mt-2 form-control @error('name') is-invalid @enderror " name="name" id="name" value="{{old('name')}}" placeholder="{{ translate('Put Session Name (Any)')}}">
                                                        @error('name')
                                                            <span class="text-danger">{{$message}}</span>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-6 mb-4">
                                                        <label for="min_delay">{{ translate('Message Minimum Delay Time')}}
                                                            <span class="text-danger" >*</span>
                                                        </label>
                                                        <input type="number" class="mt-2 form-control" name="min_delay" id="min_delay" value="{{old('min_delay')}}" placeholder="{{ translate('Message minimum delay time in seconds')}}">
                                                    </div>
                                                    <div class="col-md-6 mb-4">
                                                        <label for="max_delay">{{ translate('Message Maximum Delay Time')}}
                                                            <span class="text-danger" >*</span>
                                                        </label>
                                                        <input type="number" class="mt-2 form-control" name="max_delay" id="max_delay" value="{{old('max_delay')}}" placeholder="{{ translate('Message maximum delay time in second')}}">
                                                    </div>
                                                </div>
                                                <button type="submit" class="i-btn primary--btn btn--md">{{ translate('Submit')}}</button>
                                            </div>
                                        </div>
                                    </form>

                                    <div class="card">
                                        <div class="card-header">
                                            <h4 class="card-title">
                                                {{translate('WhatsApp Device List')}}
                                            </h4>
                                            <div class="d-flex align-items-center flex-wrap gap-3">
                                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#whatsappServerSetting" class="i-btn success--btn btn--md"><i class="las la-key"></i> {{translate('Server Settings')}}</a>
                                            </div>
                                        </div>
                                        <div class="card-body px-0">
                                            <div class="responsive-table">
                                                <table>
                                                    <thead>
                                                    <tr>
                                                        <th>{{ translate('Session Name')}}</th>
                                                        <th>{{ translate('WhatsApp Number')}}</th>
                                                        <th>{{ translate('Minimum Delay')}}</th>
                                                        <th>{{ translate('Maximum Delay')}}</th>
                                                        <th>{{ translate('Status')}}</th>
                                                        <th>{{ translate('Action')}}</th>
                                                    </tr>
                                                    </thead>
                                                    @forelse ($whatsappNodes as $item)

                                                        <tbody>
                                                        <tr>
                                                            <td data-label="{{translate('Session Name')}}">{{$item->name}}</td>
                                                            <td data-label="{{translate('WhatsApp Number')}}" >{{array_key_exists("number", $item->credentials) && $item->credentials["number"] != " "? $item->credentials["number"] : 'N/A'}}</td>
                                                            <td data-label="{{translate('Time Delay')}}" >{{array_key_exists("min_delay", $item->credentials) ? convertTime($item->credentials["min_delay"]) : "N/A"}}</td>
                                                            <td data-label="{{translate('Time Delay')}}" >{{array_key_exists("max_delay", $item->credentials) ? convertTime($item->credentials["max_delay"]) : "N/A"}}</td>
                                                            <td data-label="{{translate('Status')}}" >
                                                                <div class="d-flex align-items-center justify-content-md-start justify-content-end gap-3">
                                                                    <span class="badge badge--{{$item->status == 'initiate' ? 'primary' : ($item->status == 'connected' ? 'success' : 'danger')}}">
                                                                        {{ucwords($item->status)}}
                                                                    </span>
                                                                </div>

                                                            </td>
                                                            <td>
                                                                <div class="d-flex align-items-center justify-content-md-start justify-content-end gap-3">

                                                                    <a title="Edit" href="javascript:void(0)" class="i-btn primary--btn btn--sm whatsappEdit"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#whatsappEdit"
                                                                    data-id="{{$item->id}}"
                                                                    data-name="{{$item->name}}"
                                                                    data-min_delay="{{$item->credentials["min_delay"]}}"
                                                                    data-max_delay="{{$item->credentials["max_delay"]}}"><i class="las la-pen"></i>{{translate('Edit')}}</a>
                                                                    @if($item->status == 'initiate')
                                                                    <a title="Scan" href="javascript:void(0)" id="textChange" class="i-btn success--btn btn--sm qrQuote textChange{{$item->id}}" value="{{$item->id}}"><i class="fas fa-qrcode"></i>{{ translate('Scan')}}</a>
                                                                    @elseif($item->status == 'connected')
                                                                        <a title="Disconnect" href="javascript:void(0)" onclick="return deviceStatusUpdate('{{$item->id}}','disconnected','deviceDisconnection','Disconnecting','Connect')" class="i-btn warning--btn btn--sm deviceDisconnection{{$item->id}}" value="{{$item->id}}"><i class="fas fa-plug"></i>{{ translate('Disconnect')}}</a>
                                                                    @else
                                                                        <a title="Scan" href="javascript:void(0)" id="textChange" class="i-btn success--btn btn--sm qrQuote textChange{{$item->id}}" value="{{$item->id}}"><i class="fas fa-qrcode"></i>{{translate('Scan')}}</a>
                                                                    @endif

                                                                    <a title="Delete" href="" class="i-btn danger--btn btn--sm whatsappDelete" value="{{$item->id}}"><i class="fas fa-trash-alt"></i>{{translate('Trash')}}</a>

                                                                </div>
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    @empty
                                                        <tbody>
                                                        <tr>
                                                            <td colspan="50"><span class="text-danger">{{ translate('No data Available')}}</span></td>
                                                        </tr>
                                                        </tbody>
                                                    @endforelse
                                                </table>
                                            </div>
                                            <div class="m-3">
                                                {{$whatsappNodes->appends(request()->all())->onEachSide(1)->links()}}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal fade" id="qrQuoteModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="staticBackdropLabel">{{ translate('Scan Device')}}</h5>
                                                <button type="button" class="btn-close" aria-label="Close" onclick="return deviceStatusUpdate('','initiate','','','')"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="scan_id" id="scan_id" value="">
                                                <div>
                                                    <h4 class="py-3">{{ translate('To use WhatsApp')}}</h4>
                                                    <ul>
                                                        <li>{{ translate('1. Open WhatsApp on your phone')}}</li>
                                                        <li>{{ translate('2. Tap Menu  or Settings  and select Linked Devices')}}</li>
                                                        <li>{{ translate('3. Point your phone to this screen to capture the code')}}</li>
                                                    </ul>
                                                </div>
                                                <div class="text-center">
                                                    <img id="qrcode" class="w-50" src="" alt="">
                                                </div>
                                                <div class="text-center">
                                                    <small><a href="https://faq.whatsapp.com/1317564962315842/?cms_platform=web&lang=en" target="_blank"><i class="fas fa-info"></i>{{translate('More Guide')}}</a></small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title"> {{ translate('WhatsApp Device List')}} </h4>

                                        <div class="header-with-btn">
                                            <span class="d-flex align-items-center gap-2">
                                                <span>{{ translate('Error Notice ')}}</span>
                                                <a href="" class="badge badge--primary"> <i class="fas fa-refresh"></i>  {{ translate('Try Again') }}</a>
                                                <a href="https://support.igensolutionsltd.com/help-center/categories/2/xsender" target="_blank" class="badge badge--success"> <i class="fas fa-info"></i>  {{ translate('Setup Guide') }}</a>
                                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#whatsappServerSetting" class="i-btn success--btn btn--md"><i class="las la-key"></i> {{translate('Server Settings')}}</a>
                                            </span>
                                        </div>

                                    </div>

                                    <div class="card-body">
                                        <h6 class="text--danger">{{ translate('Unable to connect to WhatsApp node server. Please configure the server settings and try again.') }}</h6>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="whatsappDelete" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{route('admin.gateway.whatsapp.delete')}}" method="POST">
                @csrf
                <input type="hidden" name="id" value="">
                <div class="modal_body2">
                    <div class="modal_icon2">
                        <i class="las la-trash"></i>
                    </div>
                    <div class="modal_text2 mt-3">
                        <h6>{{ translate('Are you sure to delete')}}</h6>
                    </div>
                </div>
                <div class="modal_button2 modal-footer">
                    <div class="d-flex align-items-center justify-content-center gap-3">
                        <button type="button" class="i-btn primary--btn btn--md" data-bs-dismiss="modal">{{ translate('Cancel')}}</button>
                        <button type="submit" class="i-btn danger--btn btn--md">{{ translate('Delete')}}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="whatsappBusinessApiEdit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ translate('Update Whatsapp Business API')}}</h5>
                 <button type="button" class="i-btn bg--lite--danger text--danger btn--sm" data-bs-dismiss="modal"> <i class="las la-times"></i></button>
            </div>
            <form action="{{route('admin.gateway.whatsapp.update')}}" method="POST">
                @csrf
                <input type="text" name="whatsapp_business_api" value="true" hidden>
                <input type="hidden" name="id">
                <div class="modal-body">
                        <div class="row gx-4 gy-3">

                            <div class="col-lg-12">
                                <label for="name" class="form-label">{{ translate('Business API Name')}} <sup class="text--danger">*</sup></label>
                                <div class="input-group">
                                      <input type="text" class="form-control" id="name" name="name" placeholder="{{ translate('Update Name')}}">
                                </div>
                            </div>
                            <div id="edit_cred">

                            </div>
                            
                        </div>
                </div>

                <div class="modal-footer">
                    <div class="d-flex align-items-center gap-3">
                        <button type="button" class="i-btn danger--btn btn--md" data-bs-dismiss="modal">{{ translate('Cancel')}}</button>
                        <button type="submit" class="i-btn primary--btn btn--md">{{ translate('Submit')}}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="whatsappEdit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ translate('Update Whatsapp Gateway')}}</h5>
                 <button type="button" class="i-btn bg--lite--danger text--danger btn--sm" data-bs-dismiss="modal"> <i class="las la-times"></i></button>
            </div>
            <form action="{{route('admin.gateway.whatsapp.update')}}" method="POST">
                @csrf
                <input type="text" name="whatsapp_node_module" value="true" hidden>
                <input type="hidden" name="id">
                <div class="modal-body">
                        <div class="row gx-4 gy-3">

                            <div class="col-lg-12">
                                <label for="min_delay" class="form-label">{{ translate('Minimum Delay Time')}} <sup class="text--danger">*</sup></label>
                                <div class="input-group">
                                      <input type="text" class="form-control" id="min_delay" name="min_delay" placeholder="{{ translate('Enter Minimum Delay Time')}}">
                                </div>

                            </div>
                            <div class="col-lg-12">
                                <label for="max_delay" class="form-label">{{ translate('Maximum Delay Time')}} <sup class="text--danger">*</sup></label>
                                <div class="input-group">
                                      <input type="text" class="form-control" id="max_delay" name="max_delay" placeholder="{{ translate('Enter maximum Delay Time')}}">
                                </div>

                            </div>
                        </div>
                </div>

                <div class="modal-footer">
                    <div class="d-flex align-items-center gap-3">
                        <button type="button" class="i-btn danger--btn btn--md" data-bs-dismiss="modal">{{ translate('Cancel')}}</button>
                        <button type="submit" class="i-btn primary--btn btn--md">{{ translate('Submit')}}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

 {{-- Whatsapp server setting update --}}
<div class="modal fade" id="whatsappServerSetting" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{route('admin.gateway.whatsapp.server.update')}}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="card">
                        <div class="card-header bg--lite--violet">
                            <div class="card-title ">{{ translate('Update Whatsapp Server Settings')}}</div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                            <div class="col-lg-12 mb-3">
                                <label for="server_url" class="form-label">{{translate('Current Whatsapp Server URL')}}</label>
                                <input type="text" class="form-control" id="server_url" placeholder="{{ translate('Enter Whatsapp Server URL')}}" value="{{ env('WP_SERVER_URL') }}" readonly="true">
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label for="server_host" class="form-label">{{translate('Whatsapp Server Host')}} <sup class="text--danger">*</sup></label>
                                <input type="text" class="form-control" id="server_host" name="server_host" placeholder="{{ translate('Enter Whatsapp Server Host')}}" value="{{ env('NODE_SERVER_HOST') }}" required>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label for="server_port" class="form-label">{{translate('Whatsapp Server Port')}} <sup class="text--danger">*</sup></label>
                                <input type="number" class="form-control" id="server_port" name="server_port" placeholder="{{ translate('Enter Whatsapp Server Port')}}" value="{{ env('NODE_SERVER_PORT') }}" required>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label for="max_retries" class="form-label">{{translate('Maximum Retries')}} <sup class="text--danger">*</sup></label>
                                <input type="number" class="form-control" id="max_retries" name="max_retries" placeholder="{{ translate('Enter The Maximum Amount of Retries')}}" value="{{ env('MAX_RETRIES') }}" required>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label for="reconnect_interval" class="form-label">{{ translate('Reconnect Interval')}} <sup class="text--danger">*</sup></label>
                                <input type="number" class="form-control" id="reconnect_interval" name="reconnect_interval" placeholder="{{ translate('Enter Reconnect Interval Duration')}}" value="{{ env('RECONNECT_INTERVAL') }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal_button2 modal-footer">
                    <div class="d-flex align-items-center justify-content-center gap-3">
                        <button type="button" class="i-btn primary--btn btn--md" data-bs-dismiss="modal">{{ translate('Cancel')}}</button>
                        <button type="submit" class="i-btn success--btn btn--md">{{ translate('Submit')}}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('script-push')
<script>
	(function($){
		"use strict";

        // qrQuote scan
        $(document).on('click', '.qrQuote', function(e){
            e.preventDefault()
            var id = $(this).attr('value')
            var url = "{{route('admin.gateway.whatsapp.qrcode')}}"
            $.ajax({
                headers: {'X-CSRF-TOKEN': "{{csrf_token()}}"},
                url:url,
                data: {id:id},
                dataType: 'json',
                method: 'post',
                beforeSend: function(){
                    $('.textChange'+id).html(`<i class="fas fa-refresh"></i>&nbsp{{ translate('Loading...')}}`);
                },
                success: function(res){
                    $("#scan_id").val(res.response.id);
                    if (res.data.message && res.data.qr && res.data.status===200) {
                        $('#qrcode').attr('src', res.data.qr);
                        notify('success', res.data.message);
                        $('#qrQuoteModal').modal('show');
                        sleep(10000).then(() => {
                            wapSession(res.response.id);
                        });
                    } else if (res.data.message) {
                        notify('error', res.data.message);
                    }
                },
                complete: function(){
                    $('.textChange'+id).html(`<i class="fas fa-qrcode"></i>&nbsp {{ translate('Scan')}}`);
                },
                error: function(e) {
                    notify('error','Something went wrong')
                }
            });
        });


    })(jQuery);
    function wapSession(id) {
            $.ajax({
                headers: {'X-CSRF-TOKEN': "{{csrf_token()}}"},
                url:"{{route('admin.gateway.whatsapp.device.status')}}",
                data: {id:id},
                dataType: 'json',
                method: 'post',
                success: function(res){
                    $("#scan_id").val(res.response.id);
                    if (res.data.qr!=='')
                    {
                        $('#qrcode').attr('src',res.data.qr);
                    }

                    if (res.data.status===301)
                    {
                        sleep(2500).then(() => {
                            $('#qrQuoteModal').modal('hide');
                            location.reload();
                        });
                    }else{
                        sleep(10000).then(() => {
                            wapSession(res.response.id);
                        });
                    }
                }
            })
        }

        function deviceStatusUpdate(id,status,className='',beforeSend='',afterSend='') {
            if (id=='') {
                id = $("#scan_id").val();
            }
            $('#qrQuoteModal').modal('hide');
            $.ajax({
                headers: {'X-CSRF-TOKEN': "{{csrf_token()}}"},
                url:"{{route('admin.gateway.whatsapp.status-update')}}",
                data: {id:id,status:status},
                dataType: 'json',
                method: 'post',
                beforeSend: function(){
                    if (beforeSend!='') {
                        $('.'+className+id).html(beforeSend);
                    }
                },
                success: function(res){
                    sleep(1000).then(()=>{
                        location.reload();
                    })
                },
                complete: function(){
                    if (afterSend!='') {
                        $('.'+className+id).html(afterSend);
                    }
                }
            })
        }

        function textFormat(symbols, data, replaceWith) {

            symbols = symbols || null;
            replaceWith = replaceWith || ' ';

            var convertedString = data.replace(new RegExp(symbols.join('|'), 'g'), replaceWith).toLowerCase().replace(/(?:^|\s)\S/g, function(a) {
                return a.toUpperCase();
            });

            return convertedString;
        }

        $(document).ready(function() {
            $('.sync').click(function(e) {
                e.preventDefault();
                var itemId = $(this).attr('value'); 
                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                var a = $(this);
                a.addClass('disabled').append('<span class="loading-spinner spinner-border spinner-border-sm" aria-hidden="true"></span> ');
                $.ajax({
                    url: '{{ route("admin.template.whatsapp.refresh") }}',
                    type: 'GET', 
                    data: {
                        itemId: itemId 
                    },
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken 
                    },
                    success: function(response) {
                        a.find('.loading-spinner').remove();
                        a.removeClass('disabled');
                        
                     
                        if(response.status && response.reload){
                            location.reload(true);
                            notify('success', "Successfully synced Templates");
                        } else {
                            notify('error', "Could Not Sync Templates");
                        }
                    },
                    error: function(xhr, status, error) {
                        a.find('.loading-spinner').remove();
                        notify('error', "Some error occured");
                    }
                });
            });
        });
    </script>
@endpush
