export const useMeta = () => {
  const setTitle = (title) => {
    window.document.title = (title && title !== 'top') ? `${title} - ${process.env.APP_NAME}` : process.env.APP_NAME;
  };

  return {
    setTitle,
  };
};
