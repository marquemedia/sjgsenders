@extends('admin.layouts.app')
@section('panel')
@push('style-include')
    <link rel="stylesheet" type="text/css" href="{{asset('assets/theme/admin/css/iconpicker/fontawesome-iconpicker.css')}}">
@endpush
    <section>
        @if(isset($sectionData['fixed_content']))

        <div class="card mb-3">
            <div class="card-header">
                <h4 class="card-title">{{ translate($title)}}</h4>
               
            </div>
            <div class="card-body">
                <div class="form-wrapper">

                    <form action="{{route('admin.frontend.sections.save.content', $section_key)}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="content_type" value="fixed_content">
                                <div class="row g-4 mb-4">
                                    @foreach($sectionData['fixed_content'] as $key => $item)
                                        @if($key === 'images')
                                            @foreach($item as $image_key => $file)
                                                <div class="col-md-6 ">
                                                    <div >
                                                        <label for="{{ $image_key }}" class="form-label">{{ __(setInputLabel($image_key)) }} <sup class="text--danger">*</sup></label>
                                                        <input type="file" class="form-control" id="{{ $image_key }}" name="images[{{ @$image_key }}]" value="{{ @$sectionFixedContent->section_value[$image_key] ?? '' }}" placeholder="{{ __(setInputLabel($key)) }}">
                                                        <small>{{translate('File formats supported: jpeg, jpg, png. The image will be resized to')}} {{$file['size'] ?? ''}} {{translate('pixels')}}.
                                                            <a href="{{showImage(filePath()['frontend']['path'].'/'. @$sectionFixedContent->section_value[$image_key],$file['size'])}}" target="__blank">{{translate('view image')}}</a>
                                                        </small>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="col-md-6">
                                                <div>
                                                    <label for="{{ $key }}" class="form-label">{{ __(setInputLabel($key)) }} <sup class="text--danger">*</sup></label>
                                                    @switch($item)
                                                        @case('icon')
                                                        <div class="input-group">
                                                            <input type="text" class="form-control iconpicker icon" autocomplete="off" name="{{ $key }}" required value="{{ $sectionFixedContent->section_value[$key] ?? '' }}" >
                                                            <span class="input-group-text input-group-addon" role="iconpicker"></span>
                                                        </div>
                                                            @break
                                                        @case('text')
                                                            <input type="text" class="form-control" id="{{ $key }}" name="{{ $key }}" value="{{ $sectionFixedContent->section_value[$key] ?? '' }}" placeholder="{{ __(setInputLabel($key)) }}" required>
                                                            @break
                                                        @case('textarea')
                                                            <textarea class="form-control" id="{{ $key }}" name="{{ $key }}" placeholder="{{ __(setInputLabel($key)) }}" required>{{ $sectionFixedContent->section_value[$key] ?? '' }}</textarea>
                                                            @break

                                                    @endswitch
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>

                            <button type="submit" class="i-btn primary--btn btn--md ">
                                {{translate("Submit")}}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        @endif

        @if(isset($sectionData['element_content']))
            <div class="card mt-4">
                <div class="card-header">
                    <h4 class="card-title"> {{ translate('Element Contents')}}</h4>
                    @if(isset($sectionData['element_content']))
                    <a href="{{route('admin.frontend.sections.element.content',$section_key)}}" class="i-btn primary--btn btn--md text-white">
                        <i class="fa-solid fa-plus"></i> {{translate('Add New')}}
                    </a>
                @endif
                </div>

                <div class="card-body px-0">
                    <div class="responsive-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>{{ translate('#') }}</th>
                                    @if (Illuminate\Support\Arr::has($sectionData, 'element_content.images'))
                                        <th>{{translate('Image')}}</th>
                                    @endif

                                    @foreach ($sectionData['element_content'] as $key => $typeItem)
                                        @if (in_array($typeItem, ['text', 'icon']))
                                            <th>{{ __(setInputLabel($key)) }}</th>
                                        @endif
                                    @endforeach

                                    <th>{{ translate('Action') }}</th>
                                </tr>
                            </thead>

                            @forelse ($elementContents as $element)
                                <tr class="@if ($loop->even)@endif">
                                    <td data-label="{{ translate('Name') }}">{{ $loop->iteration }}</td>

                                    @if (Illuminate\Support\Arr::has($sectionData, 'element_content.images'))
                                      
                                        <td data-label="{{ translate('Image') }}">
                                            <img src="{{showImage(filePath()['frontend']['path'].'/'. @getArrayValue(@$element->section_value, 'card_image'),'100x80')}}" class="w-100px">
                                        </td>
                                    @endif

                                    @foreach ($sectionData['element_content'] as $key => $typeItem)
                                        @if (in_array($typeItem, ['text', 'icon']))
                                            <td data-label="{{__(setInputLabel($key)) }}">
                                                @if ($typeItem == 'icon')
                                                    @php echo $element->section_value[$key] ?? '' @endphp
                                                @else
                                                    {{ $element->section_value[$key] ?? '' }}
                                                @endif
                                            </td>
                                        @endif
                                    @endforeach

                                    <td data-label="{{ translate('Action') }}">
                                        <div class="d-flex align-items-center justify-content-md-start justify-content-end gap-3">
                                            <a href="{{route('admin.frontend.sections.element.content',[$section_key,$element->id])}}" class="i-btn primary--btn btn--sm"><i class="la la-pencil-alt"></i></a>
                                            <a href="javascript:void(0)" class="i-btn danger--btn btn--sm remove-element"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#delete-element"
                                                    data-delete_id="{{$element->id}}"
                                                ><i class="las la-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">{{ translate('No Data Found') }}</td>
                                </tr>
                            @endforelse
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </section>

    

    <div class="modal fade" id="delete-element" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{route('admin.frontend.sections.element.delete')}}" method="POST">
                    @csrf
                    <input type="hidden" name="element_id" value="">
                    <div class="modal_body2">
                        <div class="modal_icon2">
                            <i class="las la-trash"></i>
                        </div>
                        <div class="modal_text2 mt-3">
                            <h6>{{ translate('Are you sure to delete this section element')}}</h6>
                        </div>
                    </div>
                    <div class="modal_button2 modal-footer">
                        <div class="d-flex align-items-center justify-content-center gap-3">
                            <button type="button" class="i-btn primary--btn btn--md" data-bs-dismiss="modal">{{ translate('Cancel')}}</button>
                            <button type="submit" class="i-btn danger--btn btn--md">{{ translate('Delete')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('script-include')
    <script src="{{ asset('assets/theme/admin/js/iconpicker/fontawesome-iconpicker.js') }}"></script>
@endpush

@push('script-push')
    <script>
        "use strict";
        $(document).ready(function() {
            const iconPicker = document.querySelector('.iconpicker');

            iconPicker.addEventListener('click', function() {
                const iconPopover = document.querySelector('.iconpicker-popover');
                iconPopover.style.display = 'contents';
            });


            $('.iconpicker').iconpicker().on('iconpickerSelected', function(e) {
                $(this).closest('.input-group').find('.iconpicker-input').val(`<i class="${e.iconpickerValue}"></i>`);
            });
        });
        $('.remove-element').on('click', function(){
            var modal = $('#delete-element');
            modal.find('input[name=element_id]').val($(this).data('delete_id'));
            modal.modal('show');
        });
    </script>
@endpush



