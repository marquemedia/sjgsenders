@extends('admin.layouts.app')
@section('panel')
@push('style-include')
    <link rel="stylesheet" type="text/css" href="{{asset('assets/theme/admin/css/iconpicker/fontawesome-iconpicker.css')}}">
@endpush
    <section>
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ translate($title) }}</h4>
            </div>

            <div class="card-body">
                <form action="{{route('admin.frontend.sections.save.content', $section_key)}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="content_type" value="element_content">
                    @if(@$frontendSectionElement)
                        <input type="hidden" name="id" value="{{$frontendSectionElement->id}}">
                    @endif
                    <div class="form-wrapper">
                        <div class="row g-4">
                            @foreach($sectionData['element_content'] ?? [] as $key => $item)
                                @if($key === 'images')
                                    @foreach($item as $image_key => $file)

                                        <div class="col-md-6">
                                            <div>
                                                <label for="{{ $image_key }}" class="form-label">{{ __(setInputLabel($image_key)) }} <sup class="text--danger">*</sup></label>
                                                <input type="file" class="form-control" id="{{ $image_key }}" name="images[{{ @$image_key }}]" value="{{ @$frontendSectionElement->section_value[$image_key] ?? '' }}" placeholder="{{ __(setInputLabel($key)) }}">
                                                <small>{{translate('File formats supported: jpeg, jpg, png. The image will be resized to')}} {{$file['size'] ?? ''}} {{translate('pixels')}}.
                                                    <a href="{{showImage(filePath()['frontend']['path'].'/'. @$frontendSectionElement->section_value[$image_key],$file['size'])}}" target="__blank">{{translate('view image')}}</a>
                                                </small>
                                            </div>
                                        </div>
                                    @endforeach
                                @else

                                    <div class="{{ $item == 'texteditor' ? 'col-12' : 'col-md-6'}}">
                                        <div class="position-relative">
                                            <label for="{{ $key }}" class="form-label">{{ __(setInputLabel($key)) }} <sup class="text--danger">*</sup></label>
                                            @switch($item)
                                                @case('icon')
                                                    <div class="input-group">
                                                        <input type="text" class="form-control iconpicker icon" autocomplete="off" name="{{ $key }}" required>
                                                        <span class="input-group-text input-group-addon" role="iconpicker"></span>
                                                    </div>
                                                    @break
                                                @case('text')
                                                    <input type="text" class="form-control" id="{{ $key }}" name="{{ $key }}" value="{{ $frontendSectionElement->section_value[$key] ?? '' }}" placeholder="{{ __(setInputLabel($key)) }}" required>
                                                    @break
                                                @case('textarea')
                                                    <textarea class="form-control" id="{{ $key }}" name="{{ $key }}" placeholder="{{ __(setInputLabel($key)) }}" required>{{ $frontendSectionElement->section_value[$key] ?? '' }}</textarea>
                                                    @break
                                                @case('texteditor')
                                                    <textarea class="form-control" id="{{ $item }}" name="{{ $key }}" placeholder="{{ __(setInputLabel($key)) }}" required>{{ $frontendSectionElement->section_value[$key] ?? '' }}</textarea>
                                                @break
                                            @endswitch
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    <button type="submit" class="i-btn primary--btn btn--md me-sm-3 me-1">
                        {{translate("Submit")}}
                    </button>
            </form>
            </div>
        </div>

    </section>
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
        $(document).ready(function() {
            CKEDITOR.ClassicEditor.create(document.getElementById("texteditor"), {
            placeholder: document.getElementById("texteditor").getAttribute("placeholder"),
            toolbar: {
            items: [
                'heading',
                'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', 'highlight', '|',
                'alignment', '|',
                'bold', 'italic', 'strikethrough', 'underline', 'subscript', 'superscript', 'removeFormat', 'findAndReplace', '-',
                'bulletedList', 'numberedList', '|',
                'outdent', 'indent', '|',
                'undo', 'redo',
                'link', 'insertImage', 'blockQuote', 'insertTable', 'mediaEmbed', '|',
                'horizontalLine', 'pageBreak', '|',
                'sourceEditing'
            ],
            shouldNotGroupWhenFull: true
            },
            list: {
            properties: {
                styles: true,
                startIndex: true,
                reversed: true
            }
            },
            heading: {
            options: [
                { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
                { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
            ]
            },
            fontFamily: {
                options: [
                    'default',
                    'Arial, Helvetica, sans-serif',
                    'Courier New, Courier, monospace',
                    'Georgia, serif',
                    'Lucida Sans Unicode, Lucida Grande, sans-serif',
                    'Tahoma, Geneva, sans-serif',
                    'Times New Roman, Times, serif',
                    'Trebuchet MS, Helvetica, sans-serif',
                    'Verdana, Geneva, sans-serif'
                ],
                supportAllValues: true
            },
            fontSize:
            {
                options: [10, 12, 14, 'default', 18, 20, 22],
                supportAllValues: true
            },
            htmlSupport: {
            allow: [
                    {
                    name: /.*/,
                    attributes: true,
                    classes: true,
                    styles: true
                    }
                ]
            },
            htmlEmbed: { showPreviews: true },
            link: {
                decorators: {
                        addTargetToExternalLinks: true,
                        defaultProtocol: 'https://',
                        toggleDownloadable: {
                            mode: 'manual',
                            label: 'Downloadable',
                            attributes: {
                                download: 'file'
                            }
                        }
                    }
            },
            mention: {
                feeds: [
                    {
                    marker: '@',
                    feed: [
                        '@apple', '@bears', '@brownie', '@cake', '@cake', '@candy', '@canes', '@chocolate', '@cookie', '@cotton', '@cream',
                        '@cupcake', '@danish', '@donut', '@dragée', '@fruitcake', '@gingerbread', '@gummi', '@ice', '@jelly-o',
                        '@liquorice', '@macaroon', '@marzipan', '@oat', '@pie', '@plum', '@pudding', '@sesame', '@snaps', '@soufflé',
                        '@sugar', '@sweet', '@topping', '@wafer'
                    ],
                    minimumCharacters: 1
                    }
                ]
            },
            removePlugins: [
            'CKBox',
            'CKFinder',
            'EasyImage',
            'RealTimeCollaborativeComments',
            'RealTimeCollaborativeTrackChanges',
            'RealTimeCollaborativeRevisionHistory',
            'PresenceList',
            'Comments',
            'TrackChanges',
            'TrackChangesData',
            'RevisionHistory',
            'Pagination',
            'WProofreader',
            'MathType'
            ]
            });
        });
    </script>
@endpush
