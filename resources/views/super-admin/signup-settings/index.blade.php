@extends('layouts.app')

@push('head-script')
   
    <link rel="stylesheet" href="{{ asset('assets/node_modules/switchery/dist/switchery.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">

@endpush

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        
                     <div class="col-md-6 form-group">
                                
                                <label
                                    class="control-label">@lang('app.registrationStatus')
                                 </label>
                                <div class="switchery-demo">
                                    <input type="checkbox" name="registration_open" id="registration_open" 
                                    class="js-switch packeges" data-size="medium" data-color="#00c292"
                                    data-secondary-color="#f96262" value="true"@if($registration->registration_open == 1) checked @endif/>
                                </div>
                                <div class="row mt-2">
                                 
                                    <div class="col-md-12">
                                        <span>*</span><span id="registation-text" style='color:rgb(0,128,0);'>@lang('messages.registrationOpen')</span>
                                    </div>
                                </div>
                             </div>
                             <div class="col-md-6 form-group">
                                
                                <label
                                    class="control-label">@lang('app.disableRegisterButton')
                                 </label>
                                <div class="switchery-demo">
                                    <input type="checkbox" name="registration_disable_button" id="disable_button"
                                    class="js-switch " data-size="medium" data-color="#00c292"
                                    data-secondary-color="#f96262" value="true"@if($registration->registration_disable_button == 1) checked @endif/>
                                    
                                    <div class="row mt-2">
                                        
                                        <div class="col-md-12 ">
                                            <span>*</span><span id="button-text" style='color:rgb(0,128,0);'>@lang('messages.registrationButtonEnable')</span>
                                        </div>
                                    </div>
                                </div>
                       </div>
                             </div>
                            
                    </div>
                <div class="col-md-12">
                    <div class=" col-md-12 text-show registration-form">
                        <form id="editSettings" class="ajax-form">
                            @csrf
                            <div class="col-sm-12 col-md-12 col-xs-12">
                                <div class="form-group disable-message">
                                    <label for="title">@lang('app.message')</label>
                                    <textarea class="form-control " rows="6" name="registraion_message" id="message">{!! $registration->registraion_message !!}</textarea>
                                </div>
                                <button type="button" id="save-form"
                                        class="mb-3 btn btn-success waves-effect waves-light m-r-10">
                                    @lang('app.update')
                                </button>
                            </div>
                        
                        
                    
                      </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('footer-script')
   
<script src="{{ asset('assets/node_modules/switchery/dist/switchery.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>

    <script>
        // Switchery
        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
    $('.js-switch').each(function () {
        new Switchery($(this)[0], $(this).data());

    });

    
    @if ($registration->registration_open == 0)
        $('.registration-form').show();
        $('#registation-text').text("@lang('messages.registrationClose')").css("color", "#ff0000");
    @else
        $('.registration-form').hide();
        $('#registation-text').text("@lang('messages.registrationOpen')").css("color", "#008000");
    @endif
    //Form disablee and button disable
    $('#registration_open,#disable_button').change(function(){
        var status = $('#registration_open').is(':checked') ? 1 : 0 ;
        var disable = $('#disable_button').is(':checked') ? 1 : 0 ;

        var token = "{{ csrf_token() }}";
        $.easyAjax({
            url: '{{route('superadmin.signup-setting.update', $registration->id)}}',
            type: "put",
            data: {'status': status,'disable': disable,  '_token': token},
            success: function (response) {
                if(status == 0){
                    $('.registration-form').show();
                    $('#registation-text').text("@lang('messages.registrationClose')").css("color", "#ff0000");

                } else {
                    $('.registration-form').hide();
                    $('#registation-text').text("@lang('messages.registrationOpen')").css("color", "#008000");
                }
                if(disable == 0){
                     $('#button-text').text("@lang('messages.registerButtonDisable')").css("color", "#ff0000");

                 } else {
                    $('#button-text').text("@lang('messages.registrationButtonEnable')").css("color", "#008000");

                }
            }
     
         })    
    });
    @if ($registration->registration_disable_button == 0)
        $('#button-text').text("@lang('messages.registerButtonDisable')").css("color", "#ff0000");
    @else
        $('#button-text').text("@lang('messages.registrationButtonEnable')").css("color", "#008000");
    @endif
    //Registration message submit
    $('body').on('click', '#save-form', function () {
        var message = $('#message').val();
        var token = "{{ csrf_token() }}";
        $.easyAjax({
                url: '{{route('superadmin.signup-setting.update', $registration->id)}}',
                container: '#editSettings',
                type: "put",
                data:{
                    'message': message,
                    '_token': token
                },

            })
        });
    </script>
@endpush
                        
                       
                                
        

                