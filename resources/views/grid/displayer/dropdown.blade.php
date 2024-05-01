<div class="dropdown grid-column-dropdown" 
    data-reload="{{ $refresh }}" data-url="{{ $url }}" data-name="{{ $column }}" data-selected="{{ $value }}">
  <a class="dropdown-toggle" data-toggle="dropdown">{{ $options[$value] ?? '' }}</a>
  <ul class="dropdown-menu">
    @foreach($options as $k => $v)
    <li class="dropdown-item dropdown-option p-50 selected" data-value="{{ $k }}"><i class="fa fa-check pr-50"></i>{{ $v }}</li>
    @endforeach
  </ul>
</div>

<style>
    .grid-column-dropdown .dropdown-option .fa {
        visibility: hidden;
    }
    .grid-column-dropdown .selected .fa {
        visibility: visible;
    }
</style>

<script>
    $('.grid-column-dropdown').off('click').on('click', function(e){
        var $p = $(this).closest('.dropdown');
        if (!$(e.target).is('.dropdown-option')) {
            var currentValue = $p.data('selected')
            $('.dropdown-option', $p).each(function() {
                if ($(this).data('value') == currentValue) {
                    $(this).addClass('selected')
                } else {
                    $(this).removeClass('selected')
                }
            })
            return;
        }
        var value = $(e.target).data('value'),
            name = $p.data('name'),
            url = $p.data('url'),
            data = {},
            reload = $p.data('reload');

        if (name.indexOf('.') === -1) {
            data[name] = value;
        } else {
            name = name.split('.');
            data[name[0]] = {};
            data[name[0]][name[1]] = value;
        }

        Dcat.NP.start();
        $.put({
            url: url,
            data: data,
            success: function (d) {
                Dcat.NP.done();
                if (d.status) {
                    Dcat.success(d.data.message);
                    if (reload) {
                        Dcat.reload();
                    } else {
                        $p.data('selected', value)
                        $p.children('.dropdown-toggle').text($(e.target).text())
                    }
                } else {
                    Dcat.error(d.data.message);
                }
            }
        });
    });
</script>
