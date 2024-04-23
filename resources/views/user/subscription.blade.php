@extends('user.layouts.app')
@section('panel')
<section class="mt-3">
    <div class="container-fluid p-0">
		<div class="card">
			<div class="card-header">
				<h4 class="card-title">{{ translate('Current Plan')}}</h4>
			</div>
			<div class="card-body px-0">
				<div class="responsive-table">
					<table>
						<thead>
							<tr>
								<th> {{ translate('Date')}}</th>
								<th> {{ translate('Plan')}}</th>
								<th> {{ translate('Amount')}}</th>
								<th> {{ translate('SMS Credit')}}</th>
								<th> {{ translate('Email Credit')}}</th>
								<th> {{ translate('Whatsapp Credit')}}</th>
								<th> {{ translate('Expired Date')}}</th>
								<th> {{ translate('Status')}}</th>
							</tr>
						</thead>
						@forelse($subscriptions as $subscription)
							<tr class="@if($loop->even)@endif">
								<td data-label=" {{ translate('Date')}}">
									<span>{{diffForHumans($subscription->updated_at ? $subscription->updated_at : $subscription->created_at)}}</span><br>
									{{getDateTime($subscription->updated_at ? $subscription->updated_at : $subscription->created_at)}}
								</td>
	
								<td data-label="{{translate('Plan')}}">
									{{$subscription->plan->name}}
								</td>
	
								<td data-label=" {{ translate('Amount')}}">
									{{$general->currency_symbol}}{{shortAmount($subscription->amount)}}
								</td>
	
								<td data-label=" {{ translate('SMS Credit')}}">
									{{$subscription->plan->sms->credits}}  {{ translate('Credit')}}
								</td>
	
								<td data-label=" {{ translate('Email Credit')}}">
									{{$subscription->plan->email->credits}}  {{ translate('Credit')}}
								</td>
	
								<td data-label=" {{ translate('Whatsapp Credit')}}">
									{{$subscription->plan->whatsapp->credits}}  {{ translate('Credit')}}
								</td>
	
								<td data-label=" {{ translate('Expired')}}">
									{{getDateTime($subscription->expired_date)}}
								</td>
	
								<td data-label=" {{ translate('Status')}}">
									@if($subscription->status == App\Models\Subscription::RUNNING)
										<span class="badge badge--success"> {{ translate('Active')}}</span>
									@elseif($subscription->status == App\Models\Subscription::EXPIRED)
										<span class="badge badge--warning"> {{ translate('Expired')}}</span>
									@elseif($subscription->status == App\Models\Subscription::REQUESTED)
										<span class="badge badge--primary"> {{ translate('Requested')}}</span>
									@elseif($subscription->status == App\Models\Subscription::INACTIVE)
										<span class="badge badge--danger"> {{ translate('Inactive')}}</span>
									@elseif($subscription->status == App\Models\Subscription::RENEWED)
										<span class="badge badge--info"> {{ translate('Renewed')}}</span>
									@endif
								</td>
							</tr>
						@empty
							<tr>
								<td class="text-muted text-center" colspan="100%"> {{ translate('No Data Found')}}</td>
							</tr>
						@endforelse
					</table>
				</div>
			</div>
			<div class="m-3">
				{{$subscriptions->links()}}
			</div>
		</div>
	</div>
</section>
@endsection







