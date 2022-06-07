// アーカイブDLのデータから抽出する
// tweet.jsは `module.exports = ` に変更して使用する

const tweets = require('./tweet');
const data = tweets.map(t => {
  return {
    id: t.tweet.id_str,
    text: t.tweet.full_text,
    public_metrics: {
      retweet_count: Number(t.tweet.retweet_count),
      like_count: Number(t.tweet.favorite_count),
      reply_count: 0,
      quote_count: 0
    },
    created_at: t.tweet.created_at
  };
});

const fs = require('fs');
fs.writeFileSync('./extracted.json', JSON.stringify(data));
