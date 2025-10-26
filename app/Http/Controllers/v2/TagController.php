<?php

declare(strict_types=1);

namespace App\Http\Controllers\v2;

use App\Models\Tag;
use App\Services\Front\MetaOgpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

final class TagController extends Controller
{
    public function __construct(
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function index(): \Illuminate\Contracts\View\View
    {
        return view('v2.mypage.tags', [
            'tags' => Tag::all(),
            'meta' => $this->metaOgpService->tags(),
        ]);
    }

    public function store(Tag $tag): RedirectResponse
    {
        if (Auth::user()->cannot('store', $tag)) {
            return abort(403);
        }

        // TODO: store

        return to_route('mypage.tags')->with('status', '作成しました');
    }

    public function update(Tag $tag): RedirectResponse
    {
        if (Auth::user()->cannot('update', $tag)) {
            return abort(403);
        }

        // TODO: update

        return to_route('mypage.tags')->with('status', '更新しました');
    }
}
