<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mypage;

use App\Enums\ArticleAnalyticsType;
use App\Http\Controllers\Controller;
use App\Http\Resources\Mypage\ArticleAnalytic;
use App\Models\User;
use App\Repositories\Article\MypageArticleRepository;
use App\Repositories\LoginHistoryRepository;
use App\Repositories\UserRepository;
use App\Services\Front\MetaOgpService;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Laravel\Fortify\Features;

class DashboardController extends Controller
{
    /** マイページトップに出す最近更新した記事の件数（推移は公開中の記事からこの件数を対象にする） */
    private const int RECENT_ARTICLE_LIMIT = 5;

    /** 推移を出す日数（今日を含む） */
    private const int TREND_DAYS = 30;

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly MypageArticleRepository $mypageArticleRepository,
        private readonly LoginHistoryRepository $loginHistoryRepository,
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function index(): View
    {
        $user = $this->loggedinUser();
        $recentArticles = $this->mypageArticleRepository->getRecentForMypage($user, self::RECENT_ARTICLE_LIMIT);

        return view('mypage.index', [
            'user' => $user,
            'summary' => $this->userRepository->getSummary($user),
            'recentArticles' => $recentArticles,
            'trend' => $this->buildTrend($user),
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
     * 公開中で最近更新した記事の直近の日次PV・CVを、アナリティクスと同じ取得経路・同じ形式で返す
     *
     * @return array{start_date:string,end_date:string,analytics:array<mixed>}
     */
    private function buildTrend(User $user): array
    {
        $endDate = CarbonImmutable::today();
        $startDate = $endDate->subDays(self::TREND_DAYS - 1);
        $ids = $this->mypageArticleRepository->getRecentPublishedIds($user, self::RECENT_ARTICLE_LIMIT);

        $analytics = $ids === []
            ? []
            : ArticleAnalytic::collection(
                $this->mypageArticleRepository->findAllForAnalytics(
                    $user,
                    $ids,
                    ArticleAnalyticsType::Daily,
                    [$startDate->format('Ymd'), $endDate->format('Ymd')],
                )
            )->resolve();

        return [
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'analytics' => $analytics,
        ];
    }
}
