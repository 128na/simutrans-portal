export const useClipboard = () => {
  const write = async (message) => {
    try {
      await navigator.clipboard.writeText(message);
    } finally {
      // do nothing
    }
  };
  return {
    write,
  };
};
