/* eslint-disable no-console */
const path = require('path');
const fs = require('fs-extra');
const read = require('fs-readdir-recursive');

{
  console.info('copy file to laravel public dir.');
  const targets = [
    { src: path.join(__dirname, '..\\dist\\spa\\css'), dest: path.join(__dirname, '..\\..\\public\\css') },
    { src: path.join(__dirname, '..\\dist\\spa\\js'), dest: path.join(__dirname, '..\\..\\public\\js') },
    { src: path.join(__dirname, '..\\dist\\spa\\fonts'), dest: path.join(__dirname, '..\\..\\public\\fonts') },
    { src: path.join(__dirname, '..\\dist\\spa\\icons'), dest: path.join(__dirname, '..\\..\\public\\icons') },
    { src: path.join(__dirname, '..\\dist\\spa\\images'), dest: path.join(__dirname, '..\\..\\public\\images') },
  ];
  targets.forEach((target) => {
    fs.emptyDirSync(target.dest);
    fs.copySync(target.src, target.dest);
  });
}

{
  console.info('mix-manifest.json generating.');
  const targets = [
    { key: 'js\\vendor.', org: '/js/vendor.js' },
    { key: 'js\\app.', org: '/js/app.js' },
    { key: 'css\\app.', org: '/css/app.css' },
    { key: 'css\\vendor.', org: '/css/vendor.css' },
  ];
  const files = read(path.join(__dirname, '..\\..\\public'));
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
    path.join(__dirname, '..\\..\\public\\mix-manifest.json'),
    JSON.stringify(manifest),
  );
}
