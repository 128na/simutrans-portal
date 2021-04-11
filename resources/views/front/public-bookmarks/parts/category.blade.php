<div>
    <span title="カテゴリ">📁</span>
    <a href="{{ route('category', [$item->type, $item->slug]) }}">
        @lang("category.{$item->type}.{$item->slug}")
    </a>
</div>
