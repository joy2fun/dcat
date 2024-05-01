<div class="{{$viewClass['form-group']}}">
    <label class="{{$viewClass['label']}} control-label">{{$label}}</label>
    <div class="{{$viewClass['field']}}">
        @include('admin::form.error')
        <div {!! $attributes !!} style="width: 100%; height: 100%;">
            <!-- <p>{!! $value !!}</p> -->
        </div>
        <input type="hidden" name="{{$name}}" value="{{ old($column, $value) }}" />
        @include('admin::form.help-block')
    </div>
</div>

<script require="@jsoneditor" init="{!! $selector !!}">
    const $el = $('#'+id).parents('.form-field').find('input[type="hidden"]')
    const options = {
        mode: 'code',
        modes: ['code', 'tree', 'view'], // allowed modes
        onChangeText: (v) => { $el.val(v) },
        onModeChange: function (newMode, oldMode) {
            if (newMode === 'code') {
                setAutoHeight()
            }
        }
    }
    let json = ''
    try {
        json = JSON.parse($el.val())
    } catch (e) {
        json = {}
    }
    const editor = new JSONEditor(document.getElementById(id), options, json)

    function setAutoHeight () {
        editor.aceEditor.setOptions({
            maxLines: 1000
        })
    }
    setAutoHeight()
</script>