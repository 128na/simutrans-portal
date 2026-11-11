<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mypage;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\Article\ConversionCountRepository;
use App\Repositories\Article\MypageArticleRepository;
use App\Repositories\Article\ViewCountRepository;
use App\Repositories\LoginHistoryRepository;
use App\Repositories\UserRepository;
use App\Services\Front\MetaOgpService;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Laravel\Fortify\Features;

class DashboardController extends Controller
{
    /** マイページトップに出す最近更新した記事の件数 */
    private const int RECENT_ARTICLE_LIMIT = 5;

    /** 推移を出す日数（今日を含む） */
    private const int TREND_DAYS = 30;

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly MypageArticleRepository $mypageArticleRepository,
        private readonly LoginHistoryRepository $loginHistoryRepository,
        private readonly ViewCountRepository $viewCountRepository,
        private readonly ConversionCountRepository $conversionCountRepository,
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function index(): View
    {
        $user = $this->loggedinUser();
        $recentArticles = $this->mypageArticleRepository->getRecentForMypage($user, self::RECENT_ARTICLE_LIMIT);
        $summary = $this->userRepository->getSummary($user);

        return view('mypage.index', [
            'user' => $user,
            'summary' => $summary,
            'recentArticles' => $recentArticles,
            'trend' => $this->buildTrend($user, (int) $summary->published_article_count),
            // 未認証のうちは二要素認証の設定画面に入れないので、メール認証の帯だけを出す
            'showTwoFactorNotice' => Features::enabled(Features::twoFactorAuthentication())
                && $user->hasVerifiedEmail()
                && ! $user->hasEnabledTwoFactorAuthentication(),
            'meta' => $this->metaOgpService->mypage(),
        ]);
    }

    public function verifyEmail(): View
    {
        return view('mypage.verify-email', [
            'meta' => $this->metaOgpService->mypageVerifyEmail(),
        ]);
    }

    public function verifyNotice(): RedirectResponse
    {
        return to_route('mypage.verify-email')
            ->with('error', 'この機能を使うにはメールアドレスの認証を完了させる必要があります。');
    }

    public function twoFactor(): View
    {
        $user = $this->loggedinUser();

        return view('mypage.two-factor', [
            'user' => $user,
            'meta' => $this->metaOgpService->mypageTwoFactor(),
        ]);
    }

    public function loginHistories(): View
    {
        $user = $this->loggedinUser();

        return view('mypage.login-histories', [
            'loginHistories' => $this->loginHistoryRepository->getByUser($user->id),
            'meta' => $this->metaOgpService->mypageLoginHistories(),
        ]);
    }

    /**
     * ユーザー全体の直近の日次PV・CVの合計を、アナリティクスと同じ形式の1件にして返す.
     * 今月のPV・CVカードと同じく、記事の状態を問わずユーザーの全カウントを対象にする。
     * 公開中の記事が無いときは、フロントが案内を出せるよう合計を null にする。
     *
     * @return array{start_date:string,end_date:string,total:array{id:int,viewCounts:array<string,int>,conversionCounts:array<string,int>,pastViewCount:int,pastConversionCount:int}|null}
     */
    private function buildTrend(User $user, int $publishedArticleCount): array
    {
        $endDate = CarbonImmutable::today();
        $startDate = $endDate->subDays(self::TREND_DAYS - 1);
        $from = $startDate->format('Ymd');
        $to = $endDate->format('Ymd');

        $total = $publishedArticleCount === 0
            ? null
            : [
                'id' => 0,
                'viewCounts' => $this->viewCountRepository->getDailyTotals($user, $from, $to),
                'conversionCounts' => $this->conversionCountRepository->getDailyTotals($user, $from, $to),
                'pastViewCount' => 0,
                'pastConversionCount' => 0,
            ];

        return [
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'total' => $total,
        ];
    }
}
