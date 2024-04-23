@extends('admin.layouts.app')
@section('panel')
<section>
    <div class="container-fluid p-0">
		<div class="table_heading d-flex align--center justify--between">
			<nav  aria-label="breadcrumb">
				  <ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="{{ route("admin.sms.gateway.sms.api") }}"> {{translate('SMS & Whatsapp')}}</a></li>
					<li class="breadcrumb-item"><a href="{{ route("admin.gateway.whatsapp.device") }}"> {{translate('Whatsapp Devices')}}</a></li>
					<li class="breadcrumb-item"><a href="{{ request()->route()->getName() }}"> {{translate('WhatsApp API Templates')}}</a></li>
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

<div class="modal fade" id="demotemplate" tabindex="-1" aria-labelledby="demotemplateLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
            <div class="template-body">
                <div class="template-content">
                    <h3>Test One</h3>
                    <p class="mb-2">When backdrop is set to static, the modal will not close when clicking outside of it. Click the button below to try it.</p>
                    <span>
                        WhatsApp Business Platform sample message
                    </span>

                    <div>
                        <button>test</button>
                        <button>Demo Statice Site</button>
                        <button>See all options</button>
                    </div>
                </div>
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>

@endsection



