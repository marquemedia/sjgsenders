@extends('user.layouts.app')
@section('panel')
<section>
    <div class="card mb-4">
        <div class="card-header">
            <h4 class="card-title">{{translate('Contact Attribute List')}}</h4>
        </div>

        <div class="card-filter">
            <form action="{{route('user.contact.settings.attribute.search')}}" method="GET">
                <div class="filter-form">
                    <div class="filter-item">
                        <select name="status" class="form-select">
                            <option value="all" @if(@$status == "all") selected @endif>{{translate('All')}}</option>
                            <option value="active" @if(@$status == "active") selected @endif>{{translate('Active')}}</option>
                            <option value="inactive" @if(@$status == "inactive") selected @endif>{{translate('Inactive')}}</option>
                        </select>
                    </div>

                    <div class="filter-item">
                        <input type="text" autocomplete="off" name="search" placeholder="{{translate('Search with Attribute Name')}}" class="form-control" id="search" value="{{@$search}}">
                    </div>

                    <div class="filter-action">
                        <button class="i-btn info--btn btn--md" type="submit">
                            <i class="fas fa-search"></i> {{ translate('Search')}}
                        </button>
                        <a class="i-btn danger--btn btn--md text-white" href="{{ route('user.contact.settings.index') }}">
                            <i class="las la-sync"></i>  {{translate('reset')}}
                        </a>
                        <a class="i-btn primary--btn btn--md text-white" data-bs-toggle="modal" data-bs-target="#createAttribute" title="{{ translate("Add New Attribute")}}">
                            <i class="fa fa-plus"></i> {{ translate('Add New')}}
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
                            <th>{{ translate('ID')}}</th>
                            <th>{{ translate('Attribute Name')}}</th>
                            <th>{{ translate('Attribute Type')}}</th>
                            <th>{{ translate('Status')}}</th>
                            <th>{{ translate('Action')}}</th>
                        </tr>
                    </thead>
                    
                    @forelse($contact_attributes as $attribute_key => $attribute_value)
                        
                        <tr class="@if($loop->even)@endif">
                           
                            <td data-label="{{ translate('ID')}}">
                                {{$loop->iteration}}
                            </td>
                            <td data-label="{{ translate('Attribute Name')}}">
                                {{textFormat(['_'], $attribute_key) ?? 'N/A'}}
                            </td>

                            <td data-label="{{translate('Attribute Type')}}">
                                
                                @if($attribute_value["type"] == \App\Models\GeneralSetting::DATE)
                                    <span class="badge badge--info">{{ translate('Date')}}</span>
                                @elseif($attribute_value["type"] == \App\Models\GeneralSetting::BOOLEAN)
                                    <span class="badge badge--info">{{ translate('Boolean')}}</span>
                                @elseif($attribute_value["type"] == \App\Models\GeneralSetting::NUMBER)
                                    <span class="badge badge--info">{{ translate('Number')}}</span>
                                @elseif($attribute_value["type"] == \App\Models\GeneralSetting::TEXT)
                                    <span class="badge badge--info">{{ translate('Text')}}</span>
                                @endif

                            </td>
                            
                            <td class="text-center" data-label="{{ translate('Status')}}">
                                <div class="d-flex justify-content-md-start justify-content-end">
                                    <label class="switch">
                                        <input {{ $attribute_value['status'] == true ? 'checked' : '' }} type="checkbox" class="attribute_status" data-attribute_name="{{ $attribute_key }}" value="1" name="status" id="status">
                                        <span class="slider"></span>
                                    </label>
                                </div>
                            </td>
                            
                            <td data-label={{ translate('Action')}}>
                                <div class="d-flex align-items-center justify-content-md-start justify-content-end gap-3">
                                    <a class="i-btn primary--btn btn--sm attribute" 
                                    data-old={{ $attribute_key }}
                                    data-name="{{textFormat(['_'], $attribute_key) ?? 'N/A'}}"
                                    data-type="{{$attribute_value["type"]}}"
                                    data-bs-toggle="modal" data-bs-target="#updatebrand" href="javascript:void(0)"><i class="las la-pen"></i></a>

                                    <a class="i-btn danger--btn btn--sm deleteAttribute" 
                                    data-attribute_name="{{$attribute_key}}" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#deleteAttribute" 
                                    href="javascript:void(0)"><i class="las la-trash"></i></a>
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
                {{$contact_attributes->appends(request()->all())->onEachSide(1)->links()}}
            </div>
        </div>
    </div>
</section>

<!-- Add Attribute Modal -->
<div class="modal fade" id="createAttribute" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ translate('Add New Attribute')}}</h5>
                <button type="button" class="i-btn bg--lite--danger text--danger btn--sm" data-bs-dismiss="modal"> <i class="las la-times"></i></button>
            </div>
            <form action="{{route('user.contact.settings.store')}}" method="POST">

                @csrf
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-item mb-3">
                                <label for="attribute_name" class="form-label"> {{ translate('Attribute Name')}} <sup class="text--danger">*</sup></label>
                                <input type="text" class="form-control" id="atribute_name" name="attribute_name" placeholder=" {{ translate('Enter Attribute Name')}}" required>
                            </div>
                            <div class="form-item mb-3">
                                <label for="attribute_type" class="form-label"> {{ translate('Attribute Type')}} <sup class="text--danger">*</sup></label>
                                <select class="form-select" name="attribute_type" id="attribute_type" required>
                                    <option selected disabled> {{ translate("--Select One Type--")}}</option>
                                    <option value="{{ \App\Models\GeneralSetting::DATE }}"> {{ translate('Date')}}</option>
                                    <option value="{{ \App\Models\GeneralSetting::BOOLEAN }}"> {{ translate('Boolean')}}</option>
                                    <option value="{{ \App\Models\GeneralSetting::NUMBER }}"> {{ translate('Number')}}</option>
                                    <option value="{{ \App\Models\GeneralSetting::TEXT }}"> {{ translate('Text')}}</option>
                                </select>
                            </div>

                            <div class="form-item ">
                                <label for="status" class="form-label"> {{ translate('Status')}} <sup class="text--danger">*</sup></label>
                                <select class="form-select" name="status" id="status" required>
                                    <option value="true"> {{ translate('Active')}}</option>
                                    <option value="false"> {{ translate('Inactive')}}</option>
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
<!-- Update Group Modal -->
<div class="modal fade" id="updateAttribute" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ translate('Update Group Information')}}</h5>
                <button type="button" class="i-btn bg--lite--danger text--danger btn--sm" data-bs-dismiss="modal"> <i class="las la-times"></i></button>
            </div>
            <form action="{{route('user.contact.settings.update')}}" method="POST">
                @csrf
                <input type="hidden" name="oldKey">
               
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-item mb-3">
                                <label for="attribute_name" class="form-label"> {{ translate('Attribute Name')}} <sup class="text--danger">*</sup></label>
                                <input type="text" class="form-control" id="atribute_name" name="attribute_name" placeholder=" {{ translate('Enter Attribute Name')}}" required>
                            </div>
                            <div class="form-item mb-3">
                                <label for="attribute_type" class="form-label"> {{ translate('Attribute Type')}} <sup class="text--danger">*</sup></label>
                                <select class="form-select" name="attribute_type" id="attribute_type" required>
                                    <option selected disabled> {{ translate("--Select One Type--")}}</option>
                                    <option value="{{ \App\Models\GeneralSetting::DATE }}"> {{ translate('Date')}}</option>
                                    <option value="{{ \App\Models\GeneralSetting::BOOLEAN }}"> {{ translate('Boolean')}}</option>
                                    <option value="{{ \App\Models\GeneralSetting::NUMBER }}"> {{ translate('Number')}}</option>
                                    <option value="{{ \App\Models\GeneralSetting::TEXT }}"> {{ translate('Text')}}</option>
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
<!-- Delete Modal -->
<div class="modal fade" id="deleteAttribute" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{route('user.contact.settings.delete')}}" method="POST">
                @csrf
                <input type="hidden" name="attribute_name">
                <div class="modal_body2">
                    <div class="modal_icon2">
                        <i class="las la-trash"></i>
                    </div>
                    <div class="modal_text2">
                        <h6> {{ translate('Are you sure to want delete this Attribute?')}} </h6>
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
@endsection


@push('script-push')
<script>
    (function($){
        "use strict";
        $('.attribute').on('click', function(){
            console.log($(this).data('status'));
			var modal = $('#updateAttribute');
			modal.find('input[name=attribute_name]').val($(this).data('name'));
			modal.find('input[name=oldKey]').val($(this).data('old'));
			modal.find('select[name=attribute_type]').val($(this).data('type'));
			
			modal.modal('show');
		});
        $('.deleteAttribute').on('click', function(){
            
			var modal = $('#deleteAttribute');
			modal.find('input[name=attribute_name]').val($(this).data('attribute_name'));
		});
        $('.attribute_status').on('change', function() {
            const status = this.checked ? true : false;
            const name = $(this).data('attribute_name');
            
            $.ajax({
                method: 'get',
                url: "{{ route('user.contact.settings.status.update') }}",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {
                    'status': status,
                    'name': name
                },
                dataType: 'json'
            }).then(response => {
                if (response.status) {
                    notify('success', 'Contact Attribute Status Updated Successfully');
                    window.location.reload();
                } else {
                    notify('error', 'Could Not Update Contact Status');
                    window.location.reload();
                }
            });
        });
    })(jQuery);
</script>
@endpush

