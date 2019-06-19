<?php

namespace App\Http\Controllers\Mypage;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Attachment;
use App\Models\Category;
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
        $categories = Category::all()->separateByType();
        return view('mypage.articles.create', compact('post_type', 'categories'));
    }

    /**
     * 更新画面
     */
    public function edit(Article $article)
    {
        $article->load('categories', 'attachments');
        $categories = Category::all()->separateByType();
        return view('mypage.articles.edit', compact('article', 'categories'));
    }

    /**
     * 登録
     */
    public function store(Request $request)
    {
        self::validateData($request->all(), static::getValidateRule());

        $article = Article::make([
            'user_id' => Auth::id(),
            'title'   => $request->input('title'),
            'slug'    => $request->filled('slug') ? $request->input('slug') : $request->input('title'),
            'status'  => $request->input('status'),
        ]);

        if (!$article->isUniqueSlug()) {
            session()->flash('error', 'slug is not unique');
            return back()->withInput();
        }
        $article->save();

        // add post type category
        $post_type = Category::post()->where('slug', $this->post_type)->firstOrFail();
        $article->categories()->sync($post_type->id);

        // contents, attachments
        $attachments = [];
        if ($request->hasFile('thumbnail')) {
            $thumbnail = self::saveAttachment($request->file('thumbnail'), Auth::id(), $article, $contents['thumbnail'] ?? null);
            $article->setContents('thumbnail', $thumbnail->id);
            $attachments[] = $thumbnail;
        }
        $attachments = $this->saveContents($request, $article, $attachments);
        $article->attachments()->saveMany($attachments);

        session()->flash('success', "Article \"{$article->title}\" was created as \"{$article->status}\"");
        return redirect()->route('mypage.index');
    }


    /**
     * 更新
     */
    public function update(Request $request, Article $article)
    {
        self::validateData($request->all(), static::getValidateRule($article));

        $article->fill([
            'title'   => $request->input('title'),
            'slug'    => $request->filled('slug') ? $request->input('slug') : $request->input('title'),
            'status'  => $request->input('status'),
        ]);

        if (!$article->isUniqueSlug()) {
            session()->flash('error', 'slug is not unique');
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

        session()->flash('success', "Article \"{$article->title}\" was updated as \"{$article->status}\"");
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
