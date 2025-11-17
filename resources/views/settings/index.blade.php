@extends('layouts.app')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
    <div class="contents">
        <div class="crm mb-25">
            <div class="container-fluid">
                <div class="row">
                    @include('layouts.breadcumb')
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="contact-list-wrap mb-25">

                            <div class="d-flex justify-content-between mb-2">
                                <div class="action-btn">
                                    <h4 class="text-capitalize fw-500 breadcrumb-title">{{ $data['title'] }}</h4>
                                </div>
                            </div>

                            <div class="contact-list radius-xl w-100">

                                @include('alerts.success')
                                @include('alerts.errors')
                                @include('alerts.error')

                                <div class="table-responsive table-responsive--dynamic">

                                    <table class="table-borderless table-rounded mb-0 table" id="settings-table">
                                        <thead>
                                            <tr>
                                                <th>S/n</th>
                                                <th>Key</th>
                                                <th>Value</th>
                                                <th>Description</th>
                                                @can('Edit System Settings')
                                                    <th>Actions</th>
                                                @endcan
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($settings as $index => $setting)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ ucwords(str_replace('_', ' ', $setting->key)) }}</td>
                                                    <td>
                                                        @if ($setting->key === 'site_logo' && $setting->value)
                                                            <img src="{{ Storage::url($setting->value) }}" alt="Logo" style="max-height: 50px;">
                                                        @else
                                                            {{ $setting->value }}
                                                        @endif
                                                    </td>
                                                    <td>{{ $setting->description }}</td>
                                                    @can('Edit System Settings')
                                                        <td>
                                                            <button class="btn btn-warning btn-xs btn-squared btn-transparent-warning" data-bs-toggle="modal" data-bs-target="#editModal" data-id="{{ $setting->id }}" data-key="{{ $setting->key }}" data-value="{{ $setting->value }}" data-description="{{ $setting->description }}">Edit</button>
                                                        </td>
                                                    @endcan
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @can('Edit System Settings')
        @include('settings.edit')
    @endcan
@endsection

@push('page_scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            var defaultPageLength = {{ getSetting('default_page_length', 10) }};

            $('#settings-table').DataTable({
                pageLength: defaultPageLength,
                order: [[1, 'asc']],
                autoWidth: false,
                responsive: true,
                columnDefs: [{
                        width: '5%',
                        targets: 0
                    },
                    {
                        width: '20%',
                        targets: 1
                    },
                    {
                        width: '20%',
                        targets: 2
                    },
                    {
                        width: '45%',
                        targets: 3
                    },
                    @can('Edit System Settings')
                        {
                            width: '10%',
                            targets: 4
                        }
                    @endcan
                ]
            });


            $('#editModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');
                var key = button.data('key');
                var value = button.data('value');
                var description = button.data('description');

                var modal = $(this);
                modal.find('#setting-id').val(id);
                modal.find('#setting-key').val(key);
                modal.find('#setting-description').val(description);

                if (key === 'time_format') {
                    var selectInput = `<label for="setting-value" class="form-label">Value <span style="color: red">*</span></label>
                    <select id="time_format" name="value" class="form-control ih-medium ip-gray radius-xs b-light px-15 select2" autocomplete="off">
                        <option value="">Select</option>
                        <option value="H:i:s" {{ getSetting('time_format') === 'H:i:s' ? 'selected' : '' }}>24 Hour (14:30:00)</option>
                        <option value="h:i A" {{ getSetting('time_format') === 'h:i A' ? 'selected' : '' }}>12 Hour (02:30 PM)</option>
                        <option value="H:i" {{ getSetting('time_format') === 'H:i' ? 'selected' : '' }}>24 Hour (14:30)</option>
                        <option value="h:i:s A" {{ getSetting('time_format') === 'h:i:s A' ? 'selected' : '' }}>12 Hour (02:30:00 PM)</option>
                        <option value="g:i A" {{ getSetting('time_format') === 'g:i A' ? 'selected' : '' }}>12 Hour (2:30 PM)</option>
                    </select>`;
                    modal.find('#value-input-container').html(selectInput);
                } else if (key === 'date_format') {
                    var selectInput = `<label for="date_format" class="form-label">Value <span style="color: red">*</span></label>
                    <select id="date_format" name="value" class="form-control ih-medium ip-gray radius-xs b-light px-15 select2" autocomplete="off">
                        <option value="">Select</option>
                        <option value="Y-m-d" {{ getSetting('date_format') === 'Y-m-d' ? 'selected' : '' }}>Y-m-d (2024-06-22)</option>
                        <option value="d/m/Y" {{ getSetting('date_format') === 'd/m/Y' ? 'selected' : '' }}>d/m/Y (22/06/2024)</option>
                        <option value="m-d-Y" {{ getSetting('date_format') === 'm-d-Y' ? 'selected' : '' }}>m-d-Y (06-22-2024)</option>
                        <option value="F j, Y" {{ getSetting('date_format') === 'F j, Y' ? 'selected' : '' }}>F j, Y (June 22, 2024)</option>
                        <option value="D, M j, Y" {{ getSetting('date_format') === 'D, M j, Y' ? 'selected' : '' }}>D, M j, Y (Sat, Jun 22, 2024)</option>
                    </select>`;
                    modal.find('#value-input-container').html(selectInput);
                } else if (key === 'backup_schedule_hour') {
                    var timeInput = `<label for="time_picker" class="form-label">Value <span style="color: red">*</span></label>
                    <select id="time_picker" name="value" class="form-control ih-medium ip-gray radius-xs b-light px-15 select2" autocomplete="off">
                        <option value="">Select</option>
                        <option value="12:00 AM" {{ getSetting('backup_schedule_hour') === '12:00 AM' ? 'selected' : '' }}>12:00 AM</option>
                        <option value="01:00 AM" {{ getSetting('backup_schedule_hour') === '01:00 AM' ? 'selected' : '' }}>01:00 AM</option>
                        <option value="02:00 AM" {{ getSetting('backup_schedule_hour') === '02:00 AM' ? 'selected' : '' }}>02:00 AM</option>
                        <option value="03:00 AM" {{ getSetting('backup_schedule_hour') === '03:00 AM' ? 'selected' : '' }}>03:00 AM</option>
                        <option value="04:00 AM" {{ getSetting('backup_schedule_hour') === '04:00 AM' ? 'selected' : '' }}>04:00 AM</option>
                        <option value="05:00 AM" {{ getSetting('backup_schedule_hour') === '05:00 AM' ? 'selected' : '' }}>05:00 AM</option>
                        <option value="06:00 AM" {{ getSetting('backup_schedule_hour') === '06:00 AM' ? 'selected' : '' }}>06:00 AM</option>
                        <option value="07:00 AM" {{ getSetting('backup_schedule_hour') === '07:00 AM' ? 'selected' : '' }}>07:00 AM</option>
                        <option value="08:00 AM" {{ getSetting('backup_schedule_hour') === '08:00 AM' ? 'selected' : '' }}>08:00 AM</option>
                        <option value="09:00 AM" {{ getSetting('backup_schedule_hour') === '09:00 AM' ? 'selected' : '' }}>09:00 AM</option>
                        <option value="10:00 AM" {{ getSetting('backup_schedule_hour') === '10:00 AM' ? 'selected' : '' }}>10:00 AM</option>
                        <option value="11:00 AM" {{ getSetting('backup_schedule_hour') === '11:00 AM' ? 'selected' : '' }}>11:00 AM</option>
                        <option value="12:00 PM" {{ getSetting('backup_schedule_hour') === '12:00 PM' ? 'selected' : '' }}>12:00 PM</option>
                        <option value="01:00 PM" {{ getSetting('backup_schedule_hour') === '01:00 PM' ? 'selected' : '' }}>01:00 PM</option>
                        <option value="02:00 PM" {{ getSetting('backup_schedule_hour') === '02:00 PM' ? 'selected' : '' }}>02:00 PM</option>
                        <option value="03:00 PM" {{ getSetting('backup_schedule_hour') === '03:00 PM' ? 'selected' : '' }}>03:00 PM</option>
                        <option value="04:00 PM" {{ getSetting('backup_schedule_hour') === '04:00 PM' ? 'selected' : '' }}>04:00 PM</option>
                        <option value="05:00 PM" {{ getSetting('backup_schedule_hour') === '05:00 PM' ? 'selected' : '' }}>05:00 PM</option>
                        <option value="06:00 PM" {{ getSetting('backup_schedule_hour') === '06:00 PM' ? 'selected' : '' }}>06:00 PM</option>
                        <option value="07:00 PM" {{ getSetting('backup_schedule_hour') === '07:00 PM' ? 'selected' : '' }}>07:00 PM</option>
                        <option value="08:00 PM" {{ getSetting('backup_schedule_hour') === '08:00 PM' ? 'selected' : '' }}>08:00 PM</option>
                        <option value="09:00 PM" {{ getSetting('backup_schedule_hour') === '09:00 PM' ? 'selected' : '' }}>09:00 PM</option>
                        <option value="10:00 PM" {{ getSetting('backup_schedule_hour') === '10:00 PM' ? 'selected' : '' }}>10:00 PM</option>
                        <option value="11:00 PM" {{ getSetting('backup_schedule_hour') === '11:00 PM' ? 'selected' : '' }}>11:00 PM</option>
                    </select>`;
                    modal.find('#value-input-container').html(timeInput);
                } else if (key === 'default_page_length') {
                    var pageLengthInput = `<label for="default_page_length" class="form-label">Default Page Length <span style="color: red">*</span></label>
                    <select id="default_page_length" name="value" class="form-control ih-medium ip-gray radius-xs b-light px-15 select2" autocomplete="off">
                        <option value="10" {{ getSetting('default_page_length') === '10' ? 'selected' : '' }}>10</option>
                        <option value="25" {{ getSetting('default_page_length') === '25' ? 'selected' : '' }}>25</option>
                        <option value="50" {{ getSetting('default_page_length') === '50' ? 'selected' : '' }}>50</option>
                        <option value="100" {{ getSetting('default_page_length') === '100' ? 'selected' : '' }}>100</option>
                    </select>`;
                    modal.find('#value-input-container').html(pageLengthInput);
                } else if (key === 'site_logo') {
                    var fileInput = `<label for="setting-value" class="form-label">Value <span style="color: red">*</span></label>
                    <input type="file" class="form-control custom-file-input"
                        id="setting-value" name="value" value="${value}">`;
                    modal.find('#value-input-container').html(fileInput);
                } else {
                    var textInput = `<label for="setting-value" class="form-label">Value <span style="color: red">*</span></label>
                    <input type="text" class="form-control ih-medium ip-gray radius-xs b-light px-15"
                        id="setting-value" name="value" value="${value}">`;
                    modal.find('#value-input-container').html(textInput);
                }
            });

        });
    </script>
@endpush
