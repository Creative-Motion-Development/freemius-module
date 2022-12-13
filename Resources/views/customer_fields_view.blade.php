<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
    <div class="freemius-panel">
        <div class="freemius-panel-heading" role="tab" id="headingOne">
            <div class="freemius-panel-title">
                <a class="" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne"
                   aria-expanded="true" aria-controls="collapseOne">
                    {{ __('Freemius customer') }}
                </a>
            </div>
        </div>
        <div id="collapseOne" class="freemius-panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
            <div class="freemius-panel-body">
                <ul class="freemius-list">
                    <li>
                        <span class="glyphicon glyphicon-user"></span>
                        {{$freemius_user->first}} {{$freemius_user->last}} ({{$freemius_user->id}})
                    </li>
                    <li>
                        <ul class="freemius-list">
                            <li>
                                <span class="glyphicon glyphicon-usd"></span>
                                LTV: ${{$freemius_user->gross}}
                            </li>
                            <li>
                                <span class="glyphicon glyphicon-flag"></span>
                                {{$freemius_user->getCreated()}}
                            </li>
                        </ul>
                    </li>
                    <li>
                        <div class="freemius-panel-title-plugins">
                            {{ __('Plugins') }}
                        </div>
                    </li>
                    <li>
                        <ul class="freemius-list-plugins">
                            @foreach ($plugins as $plugin)
                                <li>
                                    <div class="freemius-panel">
                                        <div class="freemius-panel-title-plugins">
                                            <a class="" role="button" data-toggle="collapse" data-parent="#accordion"
                                               href="#collapsePlugins"
                                               aria-expanded="true" aria-controls="collapsePlugins">
                                                <div class="freemius-plugin-icon"
                                                     style="background-image: url('https:{{$plugin->icon}}');">&nbsp;
                                                </div>{{$plugin->title}}
                                            </a>
                                        </div>
                                        <div id="collapsePlugins" class="freemius-panel-collapse collapse in"
                                             role="tabpanel">
                                            <div class="freemius-panel-body">
                                                <ul class="freemius-list freemius-list-sites">
                                                    <li class="freemius-list-plugin-link">
                                                        <span class="glyphicon glyphicon-link"></span>
                                                        <a href="https://dashboard.freemius.com/#!/live/plugins/{{$plugin->id}}/users/{{$freemius_user->id}}/"
                                                           target="_blank">
                                                            {{ __('Go to Freemius') }}</a>
                                                    </li>
                                                    @empty($plugin->sites)
                                                        <li>{{ __('No active sites') }}</li>
                                                    @endempty
                                                    @foreach ($plugin->sites as $site)
                                                        @php $license = $site->license; @endphp
                                                        <li class="freemius-list-url">
                                                            <div class="freemius-panel">
                                                                <div class="freemius-panel-title-sites">
                                                                    <a class="" role="button" data-toggle="collapse"
                                                                       data-parent="#accordion"
                                                                       href="#collapseSite{{$site->id}}"
                                                                       aria-expanded="true"
                                                                       aria-controls="collapseSite{{$site->id}}">
                                                                        {{$site->title}}
                                                                    </a>
                                                                </div>
                                                                <div id="collapseSite{{$site->id}}"
                                                                     class="freemius-panel-collapse collapse in"
                                                                     role="tabpanel">
                                                                    <div class="freemius-panel-body">
                                                                        <ul class="freemius-list">
                                                                            <li>
                                                                                <span
                                                                                    class="glyphicon glyphicon-link"></span>
                                                                                <a href="{{$site->url}}">{{ __('Open site') }}</a>
                                                                            </li>
                                                                            <li>
                                                                                <span
                                                                                    class="glyphicon glyphicon-usd"></span>
                                                                                LTV: <span
                                                                                    class="status-box status-green">${{$site->gross}}</span>
                                                                            </li>
                                                                            <li>
                                                                                @if($site->is_premium && $site->license)
                                                                                    <spam
                                                                                        class="status-box status-blue">
                                                                                        {{ __('Premium') }}
                                                                                    </spam>
                                                                                @else
                                                                                    <spam
                                                                                        class="status-box status-gray">
                                                                                        {{ __('Free') }}
                                                                                    </spam>
                                                                                @endif
                                                                            </li>
                                                                            <li>
                                                                                <span
                                                                                    class="status-box status-green">{{$site->plan->title}}</span>
                                                                                {{ __('plan') }}
                                                                            </li>
                                                                            @if($license)
                                                                                <li>
                                                                                    <strong>{{ __('Renews in:') }}</strong>
                                                                                    {{$license->getRenewsIn()}}
                                                                                </li>
                                                                            @endif
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>

</div>
