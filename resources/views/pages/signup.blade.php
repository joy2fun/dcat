<style>
    .login-box {
        margin-top: -10rem;
        padding: 5px;
    }

    .login-card-body {
        padding: 1.5rem 1.8rem 1.6rem;
    }

    .card,
    .card-body {
        border-radius: .25rem
    }

    .login-btn {
        padding-left: 2rem !important;
        ;
        padding-right: 1.5rem !important;
    }

    .content {
        overflow-x: hidden;
    }

    .form-group .control-label {
        text-align: left;
    }
</style>
{{-- prevent console error --}}
<div class="horizontal-menu"><div class="main-horizontal-sidebar"></div></div>
<div class="login-page bg-40">
    <div class="login-box">
        <div class="login-logo mb-2">
            {{ config('admin.name') }}
        </div>
        <div class="card">
            <div class="card-body login-card-body shadow-100">
                <p class="login-box-msg mt-1 mb-1">注册新账号</p>

                <form id="login-form" method="POST" action="/signup">

                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />

                    <fieldset class="form-label-group form-group position-relative has-icon-left">
                        <input type="number" class="form-control {{ $errors->has('username') ? 'is-invalid' : '' }}" name="username" placeholder="手机号" value="{{ old('username') }}" required autofocus>

                        <div class="form-control-position">
                            <i class="feather icon-phone"></i>
                        </div>

                        <label for="email">手机号</label>

                        <div class="help-block with-errors"></div>
                        @if($errors->has('username'))
                        <span class="invalid-feedback text-danger" role="alert">
                            @foreach($errors->get('username') as $message)
                            <span class="control-label" for="inputError"><i class="feather icon-x-circle"></i> {{$message}}</span><br>
                            @endforeach
                        </span>
                        @endif
                    </fieldset>

                    <fieldset class="form-label-group form-group position-relative has-icon-left">
                        <input minlength="5" maxlength="20" id="password" type="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" name="password" placeholder="{{ trans('admin.password') }}" required autocomplete="off">

                        <div class="form-control-position">
                            <i class="feather icon-lock"></i>
                        </div>
                        <label for="password">{{ trans('admin.password') }}</label>

                        <div class="help-block with-errors"></div>
                        @if($errors->has('password'))
                        <span class="invalid-feedback text-danger" role="alert">
                            @foreach($errors->get('password') as $message)
                            <span class="control-label" for="inputError"><i class="feather icon-x-circle"></i> {{$message}}</span><br>
                            @endforeach
                        </span>
                        @endif

                    </fieldset>
                    <fieldset class="form-label-group form-group position-relative has-icon-left">
                        <input minlength="5" maxlength="20" id="confirm_password" type="password" class="form-control" name="confirm_password" placeholder="确认密码" required autocomplete="off">

                        <div class="form-control-position">
                            <i class="feather icon-lock"></i>
                        </div>
                        <label for="password">确认密码</label>
                    </fieldset>
                    <div class="form-group d-flex justify-content-between align-items-center">
                        <div id='captcha' class="text-left">
                            <img src="{{ captcha_src() }}" id="captcha_image"> <a class="text-left" href='javascript:void(0);' id="reload_captcha">换一个</a>
                        </div>
                        <div class="float-right">
                            <input type="text" class="form-control" style="width: 100px;" name="captcha" placeholder="验证码答案" />
                        </div>
                        <br>
                        <div class="row"></div>
                    </div>

                    <span class="float-left">
                        <input type="submit" class="btn btn-primary" value="立即注册">
                    </span>
                    <span class="float-right">
                        <a class="btn btn-secondary" href="/login">已有账号登录</a>
                    </span>
                </form>

            </div>
        </div>
    </div>
    <footer class="">
        <p class="clearfix blue-grey lighten-2 mb-0 text-center">
            <span class="text-center d-block d-md-inline-block mt-25">
                {!! config('admin.footer') !!}
            </span>
        </p>
    </footer>
</div>

<script>
    Dcat.ready(function() {
        // ajax表单提交
        $('#login-form').form({
            validate: true,
            after: () => {
                $('#reload_captcha').trigger("click");
            },
            success: (res) => {
                if (res.status) {
                    location.href = "{{ admin_url('verify-mobile') }}"
                }
            }
        });
        $('#reload_captcha').click(function(event) {
            $('#captcha_image').attr('src', $('#captcha_image').attr('src') + '{{ captcha_src() }}');
        });
    });
</script>