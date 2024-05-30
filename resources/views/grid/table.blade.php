
<div class="dcat-box">

    <div class="d-block pb-0">
        @include('admin::grid.table-toolbar')
    </div>

    {!! $grid->renderFilter() !!}

    {!! $grid->renderHeader() !!}

    @if (admin_is_mobile())
    <div class="{!! $grid->formatTableParentClass() !!}">
    @foreach($grid->rows() as $row)
        <div class="card">
            <ul class="list-group list-group-flush">
                @foreach($grid->getVisibleColumns() as $key => $column)
                    <li class="list-group-item">
                        @if ($key == '__row_selector__')
                        {!! $row->column($column->getName()) !!}
                        @else
                        {!! $column->getLabel() !!}
                        <span class="float-right">{!! $row->column($column->getName()) !!}</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    @endforeach
    @if ($grid->rows()->isEmpty())
    <div class="alert alert-light" role="alert">
        暂无记录。
    </div>
    @endif
    </div>
    @else
    <div class="{!! $grid->formatTableParentClass() !!}">
        <table class="{{ $grid->formatTableClass() }}" id="{{ $tableId }}" >
            @if ($grid->option('display_table_header'))
            <thead>
            @if ($headers = $grid->getVisibleComplexHeaders())
                <tr>
                    @foreach($headers as $header)
                        {!! $header->render() !!}
                    @endforeach
                </tr>
            @endif
            <tr>
                @foreach($grid->getVisibleColumns() as $column)
                    <th {!! $column->formatTitleAttributes() !!}>{!! $column->getLabel() !!}{!! $column->renderHeader() !!}</th>
                @endforeach
            </tr>
            </thead>
            @endif

            @if ($grid->hasQuickCreate())
                {!! $grid->renderQuickCreate() !!}
            @endif

            <tbody>
            @foreach($grid->rows() as $row)
                <tr {!! $row->rowAttributes() !!}>
                    @foreach($grid->getVisibleColumnNames() as $name)
                        <td {!! $row->columnAttributes($name) !!}>{!! $row->column($name) !!}</td>
                    @endforeach
                </tr>
            @endforeach
            @if ($grid->rows()->isEmpty())
                <tr>
                    <td colspan="{!! count($grid->getVisibleColumnNames()) !!}">
                        <div style="margin:5px 0 0 10px;"><span class="help-block" style="margin-bottom:0"><i class="feather icon-alert-circle"></i>&nbsp;{{ trans('admin.no_data') }}</span></div>
                    </td>
                </tr>
            @endif
            </tbody>
        </table>
    </div>
    @endif

    {!! $grid->renderFooter() !!}

    {!! $grid->renderPagination() !!}

</div>
