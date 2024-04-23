@extends('admin.layouts.app')
@section('panel')
    <section>
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="card-title">{{translate('Currencies List')}}</h4>
                
                <a href="javascript:void(0);" class="i-btn primary--btn btn--md text-white" data-bs-toggle="modal" data-bs-target="#createNewCurrency" title="{{ translate('Create New Currency')}}">
                    <i class="fa-solid fa-plus"></i> {{translate('Add New')}}
                </a>
            </div>
            <div class="card-body px-0">
                <div class="responsive-table">
                    <table>
                        <thead>
                            <tr>
                                <th>{{ translate('Name')}}</th>
                                <th>{{ translate('Symbol')}}</th>
                                <th>{{ translate('Rate')}}</th>
                                <th>{{ translate('Status')}}</th>
                                <th>{{ translate('Action')}}</th>
                            </tr>
                        </thead>
                        @forelse($currencies as $currency)
                            <tr class="@if($loop->even)@endif">
                                <td data-label="{{ translate('Code')}}">
                                    {{$currency->name}}
                                </td>

                                <td data-label="{{ translate('Symbol')}}">
                                    {{$currency->symbol}}
                                </td>

                                <td data-label="{{ translate('Rate')}}">
                                    1 {{$general->currency_name}} = {{shortAmount($currency->rate)}} {{$currency->name}}
                                </td>

                                <td data-label="{{ translate('Status')}}">
                                    @if($currency->status == 1)
                                        <span class="badge badge--success">{{ translate('Active')}}</span>
                                    @elseif($currency->status == 2)
                                        <span class="badge badge--danger">{{ translate('Inactive')}}</span>
                                    @endif
                                </td>

                                <td data-label={{ translate('Action')}}>
                                    <div class="d-flex align-items-center justify-content-md-start justify-content-end gap-3">
                                        <a class="i-btn primary--btn btn--sm currency-data" data-bs-toggle="modal" data-bs-target="#updatecurrency" href="javascript:void(0)" data-id="{{$currency->id}}" data-name="{{$currency->name}}"  data-symbol="{{$currency->symbol}}"  data-status="{{$currency->status}}" data-rate="{{shortAmount($currency->rate)}}"><i class="las la-pen"></i></a>
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
            </div>
        </div>
    </section>


    <div class="modal fade" id="createNewCurrency" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{route('admin.general.setting.currency.store')}}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-header bg--lite--violet">
                                <div class="card-title text-center text--light">{{ translate('Add New Currency')}}</div>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="name" class="form-label">{{ translate('Name')}} <sup class="text--danger">*</sup></label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="{{ translate('Enter Name')}}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="symbol" class="form-label">{{ translate('Symbol')}} <sup class="text--danger">*</sup></label>
                                    <input type="text" class="form-control" id="symbol" name="symbol" placeholder="{{ translate('Enter Symbol')}}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="rate" class="form-label">{{ translate('Exchange Rate')}} <sup class="text--danger">*</sup></label>
                                    <div class="input-group">
                                        <span class="input-group-text" id="basic-addon1">1 {{$general->currency_name}} = </span>
                                        <input type="text" id="rate" name="rate" class="form-control" placeholder="0.00" aria-label="Username" aria-describedby="basic-addon1">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="status" class="form-label">{{ translate('Status')}} <sup class="text--danger">*</sup></label>
                                    <select class="form-control" name="status" id="status" required>
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


    <div class="modal fade" id="update-currency" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{route('admin.general.setting.currency.update')}}" method="POST">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-header bg--lite--violet">
                                <div class="card-title text-center text--light">{{ translate('Update Currency')}}</div>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="name" class="form-label">{{ translate('Name')}} <sup class="text--danger">*</sup></label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="{{ translate('Enter Name')}}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="symbol" class="form-label">{{ translate('Symbol')}} <sup class="text--danger">*</sup></label>
                                    <input type="text" class="form-control" id="symbol" name="symbol" placeholder="{{ translate('Enter Symbol')}}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="rate" class="form-label">{{ translate('Exchange Rate')}} <sup class="text--danger">*</sup></label>
                                    <div class="input-group">
                                        <span class="input-group-text" id="basic-addon1">1 {{$general->currency_name}} = </span>
                                        <input type="text" id="rate" name="rate" class="form-control" placeholder="0.00" aria-label="Username" aria-describedby="basic-addon1">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="status" class="form-label">{{ translate('Status')}} <sup class="text--danger">*</sup></label>
                                    <select class="form-control" name="status" id="status" required>
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
		$('.currency-data').on('click', function(){
            const modal = $('#update-currency');
            modal.find('input[name=id]').val($(this).data('id'));
			modal.find('input[name=name]').val($(this).data('name'));
			modal.find('input[name=symbol]').val($(this).data('symbol'));
			modal.find('input[name=rate]').val($(this).data('rate'));
			modal.find('select[name=status]').val($(this).data('status'));
			modal.modal('show');
		});
	})(jQuery);
</script>
@endpush


