@extends('user.layouts.app')
@section('panel')
<section>
    <div class="card mb-4">
        <div class="card-header">
            <h4 class="card-title">{{$title}}</h4>
        </div>

        <div class="card-filter">
            <form action="{{route('user.contact.group.search')}}" method="GET">
                <div class="filter-form">
                    <div class="filter-item">
                        <select name="status" class="form-select">
                            <option value="all" @if(@$status == "all") selected @endif>{{translate('All')}}</option>
                            <option value="active" @if(@$status == "active") selected @endif>{{translate('Active')}}</option>
                            <option value="inactive" @if(@$status == "inactive") selected @endif>{{translate('Inactive')}}</option>
                        </select>
                    </div>

                    <div class="filter-item">
                        <input type="text" autocomplete="off" name="search" placeholder="{{translate('Search with group name')}}" class="form-control" id="search" value="{{@$search}}">
                    </div>

                    <div class="filter-action">
                        <button class="i-btn info--btn btn--md" type="submit">
                            <i class="fas fa-search"></i> {{ translate('Search')}}
                        </button>
                        <a class="i-btn danger--btn btn--md text-white" href="{{ route('user.contact.group.index') }}">
                            <i class="las la-sync"></i>  {{translate('reset')}}
                        </a>
                        <div class="statusUpdateBtn d-none">
                            <a class="i-btn success--btn btn--md bulkAction"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top" title="Bulk Actions"
                                    data-bs-toggle="tooltip"
                                    data-bs-target="#contactGroupBulkAction">
                                <i class="fas fa-gear"></i> {{translate('Action')}}
                            </a>
                        </div>
                        <a class="i-btn primary--btn btn--md text-white" data-bs-toggle="modal" data-bs-target="#createGroup" title="{{ translate("Add New Group")}}">
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
                            <th>
                                <div class="d-flex align-items-center">
                                    <input class="form-check-input mt-0 me-2 checkAll"
                                        type="checkbox"
                                        value=""
                                        aria-label="Checkbox for following text input"> <span>{{ translate("Sl No.") }}</span>
                                </div>
                            </th>
                            <th>{{ translate('Group Name')}}</th>
                            <th>{{ translate('Contacts')}}</th>
                            <th>{{ translate('Status')}}</th>
                            <th>{{ translate('Action')}}</th>
                        </tr>
                    </thead>
                    
                    @forelse($contact_groups as $contact_group)
                        <tr>
                            <td class="d-none d-sm-flex align-items-center">
                                <input class="form-check-input mt-0 me-2" type="checkbox" name="contactGroupUid" value="{{$contact_group->uid}}" aria-label="Checkbox for following text input">
                                {{$loop->iteration}}
                            </td>
                            <td class=" text-capitalize " data-label="{{ translate('Group Name')}}">
                                {{$contact_group->name}}
                            </td>
                            <td data-label=" {{ translate('Contact')}}">
                                <a href="{{route('user.contact.index', $contact_group->id)}}" class="badge badge--primary p-2"> {{ translate('view contacts ')}} ({{count($contact_group->contact)}}) </a>
                            </td>
                           
                            <td data-label="{{ translate('Status')}}">
                                @if($contact_group->status == App\Models\Group::ACTIVE)
                                    <span class="badge badge--success">{{ translate('Active')}}</span>
                                @else
                                    <span class="badge badge--danger">{{ translate('Inactive')}}</span>
                                @endif
                            </td>
                          
                            <td data-label={{ translate('Action')}}>
                                <div class="d-flex align-items-center justify-content-md-start justify-content-end gap-3">
                                    <a class="i-btn primary--btn btn--sm group" data-bs-toggle="modal" data-bs-target="#updateGroup" href="javascript:void(0)"
                                    data-uid="{{$contact_group->uid}}"
                                    data-name="{{$contact_group->name}}"
                                    data-status="{{$contact_group->status}}"
                                    ><i class="las la-pen"></i></a>
                                    <a class="i-btn danger--btn btn--sm delete" data-bs-toggle="modal" data-bs-target="#deletegroup" href="javascript:void(0)" data-id="{{$contact_group->id}}"><i class="las la-trash"></i></a>
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
                {{$contact_groups->appends(request()->all())->onEachSide(1)->links()}}
            </div>
        </div>
    </div>
   
</section>

<!-- Add Attribute Modal -->
<div class="modal fade" id="createGroup" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ translate('Create Group')}}</h5>
                <button type="button" class="i-btn bg--lite--danger text--danger btn--sm" data-bs-dismiss="modal"> <i class="las la-times"></i></button>
            </div>
            <form action="{{route('user.contact.group.store')}}" method="POST">

                @csrf
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-item mb-3">
                                <label for="group_name" class="form-label"> {{ translate('Group Name')}} <sup class="text--danger">*</sup></label>
                                <input type="text" class="form-control" id="group_name" name="group_name" placeholder=" {{ translate('Enter Group Name')}}" required>
                            </div>

                            <div class="form-item ">
                                <label for="status" class="form-label"> {{ translate('Status')}} <sup class="text--danger">*</sup></label>
                                <select class="form-select" name="status" id="status" required>
                                    <option value="1"> {{ translate('Active')}}</option>
                                    <option value="2"> {{ translate('Inactive')}}</option>
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
<div class="modal fade" id="updateGroup" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ translate('Update Group Information')}}</h5>
                <button type="button" class="i-btn bg--lite--danger text--danger btn--sm" data-bs-dismiss="modal"> <i class="las la-times"></i></button>
            </div>
            <form action="{{route('user.contact.group.update')}}" method="POST">
                @csrf
                <input type="hidden" name="uid">
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="group_name" class="form-label"> {{ translate('Group Name')}} <sup class="text--danger">*</sup></label>
                                <input type="text" class="form-control" id="group_name" name="group_name" placeholder=" {{ translate('Enter Group Name')}}" required>
                            </div>

                            <div>
                                <label for="status" class="form-label"> {{ translate('Status')}} <sup class="text--danger">*</sup></label>
                                <select class="form-control" name="status" id="status" required>
                                    <option value="1"> {{ translate('Active')}}</option>
                                    <option value="2"> {{ translate('Inactive')}}</option>
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
<div class="modal fade" id="deletegroup" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{route('user.contact.group.delete')}}" method="POST">
                @csrf
                <input type="hidden" name="id">
                <div class="modal_body2">
                    <div class="modal_icon2">
                        <i class="las la-trash"></i>
                    </div>
                    <div class="modal_text2">
                        <h6> {{ translate('Are you sure to want delete this group?')}} </h6>
                        <p>({{ translate("This will delete all the contacts within this group as well") }})</p>
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
<div class="modal fade" id="contactGroupBulkAction" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog nafiz">
        <div class="modal-content">
            <form action="{{route('user.contact.group.bulk.status.update')}}" method="POST">
                @csrf
                <input type="hidden" name="id">
                <input type="hidden" name="contactGroupUid">
                <div class="modal-body">
                    <div class="card">
                        <div class="card-header bg--lite--violet">
                            <div class="card-title text-center text--light">{{ translate('Contact Group Status Update')}}</div>
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
@endsection


@push('script-push')
<script>
    (function($){
        "use strict";
        $('.select2').select2({
            tags: true,
            tokenSeparators: [',']
        });
        $('.group').on('click', function(){
			var modal = $('#updateGroup');
			modal.find('input[name=uid]').val($(this).data('uid'));
			modal.find('input[name=group_name]').val($(this).data('name'));
			modal.find('select[name=status]').val($(this).data('status'));
			modal.modal('show');
		});
        $('.delete').on('click', function(){
			var modal = $('#deletegroup');
			modal.find('input[name=id]').val($(this).data('id'));
		});
        $('.checkAll').click(function(){
            $('input:checkbox').not(this).prop('checked', this.checked);
        });
        $('.bulkAction').on('click', function(){
            var modal = $('#contactGroupBulkAction');
            modal.find('input[name=id]').val($(this).data('id'));
            modal.modal('show');
        });
        $('.bulkAction').on('click', function(){
            var modal = $('#contactGroupBulkAction');
            var newArray = [];
            $("input:checkbox[name=contactGroupUid]:checked").each(function(){
                newArray.push($(this).val());
            });
            modal.find('input[name=contactGroupUid]').val(newArray.join(','));
            modal.modal('show');
        });
    })(jQuery);

    
</script>
@endpush

