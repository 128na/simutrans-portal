/* eslint-disable no-console */
const path = require('path');

const s = path.sep;
const fs = require('fs-extra');
const read = require('fs-readdir-recursive');

{
  console.info('copy file to laravel public dir.');
  const targets = [
    { src: path.join(__dirname, `..${s}dist${s}spa${s}css`), dest: path.join(__dirname, `..${s}..${s}public${s}css`) },
    { src: path.join(__dirname, `..${s}dist${s}spa${s}js`), dest: path.join(__dirname, `..${s}..${s}public${s}js`) },
    { src: path.join(__dirname, `..${s}dist${s}spa${s}fonts`), dest: path.join(__dirname, `..${s}..${s}public${s}fonts`) },
    { src: path.join(__dirname, `..${s}dist${s}spa${s}icons`), dest: path.join(__dirname, `..${s}..${s}public${s}icons`) },
    { src: path.join(__dirname, `..${s}dist${s}spa${s}images`), dest: path.join(__dirname, `..${s}..${s}public${s}images`) },
  ];
  targets.forEach((target) => {
    fs.emptyDirSync(target.dest);
    fs.copySync(target.src, target.dest);
  });
}

{
  console.info('mix-manifest.json generating.');
  const targets = [
    { key: `js${s}vendor.`, org: '/js/vendor.js' },
    { key: `js${s}app.`, org: '/js/app.js' },
    { key: `css${s}app.`, org: '/css/app.css' },
    { key: `css${s}vendor.`, org: '/css/vendor.css' },
  ];
  const files = read(path.join(__dirname, `..${s}..${s}public`));
  const manifest = {};
  targets.forEach((t) => {
    const item = files.find((f) => f.startsWith(t.key));
    if (!item) {
      throw new Error(`target '${t.key}' not found!`);
    }
    manifest[t.org] = `/${item.replaceAll('\\', '/')}`;
  });
  console.log({ manifest });
  fs.writeFileSync(
    path.join(__dirname, `..${s}..${s}public${s}mix-manifest.json`),
    JSON.stringify(manifest),
  );
}
