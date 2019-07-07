<?php

namespace App\Http\Controllers\Mypage;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Attachment;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Twitter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Validator;
use Carbon\CarbonPeriod;

/**
 * 記事CRUD共通コントローラー
 */
class ArticleController extends Controller
{
    protected $post_type = null;

    /**
     * 登録画面
     */
    public function create($post_type = null)
    {
        $categories = Category::for(Auth::user())->get();
        $categories = self::separateCategories($categories);
        $popular_tags = Tag::popular()->limit(20)->get();

        return view('mypage.articles.create', compact('post_type', 'categories', 'popular_tags'));
    }

    /**
     * 更新画面
     */
    public function edit(Article $article)
    {
        abort_unless($article->user_id === Auth::id(), 404);

        $article->load('categories', 'attachments', 'tags');

        $categories = Category::for(Auth::user())->get();
        $categories = self::separateCategories($categories);
        $popular_tags = Tag::popular()->limit(20)->get();

        return view('mypage.articles.edit', compact('article', 'categories', 'popular_tags'));
    }

    /**
     * 登録
     */
    public function store(Request $request, $preview = false)
    {
        self::validateData($request->all(), static::getValidateRule());

        $article = Article::make([
            'user_id'   => Auth::id(),
            'post_type' => $this->post_type,
            'title'     => $request->input('title'),
            'slug'      => $request->filled('slug') ? $request->input('slug') : $request->input('title'),
            'status'    => $request->input('status'),
        ]);

        // check slug is unique
        self::validateData(['slug' => $article->slug], ['slug' => "required|unique:articles,slug|max:255"]);

        $article->save();

        $article->setContents('thumbnail', $request->input('thumbnail_id'));
        if ($request->filled('thumbnail_id')) {
            $article->attachments()->save(Attachment::findOrFail($request->input('thumbnail_id')));
        }

        $article = $this->saveContents($request, $article);
        $article->save();

        if($preview) {
            return $this->renderPreview($article);
        }

        if($article->is_publish) {
            Twitter::articleCreated($article);
        }

        session()->flash('success', __('article.created', ['title' => $article->title, 'status' => __('status.'.$article->status)]));
        return redirect()->route('mypage.index');
    }

    /**
     * 更新
     */
    public function update(Request $request, Article $article, $preview = false)
    {
        abort_unless($article->user_id === Auth::id(), 404);

        self::validateData($request->all(), static::getValidateRule($article));

        $article->fill([
            'title'  => $request->input('title'),
            'slug'   => $request->filled('slug') ? $request->input('slug') : $request->input('title'),
            'status' => $request->input('status'),
        ]);

        // check slug is unique
        self::validateData(['slug' => $article->slug], ['slug' => "required|unique:articles,slug,{$article->id}|max:255"]);

        $article->setContents('thumbnail', $request->input('thumbnail_id'));
        if ($request->filled('thumbnail_id')) {
            $article->attachments()->save(Attachment::findOrFail($request->input('thumbnail_id')));
        }

        $article = $this->saveContents($request, $article);
        $article->save();

        if($preview) {
            return $this->renderPreview($article);
        }

        if($article->is_publish) {
            Twitter::articleUpdated($article);
        }

        session()->flash('success', __('article.updated', ['title' => $article->title, 'status' => __('status.'.$article->status)]));
        return redirect()->route('mypage.index');
    }

    public function analytics()
    {
        $articles = Auth::user()->articles()
            ->with('viewCounts', 'conversionCounts')->get();
        $articles = self::toSlim($articles);

        $labels = self::createLabels();

        return view('mypage.analytics', compact('articles', 'labels'));
    }

    /**
     * チャート用に加工
     */
    private static function toSlim($articles)
    {
        return $articles->map(function($article) {
            return [
                'id'    => $article->id,
                'title' => $article->title,
                'url'   => route('articles.show', $article->slug),
                'updated_at' => $article->updated_at->format('Ymd'),
                'created_at' => $article->created_at->format('Ymd'),
                'checked' => false,
                'conversion_counts' => $article->conversionCounts->map(function($c) {
                    return [
                        'id'     => $c->id,
                        'type'   => $c->type,
                        'period' => $c->period,
                        'count'  => $c->count,
                    ];
                }),
                'view_counts' => $article->viewCounts->map(function($c) {
                    return [
                        'id'     => $c->id,
                        'type'   => $c->type,
                        'period' => $c->period,
                        'count'  => $c->count,
                    ];
                }),
            ];
        });
    }

    private static function createLabels()
    {
        return [
            'daily' => self::createLabel('1day', 'Ymd'),
            'monthly' => self::createLabel('1month', 'Ym'),
            'yealy' => self::createLabel('1year', 'Y'),
        ];
    }
    private static function createLabel($interval, $format)
    {
        $items = (new CarbonPeriod(config('app.oldest_published_at'), '1day', now()->format('Y-m-d')))->toArray();

        return collect($items)->map(function($item) use ($format) { return $item->format($format); });
    }

    /**
     * プレビュー
     */
    private function renderPreview(Article $article)
    {
        $preview = true;
        $res = response(view('front.articles.show', compact('article', 'preview')));

        DB::rollback();
        return $res;
    }

    /**
     * 記事固有のコンテンツ保存処理
     */
    protected function saveContents(Request $request, Article $article)
    {
    }

    /**
     * バリデーションルールを返す
     */
    protected static function getValidateRule($article = null)
    {
        return [
            'title' => $article
                ? "required|unique:articles,title,{$article->id}|max:255"
                : 'required|unique:articles,title|max:255',
            'slug'         => 'nullable|max:255',
            'thumbnail_id' => 'nullable|exists:attachments,id,user_id,'.Auth::id(),
            'status'       => ['required', Rule::in(config('status')), ],
        ];
    }

    /**
     * 入力値のバリデーション
     */
    private static function validateData($data, $rule)
    {
        return Validator::make($data, $rule)->validate();
    }

    /**
     * タイプ別に分類したカテゴリ一覧を返す
     */
    private static function separateCategories($categories)
    {
        return collect($categories->reduce(function($list, $item) {
            if(!isset($list[$item->type])) {
                $list[$item->type] = [];
            }
            $list[$item->type][] = $item;
            return $list;
        }, []));
    }
}
