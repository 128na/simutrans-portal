type Props = {
  onClick: (postType: PostType) => void;
};
export const SelectPostType = ({ onClick }: Props) => {
  return (
    <div className="mx-auto px-6 lg:px-8">
      <p className="mx-auto mt-2 max-w-lg text-center text-xl font-semibold tracking-tight text-balance text-gray-950 sm:text-xl">
        記事の形式を選んでください
      </p>
      <div className="mt-10 grid gap-4 sm:mt-16 lg:grid-cols-3 lg:grid-rows-2">
        <div
          className="relative lg:row-span-2 cursor-pointer hover:bg-brand/10 rounded-lg"
          onClick={() => onClick("addon-post")}
        >
          <div className="relative flex h-full flex-col overflow-hidden">
            <div className="px-8 py-8 pb-3 sm:px-10 sm:py-4">
              <p className="mt-2 text-lg font-medium tracking-tight text-gray-950">
                アドオン投稿
              </p>
              <p className="mt-2 max-w-lg text-sm/6 text-gray-600">
                アドオンファイルをアップロードして投稿記事を作成します。
              </p>
            </div>
          </div>
          <div className="pointer-events-none absolute inset-px rounded-lg shadow-sm outline outline-black/5 "></div>
        </div>

        <div
          className="relative lg:row-span-2 cursor-pointer hover:bg-brand/10 rounded-lg"
          onClick={() => onClick("addon-introduction")}
        >
          <div className="relative flex h-full flex-col overflow-hidden lg:rounded-none">
            <div className="px-8 py-8 pb-3 sm:px-10 sm:py-4">
              <p className="mt-2 text-lg font-medium tracking-tight text-gray-950">
                アドオン紹介
              </p>
              <p className="mt-2 max-w-lg text-sm/6 text-gray-600">
                Wikiや個人サイトなどアドオン掲載ページへのリンクがある紹介記事を作成します。
              </p>
            </div>
          </div>
          <div className="pointer-events-none absolute inset-px rounded-lg shadow-sm outline outline-black/5 "></div>
        </div>

        <div
          className="relative lg:col-start-3 lg:row-start-1 cursor-pointer hover:bg-brand/10 rounded-lg"
          onClick={() => onClick("page")}
        >
          <div className="relative flex h-full flex-col overflow-hidden">
            <div className="px-8 py-8 sm:px-10 sm:py-4">
              <p className="mt-2 text-lg font-medium tracking-tight text-gray-950">
                一般記事
              </p>
              <p className="mt-2 max-w-lg text-sm/6 text-gray-600">
                アドオン以外の文章記事を作成します。Simutransに関する話題であれば自由に使ってください。
              </p>
            </div>
          </div>
          <div className="pointer-events-none absolute inset-px rounded-lg shadow-sm outline outline-black/5 "></div>
        </div>

        <div
          className="relative lg:col-start-3 lg:row-start-2 cursor-pointer hover:bg-brand/10 rounded-lg"
          onClick={() => onClick("markdown")}
        >
          <div className="relative flex h-full flex-col overflow-hidden">
            <div className="px-8 py-8 sm:px-10 sm:py-4">
              <p className="mt-2 text-lg font-medium tracking-tight text-gray-950">
                一般記事（マークダウン）
              </p>
              <p className="mt-2 max-w-lg text-sm/6 text-gray-600">
                マークダウン形式で文章記事を作成します。{" "}
              </p>
            </div>
          </div>
          <div className="pointer-events-none absolute inset-px rounded-lg shadow-sm outline outline-black/5 "></div>
        </div>
      </div>
    </div>
  );
};
