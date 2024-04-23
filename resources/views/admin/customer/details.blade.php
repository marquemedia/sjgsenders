@extends('admin.layouts.app')
@push('style-push')
    <style>
        .info-card-top::after {
            background-image: url("{{asset('assets/file/images/default/info-card.png')}}");
        }
    </style>
@endpush
@section('panel')
<section>
	<div class="row g-4">
		<div class="col-xxl-3 col-xl-4">
			<div class="card h-100">
                <div class="card-header">
                    <h4 class="card-title">{{ translate('User information')}}</h4>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="user--profile--image avatar-xl">
                            <img src="{{showImage(filePath()['profile']['user']['path'].'/'.$user->image)}}" alt="{{ translate('Profile Image')}}" class="rounded w-100 h-100">
                        </div>

                        <div>
                            <h5 class="mb-1">{{$user->name}}</h5>
                            <span>{{translate('Joining Date')}} {{getDateTime($user->created_at,'d M, Y h:i A')}}</span>
                        </div>
                    </div>

                    <ul class="list-group mt-4">
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <p><strong>{{translate('SMS Credit')}}:</strong></p>
                            <span>{{$user->credit}} {{ translate('credit')}}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <p><strong>{{ translate('Email Credit')}}:</strong></p>
                            <span>{{ $user->email_credit}} {{ translate('credit')}}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <p><strong>
                                {{translate('WhatsApp Credit')}}:
                            </strong></p>
                            <span> {{$user->whatsapp_credit}} {{ translate('credit')}}</span>
                        </li>

                         <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <p><strong>
                                {{translate('Email')}}:
                            </strong></p>
                            <span>{{$user->email}}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <p><strong>
                                {{ translate('Status')}}:
                            </strong></p>
                            @if($user->status == 1)
                                <span class="badge badge-pill bg--success">{{ translate('Active')}}</span>
                               @else
                                <span class="badge badge-pill bg--danger">{{ translate('Banned')}}</span>
                            @endif
                        </li>
                    </ul>
                </div>
        	</div>
        </div>

		<div class="col-xxl-9 col-xl-8">
            <div class="row g-4 dash-cards">
                <div class="col-md-6 col-xxl-6">
                    <div class="card">
                        <div class="info-card">
                            <div class="info-card-top">
                                <div class="info-card-header">
                                    <h4>{{ translate('SMS Statistics')}}</h4>
                                    <span>
                                        <i class="las la-comment"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="info-card-content">
                                <div class="info-inner-card">
                                    <div class="info-inner-content">
                                        <span class="bg--lite--info text--info">
                                            <i class="las la-address-book"></i>
                                        </span>

                                        <div>
                                            <h4>{{$logs['sms']['all']}}</h4>
                                            <p >{{ translate('Total')}}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="info-inner-card">
                                    <div class="info-inner-content">
                                        <span class="bg--lite--success text--success">
                                            <i class="las la-check-double"></i>
                                        </span>

                                        <div>
                                            <h4>{{$logs['sms']['success']}}</h4>
                                            <p >{{ translate('Success')}}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="info-inner-card">
                                    <div class="info-inner-content">
                                        <span class="bg--lite--warning text--warning ">
                                            <i class="las la-hourglass-half"></i>
                                        </span>
                                        <div>
                                            <h4>{{$logs['sms']['pending']}}</h4>
                                            <p >{{ translate('Pending')}}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="info-inner-card">
                                    <div class="info-inner-content">
                                        <span class="bg--lite--danger text--danger">
                                            <i class="las la-times-circle"></i>
                                        </span>
                                        <div>
                                            <h4>{{$logs['sms']['failed']}}</h4>
                                            <p >{{ translate('Failed')}}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xxl-6">
                    <div class="card">
                        <div class="info-card">
                            <div class="info-card-top">
                                <div class="info-card-header">
                                    <h4>{{ translate('Email Statistics')}}</h4>

                                    <span>
                                        <i class="las la-envelope-open"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="info-card-content">
                                <div class="info-inner-card">
                                    <div class="info-inner-content">
                                        <span class="bg--lite--info text--info">
                                            <i class="las la-address-book"></i>
                                        </span>

                                        <div>
                                            <h4>{{$logs['email']['all']}}</h4>
                                            <p >{{ translate('Total')}}</p>
                                        </div>

                                    </div>
                                </div>

                                <div class="info-inner-card">
                                    <div class="info-inner-content">
                                        <span class="bg--lite--success text--success">
                                            <i class="las la-check-double"></i>
                                        </span>

                                        <div>
                                            <h4>{{$logs['email']['success']}}</h4>
                                            <p >{{ translate('Success')}}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="info-inner-card">
                                    <div class="info-inner-content">
                                        <span class="bg--lite--warning text--warning ">
                                            <i class="las la-hourglass-half"></i>
                                        </span>
                                        <div>
                                            <h4>{{$logs['email']['pending']}}</h4>
                                            <p >{{ translate('Pending')}}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="info-inner-card">
                                    <div class="info-inner-content">
                                        <span class="bg--lite--danger text--danger">
                                            <i class="las la-times-circle"></i>
                                        </span>
                                        <div>
                                            <h4>{{$logs['email']['failed']}}</h4>
                                            <p >{{ translate('Failed')}}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xxl-6">
                    <div class="card">
                        <div class="info-card">
                            <div class="info-card-top">
                                <div class="info-card-header">
                                    <h4>{{ translate('Whatsapp Statistics')}}</h4>
                                    <span>
                                        <i class="lab la-whatsapp"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="info-card-content">
                                <div class="info-inner-card">
                                    <div class="info-inner-content">
                                        <span class="bg--lite--info text--info">
                                            <i class="las la-address-book"></i>
                                        </span>

                                        <div>
                                            <h4>{{$logs['whats_app']['all']}}</h4>
                                            <p >{{ translate('Total')}}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="info-inner-card">
                                    <div class="info-inner-content">
                                        <span class="bg--lite--success text--success">
                                            <i class="las la-check-double"></i>
                                        </span>

                                        <div>
                                            <h4>{{$logs['whats_app']['success']}}</h4>
                                            <p >{{ translate('Success')}}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="info-inner-card">
                                    <div class="info-inner-content">
                                        <span class="bg--lite--warning text--warning ">
                                            <i class="las la-hourglass-half"></i>
                                        </span>
                                        <div>
                                            <h4>{{$logs['whats_app']['pending']}}</h4>
                                            <p >{{ translate('Pending')}}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="info-inner-card">
                                    <div class="info-inner-content">
                                        <span class="bg--lite--danger text--danger">
                                            <i class="las la-times-circle"></i>
                                        </span>
                                        <div>
                                            <h4>{{$logs['whats_app']['failed']}}</h4>
                                            <p >{{ translate('Failed')}}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
		</div>

        <div class="col">
            <div class="card">
				<div class="card-header mb-20">
					<h4 class="card-title">{{ translate('Update your profile')}}</h4>
                    <div>
                        <button class="i-btn primary--btn btn--md" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">{{translate("Update Here")}}</button>
                    </div>
				</div>
                <div class="collapse" id="collapseExample">
                    <div class="card-body pt-0">
                        <form action="{{route('admin.user.update', $user->id)}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-wrapper">
                                <div class="row g-4">
                                    <div class="col-lg-6">
                                        <div class="form-item">
                                            <label for="name" class="form-label">{{ translate('Name')}} <sup class="text--danger">*</sup></label>
                                            <input type="text" name="name" id="name" class="form-control" value="{{@$user->name}}" placeholder="{{ translate('Enter Name')}}" required>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="form-item">
                                            <label for="email" class="form-label">{{ translate('Email')}} <sup class="text--danger">*</sup></label>
                                            <input type="text" name="email" id="email" class="form-control" value="{{@$user->email}}"  required>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="form-item">
                                            <label for="address" class="form-label">{{ translate('Address')}} <sup class="text--danger">*</sup></label>
                                            <input type="text" name="address" id="address" class="form-control" value="{{@$user->address->address}}" placeholder="{{ translate('Enter Address')}}" required>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="form-item">
                                            <label for="city" class="form-label">{{ translate('City')}} <sup class="text--danger">*</sup></label>
                                            <input type="text" name="city" id="city" class="form-control" value="{{@$user->address->city}}" placeholder="{{ translate('Enter City')}}" required>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="form-item">
                                            <label for="state" class="form-label">{{ translate('State')}} <sup class="text--danger">*</sup></label>
                                            <input type="text" name="state" id="state" class="form-control" value="{{@$user->address->state}}" placeholder="{{ translate('Enter State')}}" required>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="form-item">
                                            <label for="zip" class="form-label">{{ translate('Zip')}} <sup class="text--danger">*</sup></label>
                                            <input type="text" name="zip" id="zip" class="form-control" value="{{@$user->address->zip}}" placeholder="{{ translate('Enter Zip')}}" required>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-6">
                                        <div class="form-item">
                                            <label for="pricing_plan" class="form-label">{{ translate("User's Pricing Plan")}} <sup class="text--danger">*</sup></label>
                                            <select class="form-select" name="pricing_plan" id="pricing_plan">
                                                @foreach($pricing_plans as $identifier => $name)
                                                    <option value="{{ $identifier }}" @if($user->runningSubscription()?->currentPlan() && $user->runningSubscription()?->currentPlan()->id == $identifier) selected @endif>{{ $name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-6">
                                        <div class="form-item">
                                            <label for="status" class="form-label">{{ translate('Status')}} <sup class="text--danger">*</sup></label>
                                            <select class="form-select" name="status" id="status">
                                                <option value="1" @if($user->status == 1) selected @endif>{{ translate('Active')}}</option>
                                                <option value="2" @if($user->status == 2) selected @endif>{{ translate('Banned')}}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="i-btn primary--btn btn--lg">{{ translate('Submit')}}</button>
                        </form>
                    </div>
                </div>
			</div>
        </div>
	</div>
</section>
@endsection

