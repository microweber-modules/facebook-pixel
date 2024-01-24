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
                    <div>
                        <label>Facebook export feed url</label>
                        <input class="form-control" value="{{route('admin.facebook-pixel.export-feed', $exportSecretKey)}}" />
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-body">
                    <div>
                        <label>
                            {{_e('Facebook pixel id')}}
                        </label>
                        <input class="form-control" placeholder="{{_e('Place your facebook pixel id')}}" value="" />
                    </div>
                    <div class="mt-4">
                    <label>
                            {{_e('Facebook access token')}}
                        </label>
                        <input class="form-control" placeholder="{{_e('Place your facebook access token')}}" value="" />
                    </div>
                    <div class="mt-4">
                        <label>
                            {{_e('Test event ode')}}
                        </label>
                        <input class="form-control" placeholder="{{_e('Place your test event code')}}" value="TEST12345" />
                    </div>
                </div>
            </div>

    </div>

</div>
