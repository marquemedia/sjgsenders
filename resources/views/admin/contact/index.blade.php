@extends('admin.layouts.app')
@section('panel')
<section>
    <div class="card mb-4">
        <div class="card-header">
            <h4 class="card-title">{{$title}}</h4>
        </div>

        <div class="card-filter">
            <form action="{{route('admin.contact.search')}}" method="GET">
                <div class="filter-form">
                    <div class="filter-item">
                        <select name="status" class="form-select">
                            <option selected disabled>{{translate('Search By Status')}}</option>
                            <option value="all" @if(@$data["status"] == "all") selected @endif>{{translate('All')}}</option>
                            <option value="1" @if(@$data["status"] == "1") selected @endif>{{translate('Active')}}</option>
                            <option value="2" @if(@$data["status"] == "2") selected @endif>{{translate('Banned')}}</option>
                        </select>
                    </div>
                    <div class="filter-item">
                        <select name="contact_type" class="form-select">
                            <option selected disabled>{{translate('Search By Contact Type')}}</option>
                            <option value="all" @if(@$data["contact_type"] == "all") selected @endif>{{translate('All')}}</option>
                            <option value="sms_contact" @if(@$data["contact_type"] == "sms") selected @endif>{{translate('SMS Contact')}}</option>
                            <option value="whatsapp_contact" @if(@$data["contact_type"] == "whatsapp") selected @endif>{{translate('WhatsApp Number')}}</option>
                            <option value="email_contact" @if(@$data["contact_type"] == "email") selected @endif>{{translate('Email Address')}}</option>
                            <option value="none" @if(@$data["contact_type"] == "none") selected @endif>{{translate('None')}}</option>
                        </select>
                    </div>
                    <div class="filter-item">
                        <select name="group" class="form-select">
                            <option selected disabled>{{translate('Search By Group')}}</option>
                            <option value="all" @if(@$data["group"] == "all") selected @endif>{{translate('All')}}</option>
                            @foreach($groups as $id => $name)
                                <option @if(@$data["group"] == $id) selected @endif value="{{$id}}">{{$name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-item">
                        <input type="text" autocomplete="off" name="search" placeholder="{{translate('Type Name, Contact Number or Mail Address')}}" class="form-control" id="search" value="{{@$data["search"]}}">
                    </div>

                    <div class="filter-action">
                        
                        <button class="i-btn info--btn btn--md" type="submit">
                            <i class="fas fa-search"></i> {{ translate('Search')}}
                        </button>
                        <a class="i-btn danger--btn btn--md text-white" href="{{ route('admin.contact.index') }}">
                            <i class="las la-sync"></i>  {{translate('reset')}}
                        </a>
                        

                        <div class="statusUpdateBtn d-none">
                            <a class="i-btn success--btn btn--md bulkAction"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top" title="Bulk Actions"
                                    data-bs-toggle="tooltip"
                                    data-bs-target="#contactBulkAction">
                                <i class="fas fa-gear"></i> {{translate('Action')}}
                            </a>
                        </div>
                        <a href="{{ route("admin.contact.create") }}" class="i-btn primary--btn btn--md text-white" title="{{ translate("Add New Contact")}}">
                            <i class="fa fa-plus"></i>{{ translate('Add New')}}
                        </a>
                    </div>
                    
                </div>
            </form>
        </div>

        <div class="card-body px-0">
            <div class="responsive-table">
                <table>
                    <thead>
                        <tr>
                            <th>
                                <div class="d-flex align-items-center">
                                    <input class="form-check-input mt-0 me-2 checkAll"
                                        type="checkbox"
                                        value=""
                                        aria-label="Checkbox for following text input"> <span>{{ translate("Sl No.") }}</span>
                                </div>
                            </th>
                            <th>{{ translate('Contact Name')}}</th>
                            <th>{{ translate('Group')}}</th>
                            <th>{{ translate('SMS Contact')}}</th>
                            <th>{{ translate('WhatsApp Number')}}</th>
                            <th>{{ translate('Email Address')}}</th>
                            <th>{{ translate('Status')}}</th>
                            <th>{{ translate('Action')}}</th>
                        </tr>
                    </thead>
                    
                    @forelse($contacts as $contact)
                        <tr>
                            <td class="d-none d-sm-flex align-items-center">
                                <input class="form-check-input mt-0 me-2" type="checkbox" name="contactUid" value="{{$contact->uid}}" aria-label="Checkbox for following text input">
                                {{$loop->iteration}}
                            </td>
                            <td class=" text-capitalize " data-label="{{ translate('Contact Name')}}">
                                {{$contact->first_name || $contact->last_name ? $contact->first_name." ".$contact->last_name : "N/A" }}
                            </td>
                            <td data-label=" {{ translate('Contact')}}">
                                <a href="{{route('admin.contact.group.index', $contact->group_id)}}" class="badge badge--primary p-2">{{translate("View: ").$contact->group?->name}}</a>
                            </td>
                            <td data-label="{{ translate('Phone Number')}}">
                                @if($contact->sms_contact)
                                    {{$contact->sms_contact}}
                                @else
                                    <span class="badge badge--info">{{ translate('N/A')}}</span>
                                @endif
                            </td>
                            <td data-label="{{ translate('WhatsApp Number')}}">
                                @if($contact->whatsapp_contact)
                                    {{$contact->whatsapp_contact}}
                                @else
                                    <span class="badge badge--info">{{ translate('N/A')}}</span>
                                @endif
                                
                            </td>
                            <td data-label="{{ translate('SMS Contact')}}">
                                @if($contact->email_contact)
                                    {{$contact->email_contact}}
                                @else
                                    <span class="badge badge--info">{{ translate('N/A')}}</span>
                                @endif
                            </td>
                           
                            <td data-label="{{ translate('Status')}}">
                                @if($contact->status == App\Models\Contact::ACTIVE)
                                    <span class="badge badge--success">{{ translate('Active')}}</span>
                                @else
                                    <span class="badge badge--danger">{{ translate('Banned')}}</span>
                                @endif
                            </td>
                           
                            <td data-label={{ translate('Action')}}>
                                <div class="d-flex align-items-center justify-content-md-start justify-content-end gap-3">

                                    @php 
                                        $data = [];
                                        $data["name"]            = $contact->first_name." ".$contact->last_name;
                                        if ($contact->whatsapp_contact !== null) {
                                            $data["whatsapp_number"] = $contact->whatsapp_contact;
                                        }

                                        if ($contact->sms_contact !== null) {
                                            $data["sms_contact"] = $contact->sms_contact;
                                        }

                                        if ($contact->email_contact !== null) {
                                            $data["email_contact"] = $contact->email_contact;
                                        }
                                        
                                        if($contact->attributes) {
                                            
                                            foreach($contact->attributes as $key => $value) {
                                                $data[$key] = $value;
                                            }
                                        }
                                        
                                        $data["contact_added"]   = Carbon\Carbon::parse($contact->created_at)->toDayDateTimeString();
                                        $data["contact_updated"] = Carbon\Carbon::parse($contact->updated_at)->toDayDateTimeString();
                                        
                                    @endphp

                                    <a class="i-btn info--btn btn--sm contact-details"
                                        data-contact_information="{{json_encode($data)}}"
                                        data-bs-placement="top" title="Gateway Information"
                                        data-bs-toggle="modal"
                                        data-bs-target="#contactInfo">
                                        <i class="las la-info-circle"></i>
                                    </a> 
                                    <a class="i-btn primary--btn btn--sm contact" data-bs-toggle="modal" data-bs-target="#updateContact" href="javascript:void(0)"
                                    data-uid              ="{{$contact->uid}}"
                                    data-first_name       ="{{$contact->first_name}}"
                                    data-last_name        ="{{$contact->last_name}}"
                                    data-group_id         ="{{$contact->group_id}}"
                                    data-attributes       ="{{json_encode($contact->attributes)}}"
                                    data-whatsapp_contact ="{{$contact->whatsapp_contact}}"
                                    data-email_contact    ="{{$contact->email_contact}}"
                                    data-sms_contact      ="{{$contact->sms_contact}}"
                                    data-status           ="{{$contact->status}}"
                                    ><i class="las la-pen"></i></a>
                                    <a class="i-btn danger--btn btn--sm delete" data-bs-toggle="modal" data-bs-target="#deleteContact" href="javascript:void(0)" data-uid="{{$contact->uid}}"><i class="las la-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-muted text-center" colspan="100%">{{ translate('No Data Found')}}</td>
                        </tr>
                    @endforelse
                </table>
            </div>
            <div class="m-3">
                {{$contacts->appends(request()->all())->onEachSide(1)->links()}}
            </div>
        </div>
    </div>
    
</section>

<!-- Update Contact Modal -->
<div class="modal fade" id="updateContact" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ translate('Update Contact Information')}}</h5>
                <button type="button" class="i-btn bg--lite--danger text--danger btn--sm" data-bs-dismiss="modal"> <i class="las la-times"></i></button>
            </div>
            <form action="{{route('admin.contact.update')}}" method="POST">
                @csrf
                <input type="hidden" name="uid">
                <input type="hidden" name="single_contact" value="true">
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3">
                                    <label class="form-label" for="group_id">{{ translate('Select a Group')}}</label>
                                    <select class="form-select" name="group_id" id="group_id">
                                        <option selected disabled="">{{ translate('Select One')}}</option>
                                        @foreach($groups as $id => $name)
                                            <option value="{{$id}}">{{$name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3 col-lg-6">
                                    <label for="first_name" class="form-label"> {{ translate('Contact First Name')}} <sup class="text--danger">*</sup></label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" placeholder=" {{ translate('Enter First Name')}}" required>
                                </div>
                                <div class="mb-3 col-lg-6">
                                    <label for="last_name" class="form-label"> {{ translate('Contact Last Name')}} <sup class="text--danger">*</sup></label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" placeholder=" {{ translate('Enter Last Name')}}" required>
                                </div>
                                <div class="mb-3 col-lg-6">
                                    <label for="whatsapp_contact" class="form-label"> {{ translate('WhatsApp Number')}}</label>
                                    <input type="text" class="form-control" id="whatsapp_contact" name="whatsapp_contact" placeholder=" {{ translate('Enter WhatsApp Number')}}">
                                </div>
                                <div class="mb-3 col-lg-6">
                                    <label for="sms_contact" class="form-label"> {{ translate('SMS Number')}}</label>
                                    <input type="text" class="form-control" id="sms_contact" name="sms_contact" placeholder=" {{ translate('Enter SMS Number')}}">
                                </div>
                                <div class="mb-3 col-lg-6">
                                    <label for="email_contact" class="form-label"> {{ translate('Email Address')}}</label>
                                    <input type="text" class="form-control" id="email_contact" name="email_contact" placeholder=" {{ translate('Enter Email Address')}}" >
                                </div>
    
                                <div class="mb-3 col-lg-6">
                                    <label for="status" class="form-label"> {{ translate('Status')}} <sup class="text--danger">*</sup></label>
                                    <select class="form-select" name="status" id="status" required>
                                        <option selected disabled="">{{ translate('Select One')}}</option>
                                        <option value="1"> {{ translate('Active')}}</option>
                                        <option value="2"> {{ translate('Banned')}}</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row addExtraAttribute"></div>
                            
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
<!-- Delete Modal -->
<div class="modal fade" id="deleteContact" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{route('admin.contact.delete')}}" method="POST">
                @csrf
                <input type="hidden" name="uid">
                <div class="modal_body2">
                    <div class="modal_icon2">
                        <i class="las la-trash"></i>
                    </div>
                    <div class="modal_text2">
                        <h6> {{ translate('Are you sure to want delete this Contact?')}} </h6>
                        <p>{{ translate("This will permanently remove the contact from your database") }}</p>
                    </div>
                </div>
                <div class="modal_button2 modal-footer">
                    <div class="d-flex align-items-center justify-content-center gap-3">
                        <button type="button" class="i-btn primary--btn btn--md" data-bs-dismiss="modal"> {{ translate('Cancel')}}</button>
                        <button type="submit" class="i-btn danger--btn btn--md"> {{ translate('Delete')}}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Bulk Action -->
<div class="modal fade" id="contactBulkAction" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog nafiz">
        <div class="modal-content">
            <form action="{{route('admin.contact.bulk.status.update')}}" method="POST">
                @csrf
                <input type="hidden" name="id">
                <input type="hidden" name="contactUid">
                <div class="modal-body">
                    <div class="card">
                        <div class="card-header bg--lite--violet">
                            <div class="card-title text-center text--light">{{ translate('Contact Status Update')}}</div>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="status" class="form-label">{{ translate('Status')}} <sup class="text--danger">*</sup></label>
                                <select class="form-control" name="status" id="status" required>
                                    <option value="" selected="" disabled="">{{ translate('Select Status')}}</option>
                                    <option value="1">{{ translate('Active')}}</option>
                                    <option value="2">{{ translate('Inactive')}}</option>
                                </select>
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
<!-- Contact Information -->
<div class="modal fade" id="contactInfo" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-body">
                <div class="card">
                    <div class="card-header bg--lite--violet">
                        <div class="card-title text-center text--light">{{ translate('Gateway Information')}}</div>
                    </div>
                    <div class="card-body">
                        <div class="contact-info"></div>
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
@endsection


@push('script-push')
<script>
    (function($){
        "use strict";
        
        

        $('.contact').on('click', function(){

      
			var modal = $('#updateContact');
            modal.find('.addExtraAttribute').empty();
			modal.find('input[name=uid]').val($(this).data('uid'));
			modal.find('input[name=first_name]').val($(this).data('first_name'));
			modal.find('input[name=last_name]').val($(this).data('last_name'));
			modal.find('input[name=whatsapp_contact]').val($(this).data('whatsapp_contact'));
			modal.find('input[name=sms_contact]').val($(this).data('sms_contact'));
			modal.find('input[name=email_contact]').val($(this).data('email_contact'));
			modal.find('select[name=status]').val($(this).data('status'));
			modal.find('select[name=group_id]').val($(this).data('group_id'));

            

            var attributes = $(this).data('attributes');
            
            
            $.each(attributes, function (key, value) {
                
                var contactAttributes = JSON.parse('{!! $contactAttributes !!}');
                if (contactAttributes[key].type == {{ App\Models\GeneralSetting::DATE }}) {

                    modal.find('.addExtraAttribute').append(`<div class="mb-3 col-lg-6"><label for="${convertToTitleCase(key)}" class="form-label">{{ textFormat(["_"], '${convertToTitleCase(key)}')}}</label><input type="datetime" value="${value.value}" class="static-flatpicker form-control" name="attributes[${key}::${value.type}]" placeholder="Enter {{ textFormat(["_"], '${convertToTitleCase(key)}') }}"></div>`);
                }
                if (contactAttributes[key].type == {{ App\Models\GeneralSetting::BOOLEAN }}) {
                    modal.find('.addExtraAttribute').append(`<div class="mb-3 col-lg-6"><label for="${convertToTitleCase(key)}" class="form-label">{{ textFormat(["_"], '${convertToTitleCase(key)}')}}</label><select class="form-select" name="attributes[${key}::${value.type}]" required><option disabled>-- Select An Option --</option><option ${value.value == "true" ? 'selected' : ''} value="true">{{ translate("Yes") }}</option><option ${value.value == "false" ? 'selected' : ''} value="false">{{ translate("No") }}</option></select></div>`);
                }
                if (contactAttributes[key].type == {{ App\Models\GeneralSetting::NUMBER }}) {
                    modal.find('.addExtraAttribute').append(`<div class="mb-3 col-lg-6"><label for="${convertToTitleCase(key)}" class="form-label">{{ textFormat(["_"], '${convertToTitleCase(key)}')}}</label><input type="number" value="${value.value}" class="form-control" name="attributes[${key}::${value.type}]" placeholder="Enter {{ textFormat(["_"], '${convertToTitleCase(key)}') }}"></div>`);
                }
                if (contactAttributes[key].type == {{ App\Models\GeneralSetting::TEXT }}) {
                    modal.find('.addExtraAttribute').append(`<div class="mb-3 col-lg-6"><label for="${convertToTitleCase(key)}" class="form-label">{{ textFormat(["_"], '${convertToTitleCase(key)}')}}</label><input type="text" value="${value.value}" class="form-control" name="attributes[${key}::${value.type}]" placeholder="Enter {{ textFormat(["_"], '${convertToTitleCase(key)}') }}"></div>`);
                }
            });


			modal.modal('show');
		});
        
        
        function convertToTitleCase(str) {
            return str.replace(/_/g, ' ').replace(/\w\S*/g, function (txt) {
                return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
            });
        }
        $('.delete').on('click', function(){
			var modal = $('#deleteContact');
			modal.find('input[name=uid]').val($(this).data('uid'));
		});
        $('.checkAll').click(function(){
            $('input:checkbox').not(this).prop('checked', this.checked);
        });
        $('.bulkAction').on('click', function(){
            var modal = $('#contactBulkAction');
            modal.find('input[name=id]').val($(this).data('id'));
            modal.modal('show');
        });
        $('.bulkAction').on('click', function(){
            var modal = $('#contactBulkAction');
            var newArray = [];
            $("input:checkbox[name=contactUid]:checked").each(function(){
                newArray.push($(this).val());
            });
            modal.find('input[name=contactUid]').val(newArray.join(','));
            modal.modal('show');
        });

        $('.contact-details').on('click', function(){
            $('.contact-info').empty();
            var modal = $('#contactInfo');
            var driver = $(this).data('contact_information');
            $.each(driver, function(key, value) {
                if(jQuery.type(value) === "object") {
                    var paragraph = $('<p class="mb-2 d-flex justify-content-start align-items-center "><span class="fw-bold text-capitalize col-4 mb-1">' + convertToTitleCase(key) + ' </span> <span class="col-9"> : ' + (value.value === "true" ? "Yes" : (value.value === "false" ? "No" : value.value)) + ' </span></p>');
                } else{
                    var paragraph = $('<p class="mb-2 d-flex justify-content-start align-items-center "><span class="fw-bold text-capitalize col-4 mb-1">' + convertToTitleCase(key) + ' </span> <span class="col-9"> : ' + value + ' </span></p>');
                }
                $('.contact-info').append(paragraph);
            });
            modal.modal('show');
        });
    })(jQuery);

    
</script>
@endpush

