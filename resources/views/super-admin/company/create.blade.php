@extends('layouts.app') @push('head-script')
<link rel="stylesheet" href="{{ asset('assets/node_modules/dropify/dist/css/dropify.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/iCheck/all.css') }}">
<link rel="stylesheet" href="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}">
@endpush
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">@lang('app.createNew')</h4>

                <form id="editSettings" class="ajax-form">
                    @csrf

                    <div class="form-group">
                        <label class ="required"  for="company_name">@lang('modules.accountSettings.companyName')</label>
                        <input type="text" class="form-control" id="company_name" name="company_name">
                    </div>
                    @if(module_enabled('Subdomain'))

                        <div class="form-group">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" placeholder="subdomain" name="sub_domain" id="sub_domain">
                                <div class="input-group-append">
                                                    <span class="input-group-text"
                                                          id="basic-addon2">.{{ get_domain() }}</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="form-group">
                        <label class ="required" for="company_email">@lang('modules.accountSettings.companyEmail')</label>
                        <input type="email" class="form-control" id="company_email" name="company_email">
                    </div>
                    <div class="form-group">
                        <label for="company_phone">@lang('modules.accountSettings.companyPhone')</label>
                        <input type="tel" class="form-control" id="company_phone" name="company_phone">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">@lang('modules.accountSettings.companyWebsite')</label>
                        <input type="text" class="form-control" id="website" name="website">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">@lang('modules.accountSettings.companyLogo')</label>
                        <div class="card">
                            <div class="card-body">
                                <input type="file" id="input-file-now" name="logo" class="dropify" data-default-file="{{ asset('logo-not-found.png') }}"
                                />
                            </div>
                        </div>
                    </div>


                    <div class="form-group">
                        <label for="address">@lang('modules.accountSettings.companyAddress')</label>
                        <textarea class="form-control" id="address" rows="5" name="address"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="address">@lang('modules.company.showFrontend')</label>
                        <select name="show_in_frontend" id="show_in_frontend" class="form-control">
                                    <option value="true">@lang('app.yes')</option>
                                    <option value="false">@lang('app.no')</option>
                                </select>
                    </div>

                    <hr>
                    <div class="form-group">
                        <h5 class="text-uppercase">@lang('modules.register.accountDetails')</h5>
                    </div>

                    <div class="form-group">
                        <input type="text" class="form-control" id="full_name" name="full_name" placeholder="@lang('modules.front.fullName')" />
                    </div>

                    <div class="form-group">
                        <input type="email" name="email" placeholder="@lang('modules.front.email')" class="form-control" />
                    </div>

                    <div class="form-group">
                        <input type="password" name="password" placeholder="@lang('app.password')" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label class="">
                            <div class="icheckbox_flat-green" aria-checked="false" aria-disabled="false" style="position: relative;">
                                <input  type="checkbox" value="yes" name="featured" id="featured" class="flat-red"  style="position: absolute; opacity: 0;">
                                <ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins>
                            </div>
                            @lang('app.addAs') @lang('app.featured')
                        </label>
                    </div>
                    <div id="featureDateBox" style="display: none">

                        <div class="form-group">
                            <label for="address">@lang('app.featured') @lang('app.startDate')</label>
                            <input type="text" class="form-control datepicker" id="date-start"  autocomplete="off"   name="start_date">
                        </div>
                        <div class="form-group">
                            <label for="address">@lang('app.featured') @lang('app.endDate')</label>
                            <input type="text" class="form-control datepicker" id="date-end" autocomplete="off" value="" name="end_date">
                        </div>

                    </div>

                    <button type="button" id="save-form" class="btn btn-success waves-effect waves-light m-r-10">
                            @lang('app.save')
                        </button>
                    <button type="reset" class="btn btn-inverse waves-effect waves-light">@lang('app.reset')</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
 @push('footer-script')
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/dropify/dist/js/dropify.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/iCheck/icheck.min.js') }}"></script>
<script src="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>


<script src="{{ asset('assets/node_modules/moment/moment.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/multiselect/js/jquery.multi-select.js') }}"></script>
<script src="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}" type="text/javascript"></script>
<script>
    //Flat red color scheme for iCheck
    $('input[type="checkbox"].flat-red').iCheck({
        checkboxClass: 'icheckbox_flat-blue',
    })

    $('#date-start').bootstrapMaterialDatePicker
    ({
        time: false,
        clearButton: true,
    }).on('change', function (selected) {
        var value = $('#date-start').val();
       console.log(selected,  'value', value);
       $('#date-end').bootstrapMaterialDatePicker
    ({
        minDate : new Date(value),
        time: false,
        clearButton: true,
    });
       
});
    
    $('#featured').on('ifChecked', function (event){
        $("#featureDateBox").show();
    });
    $('#featured').on('ifUnchecked', function (event) {
        $("#featureDateBox").hide();
    });

    // For select 2
        $(".select2").select2();

        $('.dropify').dropify({
            messages: {
                default: '@lang("app.dragDrop")',
                replace: '@lang("app.dragDropReplace")',
                remove: '@lang("app.remove")',
                error: '@lang('app.largeFile')'
            }
        });



        $('#save-form').click(function () {
            $.easyAjax({
                url: '{{route("superadmin.company.store")}}',
                container: '#editSettings',
                type: "POST",
                redirect: true,
                file: true
            })
        });
</script>



@endpush
