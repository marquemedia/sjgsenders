@extends('admin.layouts.app')
@section('panel')
<section>
    <div class="container-fluid p-0">
		<div class="card">
	    	<div class="card-header">
				<h4 class="card-title">{{translate('Automatic Payment Method')}}</h4>
			</div>
	        <div class="card-body px-0">
	            <div class="responsive-table">
	                <table>
	                    <thead>
	                        <tr>
	                        	<th>#</th>
	                            <th> {{ translate('Name')}}</th>
	                            <th> {{ translate('Image')}}</th>
	                            <th> {{ translate('Method Currency')}}</th>
	                            <th> {{ translate('Status')}}</th>
	                            <th> {{ translate('Action')}}</th>
	                        </tr>
	                    </thead>
	                    @php
	                    $i = 0;
	                    @endphp
	                    @forelse($paymentMethods as $paymentMethod)
                            @php
                                $i++;
                            @endphp
		                    <tr class="@if($loop->even)@endif">
		                    	<td data-label=" #">
		                    		{{$i}}
		                    	</td>
			                    <td data-label=" {{ translate('Name')}}">
			                    	{{$paymentMethod->name}}
			                    </td>

			                    <td data-label=" {{ translate('Logo')}}">
			                    	<img src="{{showImage(filePath()['payment_method']['path'].'/'.$paymentMethod->image)}}" class="automatic-payment-logo">
			                    </td>

			                    <td data-label=" {{ translate('Currency')}}">
			                    	{{$general->currency_name}} = {{shortAmount($paymentMethod->rate)}} {{$paymentMethod->currency->name}}
			                    </td>
			                    <td data-label=" {{ translate('Status')}}">
			                    	@if($paymentMethod->status == 1)
			                    		<span class="badge badge--success"> {{ translate('Active')}}</span>
			                    	@else
			                    		<span class="badge badge--danger"> {{ translate('Inactive')}}</span>
			                    	@endif
			                    </td>
			                    <td data-label=" {{ translate('Action')}}">
									<div class="d-flex align-items-center justify-content-md-start justify-content-end gap-3">
										@if(substr($paymentMethod->unique_code,0,6) == "MANUAL")
										    <a href="{{route('admin.manual.payment.edit',$paymentMethod->id)}}" class="i-btn primary--btn btn--sm"><i class="las la-pen"></i></a>
										@else
										    <a href="{{route('admin.payment.method.edit', [slug($paymentMethod->name), $paymentMethod->id])}}" class="i-btn primary--btn btn--sm"><i class="las la-pen"></i></a>
										@endif
									</div>
			                    </td>
		                    </tr>
		                @empty
		                	<tr>
		                		<td class="text-muted text-center" colspan="100%"> {{ translate('No Data Found')}}</td>
		                	</tr>
		                @endforelse
	                </table>
	            </div>
				<div class="m-3">
					{{$paymentMethods->links()}}
				</div>
	        </div>
	    </div>
	</div>
</section>
@endsection
