<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages\Article;

use App\Actions\FrontArticle\ConversionAction;
use App\Actions\FrontArticle\DownloadAction;
use App\Models\Article;
use App\Models\Contents\AddonIntroductionContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class DownloadController extends Controller
{
    public function download(Article $article, DownloadAction $downloadAction): StreamedResponse
    {
        if (Gate::denies('download', $article)) {
            abort(404);
        }

        return $downloadAction($article, Auth::user());
    }

    public function conversion(Article $article, ConversionAction $conversionAction): RedirectResponse
    {
        if (Gate::allows('conversion', $article)) {
            $conversionAction($article, Auth::user());
        }

        assert($article->contents instanceof AddonIntroductionContent);
        if ($article->contents->link) {
            return redirect($article->contents->link, Response::HTTP_FOUND);
        }

        abort(404);
    }
}
