<?php

namespace App\Http\Controllers\Mypage;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Attachment;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\Auth;

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
        $post_types       = Category::post()->get();
        $addons           = Category::addon()->get();
        $paks             = Category::pak()->get();
        $pak128_positions = Category::pak128Position()->get();
        $licenses         = Category::license()->get();

        $popular_tags = Tag::popular()->limit(20)->get();

        return view('mypage.articles.create', compact('post_type', 'post_types', 'addons', 'paks', 'pak128_positions', 'licenses', 'popular_tags'));
    }

    /**
     * 更新画面
     */
    public function edit(Article $article)
    {
        abort_unless($article->user_id === Auth::id(), 404);

        $article->load('categories', 'attachments', 'tags');

        $post_types       = Category::post()->get();
        $addons           = Category::addon()->get();
        $paks             = Category::pak()->get();
        $pak128_positions = Category::pak128Position()->get();
        $licenses         = Category::license()->get();

        $popular_tags = Tag::popular()->limit(20)->get();

        return view('mypage.articles.edit', compact('article', 'post_types', 'addons', 'paks', 'pak128_positions', 'licenses', 'popular_tags'));
    }

    /**
     * 登録
     */
    public function store(Request $request)
    {
        self::validateData($request->all(), static::getValidateRule());

        $article = Article::make([
            'user_id' => Auth::id(),
            'post_type' => $this->post_type,
            'title'   => $request->input('title'),
            'slug'    => $request->filled('slug') ? $request->input('slug') : $request->input('title'),
            'status'  => $request->input('status'),
        ]);

        if (!$article->isUniqueSlug()) {
            session()->flash('error', __('article.slug-duplicate'));
            return back()->withInput();
        }
        $article->save();

        // contents, attachments
        $attachments = [];
        if ($request->hasFile('thumbnail')) {
            $thumbnail = self::saveAttachment($request->file('thumbnail'), Auth::id(), $article, $contents['thumbnail'] ?? null);
            $article->setContents('thumbnail', $thumbnail->id);
            $attachments[] = $thumbnail;
        }
        $attachments = $this->saveContents($request, $article, $attachments);
        $article->attachments()->saveMany($attachments);

        session()->flash('success', __('article.created', ['title' => $article->title, 'status' => __('status.'.$article->status)]));
        return redirect()->route('mypage.index');
    }


    /**
     * 更新
     */
    public function update(Request $request, Article $article)
    {
        abort_unless($article->user_id === Auth::id(), 404);

        self::validateData($request->all(), static::getValidateRule($article));

        $article->fill([
            'title'   => $request->input('title'),
            'slug'    => $request->filled('slug') ? $request->input('slug') : $request->input('title'),
            'status'  => $request->input('status'),
        ]);

        if (!$article->isUniqueSlug()) {
            session()->flash('error', __('article.slug-duplicate'));
            return back()->withInput();
        }
        $article->save();

        // contents, attachments
        $attachments = [];
        if ($request->hasFile('thumbnail')) {
            $thumbnail = self::saveAttachment($request->file('thumbnail'), Auth::id(), $article, $contents['thumbnail'] ?? null);
            $article->setContents('thumbnail', $thumbnail->id);
            $attachments[] = $thumbnail;
        }
        $attachments = $this->saveContents($request, $article, $attachments);
        $article->attachments()->saveMany($attachments);

        session()->flash('success', __('article.updated', ['title' => $article->title, 'status' => __('status.'.$article->status)]));
        return redirect()->route('mypage.index');
    }

    /**
     * 記事固有のコンテンツ保存処理
     */
    protected function saveContents(Request $request, Article $article, $attachments)
    {
    }

    /**
     * バリデーションルールを返す
     */
    protected static function getValidateRule($article = null)
    {
        return [
            'title'     => $article
                ? "required|unique:articles,title,{$article->id}|max:255"
                : 'required|unique:articles,title|max:255',
            'slug'      => 'nullable|max:255',
            'thumbnail' => 'nullable|image',
            'status'    => ['required', Rule::in(config('status')), ],
        ];
    }

    /**
     * 添付ファイルの保存
     */
    protected static function saveAttachment($file, $user_id, $attachmentable, $old_attachment_id = null)
    {
        $new_attachment = Attachment::createFromFile($file, $user_id);
        if ($old_attachment_id) {
            Attachment::destroy($old_attachment_id);
        }
        return $new_attachment;
    }

    /**
     * 入力値のバリデーション
     */
    private static function validateData($data, $rule)
    {
        return Validator::make($data, $rule)->validate();
    }

}
