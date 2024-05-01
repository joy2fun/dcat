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
                <p class="login-box-msg mt-1 mb-1">验证手机号</p>

                <form id="login-form" method="POST" action="{{ admin_url('verify-mobile') }}">

                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />

                    <fieldset class="form-label-group form-group form-disabled position-relative">
                        <input type="text" class="form-control" name="mobile" value="{{ Admin::user()->mobile }}" readonly>
                        <label for="email">手机号</label>
                    </fieldset>

                    <fieldset class="form-label-group form-group form-disabled position-relative">
                        <input type="text" class="form-control" name="code" placeholder="输入短信验证码">
                        <label for="email">短信验证码</label>
                    </fieldset>

                    <a id="send" class="btn">发送短信验证码</a>

                    <input type="submit" class="btn btn-primary float-right" value="立即验证" />
                </form>

            </div>
        </div>
    </div>
</div>

<script>
    Dcat.ready(function() {
        // ajax表单提交
        $('#login-form').form({
            validate: true,
            success: (r) => {
                if (r.status) {
                    Dcat.success('操作成功');
                    setTimeout(() => {
                        location.href = "{{ admin_url('/') }}"
                    }, 1000);
                }
            }
        });
        $('#send').click(function(t, secs, $el) {
            $el = $(this);
            if ($el.hasClass('disabled')) return;
            $el.addClass('disabled');
            secs = 60;
            t = setInterval(() => {
                secs--;
                if (secs <=1) {
                    $el.text('发送短信验证码').removeClass('disabled');
                    clearInterval(t);
                } else {
                    $el.text(`${secs}秒后可发送`);
                }
            }, 1000);

            $.ajax({
                type: 'POST',
                url: "{{ admin_url('send-verify-code') }}",
                error: function(e) {
                    console.log(e)
                },
                success: function(res) {
                    if (!res.status) {
                        Dcat.error(res?.data?.message ?? '发送短信失败')
                    }
                }
            });
        });
    });
</script>