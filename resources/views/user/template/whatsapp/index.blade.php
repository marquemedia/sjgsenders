@extends('user.layouts.app')
@section('panel')
<section>

    <div class="container-fluid p-0">
		<div class="table_heading d-flex align--center justify--between">
			<nav  aria-label="breadcrumb">
				  <ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="{{ route("user.gateway.whatsapp.create") }}"> {{translate('Whatsapp Gateway Settings')}}</a></li>
					<li class="breadcrumb-item"><a href="#"> {{translate('WhatsApp API Templates')}}</a></li>
				  </ol>
			</nav>

		</div>

		<div class="card">
			<div class="card-body px-0">
				<div class="responsive-table">
					<table>
						<thead>
							<tr>
                                <th>{{ translate('Sl No') }}</th>
                                <th>{{ translate('Name') }}</th>
                                <th>{{ translate('Langauge Code') }}</th>
                                <th>{{ translate('Category')}}</th>
                                <th>{{ translate('Status')}}</th>
							</tr>
						</thead>
						@forelse($templates as $template)

							<tr class="@if($loop->even)@endif">
								<td class="d-none d-sm-flex align-items-center">{{$loop->iteration}}</td>
                                <td data-label="{{ translate('Name')}}">{{$template?->name}}</td>
                                <td data-label="{{ translate('Language Code')}}">{{$template?->language_code}}</td>
								<td data-label="{{ translate('Category')}}">{{$template?->category}}</td>
                                <td data-label="{{ translate('Status')}}">
                                    <span class="{{$template?->status == 'APPROVED' ? 'badge badge--success' : ($template?->status == 'REJECTED' ? 'badge badge--danger' : 'badge badge--info')}}">{{$template?->status}}</span>
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
					{{$templates->appends(request()->all())->onEachSide(1)->links()}}
				</div>
			</div>
		</div>
	</div>
</section>

@endsection



