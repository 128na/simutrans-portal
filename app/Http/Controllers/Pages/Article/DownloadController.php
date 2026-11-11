<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages\Article;

use App\Actions\FrontArticle\ConversionAction;
use App\Actions\FrontArticle\DownloadAction;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Contents\AddonIntroductionContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadController extends Controller
{
    public function download(Request $request, Article $article, DownloadAction $downloadAction): StreamedResponse
    {
        if (Gate::denies('download', $article)) {
            abort(404);
        }

        // GET ルートは HEAD にも応答する。本文を返さない HEAD では CV を数えない
        return $downloadAction($article, Auth::user(), ! $request->isMethod('HEAD'));
    }

    public function conversion(Article $article, ConversionAction $conversionAction): RedirectResponse
    {
        if (Gate::denies('conversion', $article)) {
            abort(404);
        }

        $conversionAction($article, Auth::user());

        if ($article->contents instanceof AddonIntroductionContent && $article->contents->link) {
            return redirect($article->contents->link, Response::HTTP_FOUND);
        }

        abort(404);
    }
}
