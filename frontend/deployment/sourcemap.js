/* eslint-disable no-console */
require('dotenv').config();
const path = require('path');
const { publishSourcemap } = require('@newrelic/publish-sourcemap');
const read = require('fs-readdir-recursive');

const uploadSourceMaps = (files) => {
  for (let index = 0; index < files.length; index += 2) {
    const file = files[index];
    const source = files[index + 1];
    const data = {
      sourcemapUrl: `${process.env.BACKEND_URL}/js/${source}`,
      javascriptUrl: `${process.env.BACKEND_URL}/js/${file}`,
      applicationId: 601355519,
      apiKey: process.env.NR_USER_KEY,
      releaseName: process.env.APP_VERSION,
      releaseId: process.env.APP_VERSION,
      repoUrl: 'https://github.com/128na/simutrans-portal',
    };
    publishSourcemap(data, (err) => { console.log(err || 'Source map upload done'); });
  }
};

const s = path.sep;
const jsDir = path.join(__dirname, `..${s}..${s}public`, 'js');
const files = read(jsDir);
uploadSourceMaps(files);
