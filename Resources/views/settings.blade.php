@extends('layouts.app')

@section('title', __('Freemius integration'))

@section('content')
    <div class="section-heading">
        {{ __('Freemius') }}
    </div>
    <div class="row-container form-container">
        <div class="row">
            <div class="col-xs-12">
                <form class="form-horizontal margin-top margin-bottom" method="POST" action="">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="col-sm-2 control-label">{{ __('Freemius App ID') }}</label>
                        <div class="col-sm-1">
                            <input class="form-control" name="freemius_app_id" value="{{$settings['app_id']}}" maxlength="75"
                                   required/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">{{ __('Freemius Public key') }}</label>
                        <div class="col-sm-6">
                            <input class="form-control" name="freemius_public_key" value="{{$settings['public_key']}}" maxlength="75"
                                   required/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">{{ __('Freemius Secret key') }}</label>
                        <div class="col-sm-6">
                            <input class="form-control" name="freemius_secret_key" value="{{$settings['secret_key']}}" maxlength="75"
                                   required/>
                        </div>
                    </div>

                    <div class="form-group margin-top margin-bottom-10">
                        <div class="col-sm-10 col-sm-offset-2">
                            <button class="btn btn-primary"
                                    data-loading-text="{{ __('Saving') }}…">{{ __('Save') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
