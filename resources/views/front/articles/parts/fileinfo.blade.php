@isset($fileInfo->data['dats'])
    <div>
        <a data-toggle="collapse" href="#datInfo" aria-expanded="false">
            ▶Datファイル一覧
        </a>
    </div>
    <div class="collapse" id="datInfo">
        <div class="card card-body bg-light">
            @foreach ($fileInfo->data['dats'] as $filename => $names)
                <li><span>{{ $filename }}</span>
                    <ul>
                        @foreach ($names as $name)
                            <li>{{ $name }}</li>
                        @endforeach
                    </ul>
                </li>
            @endforeach
        </div>
    </div>
@endisset
@isset($fileInfo->data['paks'])
    <div>
        <a data-toggle="collapse" href="#pakInfo" aria-expanded="false">
            ▶Pakファイル一覧
        </a>
    </div>
    <div class="collapse" id="pakInfo">
        <div class="card card-body bg-light">
            @foreach ($fileInfo->data['paks'] as $filename => $names)
                <li><span>{{ $filename }}</span>
                    <ul>
                        @foreach ($names as $name)
                            <li>{{ $name }}</li>
                        @endforeach
                    </ul>
                </li>
            @endforeach
        </div>
    </div>
@endisset
@isset($fileInfo->data['tabs'])
    <div>
        <a data-toggle="collapse" href="#tabInfo" aria-expanded="false">
            ▶Tabファイル一覧
        </a>
    </div>
    <div class="collapse" id="tabInfo">
        <div class="card card-body bg-light">
            @foreach ($fileInfo->data['tabs'] as $filename => $names)
                <li><span>{{ $filename }}</span>
                    <ul>
                        @foreach ($names as $original => $translated)
                            <li>{{ $translated }} ({{ $original }})</li>
                        @endforeach
                    </ul>
                </li>
            @endforeach
        </div>
    </div>
@endisset
