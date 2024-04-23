@extends('user.layouts.app')
@section('panel')
<section>
	<div class="card mb-4">
		<div class="card-header">
			<h4 class="card-title">{{translate('Support Tickets')}}</h4>
		</div>
		<div class="card-body px-0">
			<div class="responsive-table">
				<table>
					<thead>
						<tr>
							<th>{{ translate('Time')}}</th>
							<th>{{ translate('Ticket Number')}}</th>
							<th>{{ translate('Subject')}}</th>
							<th>{{ translate('Priority')}}</th>
							<th>{{ translate('Status')}}</th>
							<th>{{ translate('Action')}}</th>
						</tr>
					</thead>
					@forelse($tickets as $ticket)
						<tr class="@if($loop->even)@endif">
							<td data-label="{{ translate('Time')}}">
								<span class="fw-bold">{{diffForHumans($ticket->created_at)}}</span><br>
								{{getDateTime($ticket->created_at)}}
							</td>

							<td data-label="{{ translate('Ticket Number')}}">
								{{$ticket->ticket_number}}
							</td>

							<td data-label="{{ translate('Subject')}}">
								{{$ticket->subject}}
							</td>

							<td data-label="{{ translate('Priority')}}">
								@if($ticket->priority == 1)
									<span class="badge badge--info">{{ translate('Low')}}</span>
								@elseif($ticket->priority == 2)
									<span class="badge badge--success">{{ translate('Medium ')}}</span>
								@elseif($ticket->priority == 3)
									<span class="badge badge--primary">{{ translate('High')}}</span>
								@endif
							</td>

							<td data-label="{{ translate('Status')}}">
								@if($ticket->status == 1)
									<span class="badge badge--info">{{ translate('Running')}}</span>
								@elseif($ticket->status == 2)
									<span class="badge badge--primary">{{ translate('Answered')}}</span>
								@elseif($ticket->status == 3)
									<span class="badge badge--warning">{{ translate('Replied')}}</span>
								@elseif($ticket->status == 4)
									<span class="badge badge--danger">{{ translate('Closed')}}</span>
								@endif
							</td>

							<td data-label="{{ translate("Action")}}">
								<div class="d-flex align-items-center justify-content-md-start justify-content-end gap-3">
									<a href="{{route('user.ticket.detail', $ticket->id)}}" class="i-btn primary--btn btn--sm"><i class="las la-desktop"></i></a>
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
			<nav aria-label="Page navigation example">
				<ul class="pagination justify-content-end">
					{{$tickets->links()}}
				</ul>
			</nav>
		</div>
	</div>

	<a href="{{route('user.ticket.create')}}" class="support-ticket-float-btn" title="{{ translate('Create New Ticket')}}">
		<i class="fa fa-plus ticket-float"></i>
	</a>
</section>
@endsection




