export function routeTo(router) {
  return ({ to }) => {
    if (to) {
      router.push(to);
    }
  };
}

export function staticUrls() {
  return {
    feedUrl: `${process.env.BACKEND_URL}/feed`,
  };
}
