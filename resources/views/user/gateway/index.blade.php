@extends('user.layouts.app')
@section('panel')
<section>
    <div class="container-fluid p-0">
        <div class="row gy-4">
            @include('user.gateway.method')
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title"> {{ translate('SMS Gateway List')}}</h4>
                        
                    </div>

                    <div class="card-body px-0">
                        <div class="responsive-table">
                            <table>
                                <thead>
                                    <tr>
                                        <th>{{ translate('Gateway Name')}}</th>
                                        <th>{{ translate('Gateway Type')}}</th>
                                        @if($allowed_access->type == App\Models\PricingPlan::USER)<th>{{ translate('Default')}}</th>@endif
                                        <th>{{ translate('Status')}}</th>
                                        @if($allowed_access->type == App\Models\PricingPlan::USER)<th>{{ translate('Action')}}</th>@endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($smsGateways as $smsGateway)
                                    
                                        <tr class="@if($loop->even)@endif">
                                            <td data-label="{{ translate('Gateway Name')}}"><span class="text-dark">{{ucfirst($smsGateway->name)}}</span></td>
                                            <td data-label="{{ translate('Gateway Type')}}"><span class="text-dark">{{preg_replace('/[[:digit:]]/','', setInputLabel($smsGateway->type))}}</span></td>
                                            @if($allowed_access->type == App\Models\PricingPlan::USER)
                                                <td class="text-center" data-label="{{ translate('Default')}}">
                                                    <div class="d-flex justify-content-md-start justify-content-end">
                                                        @if($smsGateway?->is_default == 1)
                                                            <i class="las la-check-double text--success" style="font-size:32px"></i>
                                                        @else
                                                            <label class="switch">
                                                                <input type="checkbox" class="default_status" data-id="{{$smsGateway->id}}" value="1" name="default_value" id="default_gateway">
                                                                <span class="slider"></span>
                                                            </label>
                                                        @endif
                                                    </div>
                                                </td>
                                            @endif
                                            <td data-label="{{ translate('Status')}}">
                                                @if($smsGateway->status == 1)
                                                    <span class="badge badge--success">{{ translate('Active')}}</span>
                                                @else
                                                    <span class="badge badge--danger">{{ translate('Inactive')}}</span>
                                                @endif
                                            </td>
                                            @if($allowed_access->type == App\Models\PricingPlan::USER)
                                                <td data-label="{{ translate('Action')}}">
                                                    <div class="d-flex align-items-center justify-content-md-start justify-content-end gap-3">
                                                        <a class="i-btn info--btn btn--sm gateway-details"
                                                            data-sms_credentials="{{json_encode($smsGateway->sms_gateways)}}"
                                                            data-bs-placement="top" title="Gateway Information"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#gatewayInfo">
                                                            <i class="las la-info-circle"></i>
                                                        </a>    
                                                        <a href="javascript:void(0)" class="i-btn success--btn btn--sm edit-gateway" 
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editgateway"
                                                            data-id="{{$smsGateway?->id}}"
                                                            data-gateway_type="{{$smsGateway?->type}}"
                                                            data-gateway_name="{{$smsGateway?->name}}"
                                                            data-gateway_credentials="{{json_encode($smsGateway?->sms_gateways)}}"
                                                            data-gateway_status="{{$smsGateway?->status}}">
                                                            <i class="las la-pen"></i>
                                                        </a>
                    
                                                        <a href="javascript:void(0)" class="i-btn danger--btn btn--sm delete"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#gatewayDelete"
                                                            data-delete_id="{{$smsGateway->id}}"
                                                            ><i class="las la-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            @endif
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-muted text-center" colspan="100%">{{ translate('No Data Found')}}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="m-3">
                            {{$smsGateways->appends(request()->all())->onEachSide(1)->links()}}
                        </div>
                    </div>
                </div>
                
                @if($allowed_access->type == App\Models\PricingPlan::USER)
                    <a href="javascript:void(0);" class="support-ticket-float-btn" data-bs-toggle="modal" data-bs-target="#addgateway" title=" {{ translate('Add New Gateway')}}">
                        <i class="fa fa-plus ticket-float"></i>
                    </a>
                @endif
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="gatewayInfo" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg nafiz">
        <div class="modal-content">
            <div class="modal-body">
                <div class="card">
                    <div class="card-header bg--lite--violet">
                        <div class="card-title text-center text--light">{{ translate('Gateway Information')}}</div>
                    </div>
                    <div class="card-body">
                        <div class="driver-info"></div>
                    </div>
                </div>
            </div>

            <div class="modal_button2 modal-footer">
                <div class="d-flex align-items-center justify-content-center gap-3">
                    <button type="button" class="i-btn primary--btn btn--md" data-bs-dismiss="modal">{{ translate('Close')}}</button>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="gatewayDelete" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        	<form action="{{route('user.sms.gateway.delete')}}" method="GET">
        		@csrf
        		<input type="hidden" name="id" value="">
	            <div class="modal_body2">
	                <div class="modal_icon2">
	                    <i class="las la-trash"></i>
	                </div>
	                <div class="modal_text2 mt-4">
	                    <h5>{{ translate('Are you sure to delete this gateway')}}</h5>
	                </div>
	            </div>

				<div class="modal_button2 modal-footer">
					<div class="d-flex align-items-center justify-content-center gap-3">
						<button type="button" class="i-btn danger--btn btn--md" data-bs-dismiss="modal">{{ translate('Cancel')}}</button>
						<button type="submit" class="i-btn primary--btn btn--md">{{ translate('Delete')}}</button>
					</div>
				</div>
	        </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editgateway" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ translate('Edit SMS Gateway')}}</h5>
                <button type="button" class="i-btn bg--lite--danger text--danger btn--sm" data-bs-dismiss="modal"> <i class="las la-times"></i></button>
            </div>

            <form action="{{ route('user.sms.gateway.update')}}" method="post">
                @csrf
                <input type="hidden" name="id" value="">
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-item mb-3">
                                <label for="name" class="form-label"> {{ translate('From Name')}} <sup class="text--danger">*</sup></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder=" {{ translate('Enter From Name')}}" required>
                            </div>

                            <div class="form-item mb-3">
                                <label for="gateway_type_edit" class="form-label"> {{ translate('Gateway Type')}} <sup class="text--danger">*</sup></label>
                                <select class="form-select text-uppercase select-gateway-type gateway_type" name="type" required="" id="gateway_type_edit"></select> 
                            </div>
                            
                            <div class="row mb-3 newdataadd"></div>
                            <div class="row mb-3 oldData"></div>

                            <div class="form-item mb-3">
                                <label for="status" class="form-label"> {{ translate('Status')}} <sup class="text--danger">*</sup></label>
                                <select class="form-select" name="status" id="status" required>
                                    <option class="active" value="1"> {{ translate('Active')}}</option>
                                    <option class="inactive" value="0"> {{ translate('Inactive')}}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <div class="d-flex align-items-center gap-3">
                        <button type="button" class="i-btn danger--btn btn--md" data-bs-dismiss="modal"> {{ translate('Cancel')}}</button>
                        <button type="submit" class="i-btn primary--btn btn--md"> {{ translate('Submit')}}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@if($allowed_access->type == App\Models\PricingPlan::USER)
    <div class="modal fade" id="addgateway" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ translate('Add SMS Gateway')}}</h5>
                    <button type="button" class="i-btn bg--lite--danger text--danger btn--sm" data-bs-dismiss="modal"> <i class="las la-times"></i></button>
                </div>

                <form action="{{route('user.sms.gateway.create')}}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-body">
                                
                                <div class="form-item mb-3">
                                    <label for="name" class="form-label"> {{ translate('Gateway Name')}} <sup class="text--danger">*</sup></label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder=" {{ translate('Gateway Name')}}" required>
                                </div>
                            
                                <div class="form-item mb-3">
                                    <label for="add_gateway_type" class="form-label"> {{ translate('Gateway Type')}} <sup class="text--danger">*</sup></label>
                                    
                                    <select class="form-select gateway_type" name="type" required="" id="add_gateway_type">
                                        <option value=""selected disabled>{{ translate("Select a gateway type") }}</option>
                                        
                                            @foreach($credentials as $credential)
                                            @if($user->runningSubscription()->currentPlan()->sms?->allowed_gateways !=null )
                                                @foreach($user->runningSubscription()->currentPlan()->sms->allowed_gateways as $key => $value)
                                                    @php $remaining = isset($gatewayCount[$credential->gateway_code]) ? $value - $gatewayCount[$credential->gateway_code] : $value; @endphp
                                                    @if(preg_replace('/_/','',$key) == preg_replace('/ /','',strtoupper($credential->name)) && $remaining > 0)
                                                        <option value="{{$credential->gateway_code}}">{{strtoupper($credential->name)}} ({{translate("Remaining Gateway: ").$remaining}})</option>
                                                    @endif
                                                @endforeach
                                            @endif
                                            @endforeach
                                    </select>
                                </div>
                            
                                <div class="row mb-3 newdataadd"></div>
                            
                        
                            
                                <div class="form-item mb-3">
                                    <label for="status" class="form-label"> {{ translate('Status')}} <sup class="text--danger">*</sup></label>
                                    <select class="form-select" name="status" id="status" required>
                                        <option value="1"> {{ translate('Active')}}</option>
                                        <option value="0"> {{ translate('Inactive')}}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="d-flex align-items-center gap-3">
                            <button type="button" class="i-btn danger--btn btn--md" data-bs-dismiss="modal"> {{ translate('Cancel')}}</button>
                            <button type="submit" class="i-btn primary--btn btn--md"> {{ translate('Submit')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
@endsection


@push('script-push')
    <script>
        (function($){
            "use strict";

            var oldType = '';
            var oldInfo = [];

            $('.gateway-details').on('click', function(){
                $('.driver-info').empty();
                var modal = $('#gatewayInfo');
                var driver = $(this).data('sms_credentials');
                $.each(driver, function(key, value) {
                  var paragraph = $('<p class="d-flex justify-content-start align-items-center "><span class="fw-bold text-capitalize col-4">' + key + ' </span> <span class="col-8">: ' + value + ' </span></p>');
                  $('.driver-info').append(paragraph);
              });
                modal.modal('show');
            });

            $('.delete').on('click', function(){

                var modal = $('#gatewayDelete');
                modal.find('input[name=id]').val($(this).data('delete_id'));
                modal.modal('show');
            });
          
           
            $('.edit-gateway').on('click', function() {

                $('.newdataadd').empty();
                $('.oldData').empty();
                $('.select-gateway-type').empty();
                $('.active').attr("selected",false);
                $('.inactive').attr("selected",false);
                $('.gatewayType').attr("selected",false);

                var modal = $('#editgateway');
                modal.find('input[name=id]').val($(this).data('id'));
                modal.find('input[name=name]').val($(this).data('gateway_name'));
                modal.find('#gateway_type_edit').append(`<option class="text-uppercase gatewayType" value="${$(this).data('gateway_type')}" selected>${$(this).data('gateway_type').replace($(this).data('gateway_type').match(/(\d+)/g)[0], '').trim()}</option>`);
                var previousType = $(this).data('gateway_type');
                $(this).data('gateway_status') == 1 ? $('.active').attr("selected",true) : $('.inactive').attr("selected",true);


                var data = <?php echo $credentials ?>;
                oldType = $(this).data('gateway_type');

                var user = <?php echo json_encode(@$user->runningSubscription()->currentPlan()->sms->allowed_gateways ?? []) ?>;
                $.each(data, function(key, value) {

                    $.each(user, function(u_key, u_value){
                        if(u_key.replace(/_/g, '') == value.name.toLowerCase() && u_value > 0){
                            var gateway = value['gateway_code'].replace(value['gateway_code'].match(/(\d+)/g)[0], '').trim(); 
                            if(oldType != value) {
                                var previous = $('<option class="text-uppercase gatewayType" disabled">'+ previousType +'</option>');
                            }
                            var option = $('<option class="text-uppercase gatewayType" value="'+ value['gateway_code'] +'">'+ gateway +'</option>');
                            $('.select-gateway-type').append(previous, option);
                        }
                        
                    
                    });
                    
                });



                oldInfo = $(this).data('gateway_credentials');

                $.each(oldInfo, function(key, value) {
                    var filterkey = key.replace("_", " ");
                    var div   = $('<div class="form-item mb-3 col-lg-6"></div>');
                    var label = $('<label for="' + key + '" class="form-label text-capitalize">' + filterkey + '<sup class="text--danger">*</sup></label>');
                    var input = $('<input type="text" class="form-control" id="' + key + '" value="' + value + '" name="driver_information[' + key + ']" placeholder="Enter ' + filterkey + '" required>');
                    div.append(label, input);
                    $('.oldData').append(div);
                });

                modal.modal('show');
            });
            
            
           

            $('.default_status').on('change', function(){
                
                const default_value = $(this).val();
                const id = $(this).attr('data-id');
                $.ajax({
                    method:'get',
                    url: "{{ route('user.sms.gateway.default.status') }}",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data:{
                    'default_value' : default_value,
                    'id' :id
                    },
                    dataType: 'json'
                }).then(response => {
                    if(response.status){
                        notify('success', 'Recommended Status Updated Successfully');
                        window.location.reload()
                    }
                    else{
                        notify('error', 'Inactive gateway can not be the default gateway');
                        window.location.reload()
                    }
                })


		    });
            $('.gateway_type').on('change', function() {
               
               $('.newdataadd').empty();
               var data = <?php echo $credentials; ?>;
              
               
               var newType = this.value;
               
               if(newType != oldType){
                   
                   $.each(data, function(key, v) { 
                       $('.oldData').empty();
                       if(v['gateway_code'] == newType) {

                       var creds = v['credential'];
                       $.each(creds, function(key, v) {

                          
                               var filterkey = key.replace("_", " ");
                               var div   = $('<div class="form-item mb-3 col-lg-6"></div>');
                               var label = $('<label for="' + key + '" class="form-label text-capitalize">' + filterkey + '<sup class="text--danger">*</sup></label>');
                               var input = $('<input type="text" class="form-control" id="' + key + '" name="driver_information[' + key + ']" placeholder="Enter ' + filterkey + '" required>');
                               div.append(label, input);
                               $('.newdataadd').append(div);
                           });
                       }
                   });
               }
               else{
             
                   $.each(oldInfo, function(key, value) {
                       var filterkey = key.replace("_", " ");
                       var div   = $('<div class="form-item mb-3 col-lg-6"></div>');
                       var label = $('<label for="' + key + '" class="form-label text-capitalize">' + filterkey + '<sup class="text--danger">*</sup></label>');
                       var input = $('<input type="text" class="form-control" id="' + key + '" value="' + value + '" name="driver_information[' + key + ']" placeholder="Enter ' + filterkey + '" required>');
                       div.append(label, input);
                       $('.oldData').append(div);
                   });
               }
           });
        
        })(jQuery);
    </script>
@endpush
