/* eslint-disable no-console */
const path = require('path');
const fs = require('fs-extra');
const read = require('fs-readdir-recursive');

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

console.info('rename file verisoning.');
const replaces = [
  { key: 'js\\vendor.', dest: 'js\\vendor.js' },
  { key: 'js\\app.', dest: 'js\\app.js' },
  { key: 'css\\app.', dest: 'css\\app.css' },
  { key: 'css\\vendor.', dest: 'css\\vendor.css' },
];
const files = read(path.join(__dirname, '..\\..\\public'));
files.forEach((file) => {
  replaces.forEach((replace) => {
    if (file.startsWith(replace.key)) {
      fs.renameSync(path.join(__dirname, '..\\..\\public', file), path.join(__dirname, '..\\..\\public', replace.dest));
    }
  });
});
