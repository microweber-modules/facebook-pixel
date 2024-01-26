<div>

    <div class="d-flex justify-content-between">
        <div>
        <h2>{{_e('Facebook Pixel')}}</h2>
        <p>
            {{_e('Facebook pixel is an analytics tool that allows you to measure the effectiveness of your advertising by understanding the actions people take on your website.')}}
        </p>
        </div>
    </div>

    <div class="mb-3 mt-4 gap-3">

            <div class="card">
                <div class="card-body">
                    <div class="d-flex gap-2">
                        <div style="width:100%">
                            <label>Facebook export feed url</label>
                            <input class="form-control" value="{{route('facebook-pixel.export-feed')}}?feed_secret={{$exportSecretKey}}" />
                        </div>
                        <div>
                            <div>&nbsp;</div>
                            <div>
                                <a class="btn btn-outline-success" href="{{route('facebook-pixel.export-feed')}}?feed_secret={{$exportSecretKey}}">
                                    {{_e('Download feed')}}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-body mb-4">

                    @php
                        $facebookPixelEnabled = false;
                        $getFacebookPixelIsEnabled = get_option('is_enabled', 'facebook_pixel');
                        if ($getFacebookPixelIsEnabled == 1) {
                            $facebookPixelEnabled = true;
                        }
                    @endphp

                    <div x-data="{'facebookPixelEnabled': '{{$facebookPixelEnabled}}'}">
                        <div @mw-option-saved.window="function() {
                            if ($event.detail.optionKey == 'is_enabled') {
                                facebookPixelEnabled = $event.detail.optionValue;
                            }
                            }">
                        </div>
                        <div>
                            <label class="live-edit-label">
                                {{_e('Enable Facebook Pixel')}}
                            </label>
                            <livewire:microweber-option::toggle optionKey="is_enabled" optionGroup="facebook_pixel" />
                        </div>
                        <div x-show="facebookPixelEnabled == '1'">
                            <div>
                                <label class="live-edit-label">
                                    {{_e('Facebook pixel id')}}
                                </label>
                                <livewire:microweber-option::text placeholder="Place your facebook pixel id" optionKey="facebook_pixel_id" optionGroup="facebook_pixel" />
                            </div>
                            <div class="mt-4">
                                <label class="live-edit-label">
                                    {{_e('Facebook access token')}}
                                </label>
                                <livewire:microweber-option::text placeholder="Place your facebook access token" optionKey="facebook_access_token" optionGroup="facebook_pixel" />
                            </div>
                            <div class="mt-4">
                                <label class="live-edit-label">
                                    {{_e('Test event code')}}
                                </label>
                                <livewire:microweber-option::text placeholder="Place your test event code" optionKey="facebook_test_event_code" optionGroup="facebook_pixel" />
                            </div>
                        </div>
                    </div>

                </div>
            </div>

    </div>

</div>
