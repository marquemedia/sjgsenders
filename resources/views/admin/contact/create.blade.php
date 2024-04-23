@extends('admin.layouts.app')
@section('panel')
<div class="container-fluid p-0">
    <div class="card">
        <div class="card-body">
            <form id="contact_store" action="{{route('admin.contact.store')}}" method="POST">
                @csrf

                <div class="form-wrapper">
                        <div class="form-wrapper-title">
                            <h6>{{translate($title)}}</h6>
                        </div>

                    <div class="form-item mb-4">
                        <label class="form-label" for="group_id">{{ translate('Select a Group')}} <a href="{{ route("admin.contact.group.index") }}" target="_blank"><i style="font-size:15px" class="text--primary las la-external-link-alt"></i></a></label>
                        <select class="form-select" name="group_id" id="group_id" required>
                            <option selected disabled="">{{ translate('Select One')}}</option>
                            @foreach($groups as $id => $name)
                                <option value="{{$id}}">{{$name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="file-tab">
                        <ul class="nav nav-tabs gap-2 mb-4" id="myTabContent" role="tablist">
                            <li class="nav-item single-contact" role="presentation">
                                <button title="{{ translate("You can add a single contact by filling up the form below and submit it to save the contact") }}" class="nav-link active" id="single-tab" data-bs-toggle="tab" data-bs-target="#single-tab-pane" type="button" role="tab" aria-controls="single-tab-pane" aria-selected="true"><i class="las la-id-card"></i> {{ translate('Store Single Contact') }}</button>
                            </li>
                            <li class="nav-item upload-excel" role="presentation">
                                <button class="nav-link" id="upload-tab" data-bs-toggle="tab" data-bs-target="#upload-tab-pane" type="button" role="tab" aria-controls="upload-tab-pane" aria-selected="false"><i class="las la-copy"></i> {{ translate('Import Csv/Excel Files') }}</button>
                            </li>
                        </ul>

                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="single-tab-pane" role="tabpanel" aria-labelledby="single-tab" tabindex="0">
                                <input hidden type="text" name="single_contact">
                                <div class="row">
                                    <div class="form-item col-lg-6 mb-3">
                                        <label for="first_name" class="form-label"> {{ translate('First Name')}} <sup class="text--danger">*</sup></label>
                                        <input type="text" value="{{ old("first_name") }}" class="form-control" id="first_name" name="first_name" placeholder=" {{ translate('Enter Contact First Name')}}">
                                    </div>
                                    <div class="form-item col-lg-6 mb-3">
                                        <label for="last_name" class="form-label"> {{ translate('Last Name')}} <sup class="text--danger">*</sup></label>
                                        <input type="text" value="{{ old("last_name") }}" class="form-control" id="last_name" name="last_name" placeholder=" {{ translate('Enter Contact Last Name')}}">
                                    </div>
                                    <div class="form-item col-lg-6 mb-3">

                                        <label for="whatsapp_contact" class="form-label"> {{ translate('WhatsApp Number')}}
                                            <p class="relative-note">{{ translate("Provide the country code along with the number")}}</p>
                                        </label>

                                        <input type="number" value="{{ old("whatsapp_contact") }}" class="form-control" id="whatsapp_contact" name="whatsapp_contact" placeholder=" {{ translate('Enter Contact WhatsApp number')}}">
                                    </div>
                                    <div class="form-item col-lg-6 mb-3">
                                        <label for="sms_contact" class="form-label"> {{ translate('SMS Number')}}
                                            <p class="relative-note">{{ translate("Provide the country code along with the number")}}</p>
                                        </label>
                                        <input type="number" value="{{ old("sms_contact") }}" class="form-control" id="sms_contact" name="sms_contact" placeholder=" {{ translate('Enter Contact SMS number')}}">
                                    </div>
                                    <div class="form-item col-lg-6 mb-3">
                                        <label for="email_contact" class="form-label"> {{ translate('Email Account')}}</label>
                                        <input type="email" value="{{ old("email_contact") }}" class="form-control" id="email_contact" name="email_contact" placeholder=" {{ translate('Enter Contact Email Account')}}">
                                    </div>
                                    <div class="form-item col-lg-6">
                                        <label for="status" class="form-label"> {{ translate('Status')}} <sup class="text--danger">*</sup></label>
                                        <select class="form-select" name="status" id="status" required>
                                            <option value="1"> {{ translate('Active')}}</option>
                                            <option value="2"> {{ translate('Banned')}}</option>
                                        </select>
                                    </div>
                                </div>
                                @if(count($filteredAttributes) != 0)
                                    <div class="form-wrapper my-3 border--dark">
                                        <div class="row">
                                            <div class="form-wrapper-title"> {{ translate("Custom Attribute Data")}} <a href="{{ route("admin.contact.settings.index") }}" target="_blank"><i style="font-size:15px" class="text--primary las la-external-link-alt"></i></a></div>
                                            @foreach($filteredAttributes as $attribute_key => $attribute_value)
                                              
                                                <div class="form-item col-lg-6 mb-3">
                                                    <label for="{{ $attribute_key }}" class="form-label"> {{ translate(textFormat(["_"], $attribute_key))}}</label>
                                                    @if($attribute_value->type == \App\Models\GeneralSetting::DATE)
                                                        <input type="date" value="{{ old($attribute_key) }}" class="form-control mb-3 flatpicker" id="{{ $attribute_key }}" name="attributes[{{ $attribute_key."::".$attribute_value->type }}]" placeholder=" {{ translate('Choose Contact ').textFormat(["_"], $attribute_key)}}">
                                                    @elseif($attribute_value->type == \App\Models\GeneralSetting::BOOLEAN)
                                                        <select class="form-select mb-3" name="attributes[{{ $attribute_key."::".$attribute_value->type }}]" id="{{ $attribute_key }}" required>
                                                            <option selected disabled> {{ translate('-- Select An Option --')}}</option>
                                                            <option value="true"> {{ translate('Yes')}}</option>
                                                            <option value="false"> {{ translate('No')}}</option>
                                                        </select>
                                                    @elseif($attribute_value->type == \App\Models\GeneralSetting::NUMBER)
                                                        <input type="number" value="{{ old($attribute_key) }}" class="form-control mb-3" id="{{ $attribute_key }}" name="attributes[{{ $attribute_key."::".$attribute_value->type }}]" placeholder=" {{ translate('Enter Contact ').textFormat(["_"], $attribute_key)}}">
                                                    @else
                                                        <input type="text" value="{{ old($attribute_key) }}" class="form-control mb-3" id="{{ $attribute_key }}" name="attributes[{{ $attribute_key."::".$attribute_value->type }}]" placeholder=" {{ translate('Enter Contact ').textFormat(["_"], $attribute_key)}}">
                                                    @endif
                                                </div>

                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <div class="form-item my-3">
                                        <a href="{{ route("admin.contact.settings.index") }}" class="i-btn primary--btn btn--md">{{ translate('Add More Attributes')}}</a>

                                    </div>
                                @endif

                            </div>

                            <div class="tab-pane fade" id="upload-tab-pane" role="tabpanel" aria-labelledby="upload-tab" tabindex="0">
                                <input hidden type="text" name="import_contact">
                                <input hidden id="send_add_new_row" name="new_row">
                                <input hidden id="send_header_location" name="location[]">
                                <input hidden id="send_header_value" name="value[]">
                                <input hidden id="file__name" name="file__name">
                                
                                <div class="row">
                                    <div class="col-lg-8 mb-4">
                                        <div class="d-flex gap-3 flex-column ">
                                            <div>
                                                <div class="form-text mb-3">
                                                    {{ translate('Download Sample: ')}}
                                                    <a href="{{ route("admin.contact.demo.file", "csv" ) }}" class="badge badge--primary"><i class="fa fa-download" aria-hidden="true"></i>{{translate('csv')}}</a>
                                                    {{-- <a href="{{ route("admin.contact.demo.file", "xlsx" ) }}" class="badge badge--primary"><i class="fa fa-download" aria-hidden="true"></i> {{ translate('xlsx')}}</a> --}}
                                                </div>
                                                <div class="upload-filed">
                                                    <input type="file" id="file_upload" name="file" id="file">

                                                    <label for="file_upload" class="uplaod-file">
                                                        <div class="d-flex align-items-center gap-3">
                                                            <span class="upload-drop-file">
                                                                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" x="0" y="0" viewBox="0 0 128 128" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path fill="#f6f0ff" d="M99.091 84.317a22.6 22.6 0 1 0-4.709-44.708 31.448 31.448 0 0 0-60.764 0 22.6 22.6 0 1 0-4.71 44.708z" opacity="1" data-original="#f6f0ff" class=""></path><circle cx="64" cy="84.317" r="27.403" fill="#6009f0" opacity="1" data-original="#6009f0" class=""></circle><g fill="#f6f0ff"><path d="M59.053 80.798v12.926h9.894V80.798h7.705L64 68.146 51.348 80.798zM68.947 102.238h-9.894a1.75 1.75 0 0 1 0-3.5h9.894a1.75 1.75 0 0 1 0 3.5z" fill="#f6f0ff" opacity="1" data-original="#f6f0ff" class=""></path></g></g></svg>
                                                            </span>
                                                            <span class="upload-browse">{{ translate("Upload CSV/Excel File Here ") }}</span>
                                                        </div>
                                                    </label>
                                                    <div class="file__info d-none"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>

                <div class="d-flex align-items-end justify-content-end gap-3">
                    <button type="button" class="i-btn danger--btn btn--md" data-bs-dismiss="modal"> {{ translate('Cancel')}}</button>

                    <button type="submit" class="i-btn primary--btn btn--md"> {{ translate('Submit')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

{{-- Modal for imported contact --}}
<div class="modal fade" id="updateTableData" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            
            <div class="modal-header">
                <h5 class="modal-title">{{ translate('Edit Headers Of Your File')}}</h5>
                <button type="button" class="i-btn bg--lite--danger text--danger btn--sm" data-bs-dismiss="modal"> <i class="las la-times"></i></button>
            </div>
           <div class="modal-body">
               
                <input hidden type="text" name="import_contact">

                <div class="headers">
                    
                </div>
           </div>

            <div class="modal-footer">
                <div class="d-flex align-items-center gap-3">
                    <button type="button" class="i-btn danger--btn btn--md" data-bs-dismiss="modal"> {{ translate('Cancel')}}</button>
                    <button type="button" class="i-btn saveChanges info--btn btn--md"> {{ translate('Save')}}</button>
                </div>
            </div>
            
        </div>
    </div>
</div>

@push('script-push')
<script>
	(function($){
		"use strict";
        var GlobalColumnName = [];
        
        $(document).ready(function() {

            $(".flatpicker").flatpickr();

            $('#contact_store').submit(function (event) {
                var activeTab = $(this).find('.tab-pane.show.active');

                if (activeTab.attr('id') === 'single-tab-pane') {

                    $('input[name="single_contact"]', this).val('true');
                    $('input[name="import_contact"]', this).val('false');

                } else if (activeTab.attr('id') === 'upload-tab-pane') {

                    $('input[name="single_contact"]', this).val('false');
                    $('input[name="import_contact"]', this).val('true');
                }
            });

            function createFileHtmlBlock(file, uploadTime) {
                var iconClass;

                if (file.type === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                    iconClass = 'las la-file-excel';
                } else if (file.type === 'application/vnd.ms-excel' || file.type === 'text/csv') {
                    iconClass = 'las la-file-csv';
                } else {
                    iconClass = 'las la-file';
                }

                return `
                    <div class="d-flex align-items-center flex-wrap gap-3">
                        <div class="fs-1">
                            <i style="font-size:64px" class="${iconClass}"></i>
                        </div>
                        <div class="d-flex flex-column align-items-start gap-1">
                            <p title="${file.name}" class="fw-normal">File Name: ${file.name}</p>
                            <small title="${file.type}">File Type: ${file.type}</small>
                            <small title="${bytesToSize(file.size)}">File Size: ${bytesToSize(file.size)}</small>
                            <small title="${uploadTime}">Upload Time: ${uploadTime}</small>
                        </div>
                        <span class="edit__file">
                            <i class="las la-pen"></i>
                        </span>
                        <span class="remove__file">
                            <i class="las la-times"></i>
                        </span>
                       
                    </div>
                    <div class="mt-3 progress" role="progressbar" aria-label="Animated striped example" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 0%"></div>
                    </div>
                    `;
            }

            function deleteFile(fileName, csrfToken) {
                return $.ajax({
                    url: '{{ route("admin.contact.delete.file") }}',
                    type: 'POST',
                    data: {
                        file_name: fileName,
                    },
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,

                    },
                    xhr: function () {

                        var xhr = new window.XMLHttpRequest();

                        xhr.upload.addEventListener("progress", function (evt) {
                            if (evt.lengthComputable) {
                                var percentComplete = (evt.loaded / evt.total) * 100;
                                
                                $(".progress-bar").css("width", percentComplete + "%");
                            }
                        }, false);

                        return xhr;
                    },
                });
            }

            $(document).on('click', '.removeColumn', function () {

                $(this).closest('.columnData').remove();
            });
                        
            function keyMappingModal(data) {

                var progressBar = $(".progress").addClass("d-none");
                var modal = $('#updateTableData');
                var container = $(".switch-container");

                if (container.length === 0) {
                    var html = `<div class="container">
                                    <div class="mt-3 switch-container">
                                        <label title="{{ translate("If your excel/csv file do not containe any header and the 1st row contains data \nin it then you should toggle on this option") }}" class="form-check-label" for="add_new_row">{{translate('Enable this toggle to add this header setup as a new row')}}</label>
                                        <label class="switch">
                                            <input type="checkbox" value="true" name="add_new_row" type="checkbox" id="add_new_row">
                                            <span class="slider"></span>
                                        </label>
                                    </div>
                                </div>`;
                    $(".your-container-selector").append(html);
                } else {
                    var html = `<div class="container">`;
                }

                $.each(data, function(cell, header) {
                   
                    var formattedHeader = header.toString().replace(/_/g, ' ').replace(/\w\S*/g, function(txt) {
                        return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
                    });
                    html += `
                        <div class="row g-3 my-3 columnData">
                            <div class="col-lg-3 location">
                                <input type="text" class="form-control text-uppercase text-center" value="${cell}" placeholder="${cell.toUpperCase()}" readonly="true">
                                <input hiddden type="text" name="header_location[]"  value="${header}" hidden>
                            </div>
                            <div class="col-lg-6 col-sm-8 value">
                                <select class="form-select select-attribute" name="header_value[]" aria-label="Large select example">
                                    <option class="custom-attribute" value="${header}::4" selected>${formattedHeader}</option> 
                                    <option ${header == "first_name" ? 'selected' : ''} value="first_name::4">First Name</option>     
                                    <option ${header == "last_name" ? 'selected' : ''} value="last_name::4">Last Name</option>  
                                    <option ${header == "whatsapp_contact" ? 'selected' : ''} value="whatsapp_contact::4">Whatsapp Contact</option>     
                                    <option ${header == "email_contact" ? 'selected' : ''} value="email_contact::4">Email Contact</option>   
                                    <option ${header == "sms_contact" ? 'selected' : ''} value="sms_contact::4">SMS Contact</option>
                                    @forelse($filteredAttributes as $attributeName => $attribute)
                                        <option  ${header == "{{$attributeName}}" ? 'selected' : ''} value="{{ $attributeName.'::'.$attribute->type }}">{{ucfirst(str_replace('_', ' ', $attributeName))}}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                            <div class="type d-none">
                                
                            </div>
                            <div class="col-lg-3 col-sm-4 d-flex align-items-center justify-content-md-start justify-content-end gap-3">
                                <span class="input-group-btn">
                                    <button class="i-btn primary--btn btn--md text--light editColumn" data-cell="${cell}" data-header="${header}" type="button">
                                        <i class="las la-pen"></i>
                                    </button>
                                </span>
                                <span class="input-group-btn">
                                    <button class="i-btn danger--btn btn--md text--light removeColumn" type="button">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </span>
                            </div>
                        </div>`;
                }); 

                html += `</div>`;
                $('.headers').append(html);
                modal.modal('show');
            };

            $(document).on('click', '.editColumn', function () {

                var columnData = $(this).closest(".columnData");
               
                if (columnData.hasClass("editing")) {

                    var dataHeader = $(this).data('header');
                    var selectElement = generateSelectElement(dataHeader);
                    var currentValue = columnData.find(".value input").val();
                    var convertedValue = currentValue.toString().replace(/_/g, ' ').replace(/\w\S*/g, function(txt) {
                        return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
                    });

                    columnData.find(".value input").replaceWith(selectElement);
                    columnData.find(".value select").val(convertedValue);
                    var select = columnData.find(".type").removeClass("d-none").addClass("col-lg-2 col-sm-4 ").append(`
                        <select class="form-select" name="type[]" aria-label="Large select example">
                            <option selected disabled>Type</option>
                            <option value="1">Date</option>
                            <option value="3">Number</option>
                            <option value="4">Text</option>
                        </select>
                    `);
                    columnData.removeClass("editing");
                    $(this).removeClass("info--btn").addClass("primary--btn");
                    $(this).find("i").removeClass("la-undo-alt").addClass("la-pen");
                } else {
                    
                    var formattedValue = columnData.toString().replace(/_/g, ' ').replace(/\w\S*/g, function(txt) {
                        return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
                    });

                    columnData.find(".value select").replaceWith(function () {
                        return $("<input>").attr({
                            type: "text",
                            name: "custom_name[]",
                            class: "form-control",
                            value: "",  
                            placeholder: "Enter attribute name"
                        });
                    });

                    columnData.find(".type").addClass("d-none").removeClass("col-lg-2 col-sm-4 ").empty();
                    columnData.addClass("editing");
                    $(this).removeClass("primary--btn").addClass("info--btn");
                    $(this).find("i").removeClass("la-pen").addClass("la-undo-alt");
                    
                }

                columnData.find(".location").toggleClass('col-lg-3 col-lg-2');
                columnData.find(".value").toggleClass('col-lg-6 col-sm-8 col-lg-5 col-sm-7');

                var select = columnData.find(".type").toggleClass("d-none").addClass("col-lg-2 col-sm-4 ").append(`
                    <select class="form-select" name="type[]" aria-label="Large select example">
                        <option selected disabled>Type</option>
                        <option value="1">Date</option>
                        <option value="3">Number</option>
                        <option value="4">Text</option>
                    </select>
                `);
            });

            function generateSelectElement(header) {
                var formattedHeader = header.toString().replace(/_/g, ' ').replace(/\w\S*/g, function(txt) {
                    return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
                });

                var selectOptions = `
                    <option class="custom-attribute" value="${header}::4" selected>${formattedHeader}</option>
                    <option ${header == "first_name" ? 'selected' : ''} value="first_name::4">First Name</option>
                    <option ${header == "last_name" ? 'selected' : ''} value="last_name::4">Last Name</option>
                    <option ${header == "whatsapp_contact" ? 'selected' : ''} value="whatsapp_contact::4">Whatsapp Contact</option>
                    <option ${header == "email_contact" ? 'selected' : ''} value="email_contact::4">Email Contact</option>
                    <option ${header == "sms_contact" ? 'selected' : ''} value="sms_contact::4">SMS Contact</option>
                    @forelse($filteredAttributes as $attributeName => $attribute)
                        <option ${header == "{{$attributeName}}" ? 'selected' : ''} value="{{ $attributeName.'::'.$attribute->type }}">{{ucfirst(str_replace('_', ' ', $attributeName))}}</option>
                    @empty
                    @endforelse
                `;

                return `<select class="form-select select-attribute" name="header_value[]" aria-label="Large select example">${selectOptions}</select>`;
            }

            $(document).on('click', '.saveChanges', function () {
                var newRow = $('#add_new_row').is(':checked') ? 'true' : 'false';

                var headerLocation = [];
                var headerValue = [];

                $('.columnData').each(function () {
                    
                    var location = $(this).find("input[name='header_location[]']").val();
                    var value;
                    if ($(this).find("input[name='custom_name[]']").length > 0) {
                        
                        value = $(this).find("input[name='custom_name[]']").val().replace(/\s+/g, '_').toLowerCase()+'::'+$(this).find("select[name='type[]']").val();
                        
                    } else if ($(this).find("select[name='header_value[]']").length > 0) {
                        value = $(this).find("select[name='header_value[]']").val();
                    } else {
                        value = null;
                    }

                    headerLocation.push(location);
                    headerValue.push(value);
                });

                var hasInvalidValue = false;
                var seen = {};
                var duplicateValue = false;

                $.each(headerValue, function(index, value) {

                    if (value === "::null" || /^.+::null$/.test(value)  || value === "::undefined" || /^.+::undefined$/.test(value)  || value === "::" || value === "null::null" ||
                    value === "::5" || value === "::6" || value === "::7" || 
                    value === "::8" || value === "::9" || /^.+::[5-9]$/.test(value) || value.indexOf("::") === -1) {

                        hasInvalidValue = true;
                    }

                    var parts = value.split("::");
                    var key = parts[0];

                    if (seen[key]) {
                        duplicateValue = true;
                        return false; 
                    }
                    seen[key] = true;
                });

                if (hasInvalidValue) {

                    notify("error", "Please Make Sure that no field is empty of invalid.");

                } else if(duplicateValue) {
                    
                    notify("error", "Duplicate Column Name Detected, column names can not be same.");
                } else {

                    $('#send_add_new_row').val(newRow);
                    $('#send_header_location').val(headerLocation.join(','));
                    $('#send_header_value').val(headerValue.join(','));
                    $('#updateTableData').modal('hide');
                }
            });


            $("#file_upload").change(function () {

                var fileInput = $(this)[0];
                var file = fileInput.files[0];
                var formData = new FormData();
                formData.append('file', file);
                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url: '{{ route("admin.contact.upload.file") }}',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    success: function (response) {
                        if (response.status) {
                            $(".upload_file_loader").removeClass("d-none");
                            var uploadTime = new Date().toLocaleString();
                            var htmlBlock = createFileHtmlBlock(file, uploadTime);
                            var fileName = response.file_name;
                            var filePath = response.file_path;
                            $("input[name='file__name']").val(fileName);
                            $(".uplaod-file").addClass("d-none");
                            $(".file__info").removeClass("d-none").html(htmlBlock);


                            var reader = new FileReader();
                            
                            reader.onload = function (e) {
                                var fileData = e.target.result;
                                var fileType = file.type;
                               
                                $.ajax({
                                    url: '{{ route("admin.contact.parse.file") }}',
                                    type: 'POST',
                                    data: {
                                        fileType: fileType,
                                        filePath: filePath
                                    },
                                    headers: {
                                        'X-CSRF-TOKEN': csrfToken,
                                    },
                                    xhr: function () {

                                        var xhr = new window.XMLHttpRequest();

                                        xhr.upload.addEventListener("progress", function (evt) {
                                            if (evt.lengthComputable) {
                                                var percentComplete = (evt.loaded / evt.total) * 100;
                                                
                                                $(".progress-bar").css("width", percentComplete + "%");
                                            }
                                        }, false);

                                        return xhr;
                                    },
                                    success: function (response) {

                                        $("input[name='import_contact']").val('true');
                                       
                                        keyMappingModal(response.data);
                                        
                                    },
                                    error: function (error) {
                                        console.error('Error parsing file on the server:', error);
                                    }
                                });
                            };
                            reader.readAsText(file);

                            $(".edit__file").on("click", function(){
                                keyMappingModal(response.data);
                            })
                            $(".remove__file").on("click", function () {
                                deleteFile(fileName, csrfToken)
                                    .done(function (deleteResponse) {
                                        $('input[name="import_contact"]').val('false');
                                        if (deleteResponse.status) {
                                            var progressBar = $(".progress").removeClass("d-none");
                                            $(".headers").empty()
                                            handleDeleteSuccess();
                                        } else {
                                            handleDeleteError();
                                        }
                                    })
                                    .fail(function (deleteError) {
                                        handleDeleteError();
                                    })
                                    .always(function () {
                                        $("#dynamic_table").empty();
                                    });
                            });
                        } else {
                            console.error('Error uploading file.');
                        }
                    },
                    error: function (error) {
                        console.error('Error uploading file:', error);
                    }
                });
                
            });

            function handleDeleteSuccess() {
                $(".upload_file_loader").addClass("d-none");
                $(".table-preloader").removeClass("d-none");
                $("#file_upload").val("");
                $(".file__info").addClass("d-none");
                $(".uplaod-file").removeClass("d-none");
            }

            function handleDeleteError() {
                $(".upload_file_loader").addClass("d-none");
                $("#file_upload").val("");
                $(".file__info").addClass("d-none");
                $(".uplaod-file").removeClass("d-none");
                console.error('Error deleting file.');
            }

            function bytesToSize(bytes) {
                var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
                if (bytes == 0) return '0 Byte';
                var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
                return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
            }
        });
        
	})(jQuery);
</script>
@endpush


