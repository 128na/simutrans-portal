/* eslint-disable no-console */
// Consolidated coverage report: frontend (Vitest JSON) + backend (PHPUnit Clover XML)
// Outputs a text summary to the console.

import fs from 'fs';
import path from 'path';

function readJson(filePath) {
  try {
    const content = fs.readFileSync(filePath, 'utf-8');
    return JSON.parse(content);
  } catch {
    return null;
  }
}

function readText(filePath) {
  try {
    return fs.readFileSync(filePath, 'utf-8');
  } catch {
    return null;
  }
}

function formatPct(num) {
  return `${(Math.round(num * 10) / 10).toFixed(1)}%`;
}

function computeFrontend(json) {
  if (!json) return null;

  let stTotal = 0, stCovered = 0;
  let brTotal = 0, brCovered = 0;
  let fnTotal = 0, fnCovered = 0;
  let lnTotal = 0, lnCovered = 0;

  for (const file of Object.values(json)) {
    const s = file.s || {};
    const f = file.f || {};
    const b = file.b || {};
    const l = file.l || {};

    const sKeys = Object.keys(s);
    stTotal += sKeys.length;
    stCovered += sKeys.filter((k) => (s[k] || 0) > 0).length;

    const fKeys = Object.keys(f);
    fnTotal += fKeys.length;
    fnCovered += fKeys.filter((k) => (f[k] || 0) > 0).length;

    for (const arr of Object.values(b)) {
      const branches = Array.isArray(arr) ? arr : [];
      brTotal += branches.length;
      brCovered += branches.filter((hits) => (hits || 0) > 0).length;
    }

    const lKeys = Object.keys(l);
    lnTotal += lKeys.length;
    lnCovered += lKeys.filter((ln) => (l[ln] || 0) > 0).length;
  }

  const result = {
    statements: { total: stTotal, covered: stCovered, pct: stTotal ? (stCovered / stTotal) * 100 : 0 },
    branches: { total: brTotal, covered: brCovered, pct: brTotal ? (brCovered / brTotal) * 100 : 0 },
    functions: { total: fnTotal, covered: fnCovered, pct: fnTotal ? (fnCovered / fnTotal) * 100 : 0 },
    lines: { total: lnTotal, covered: lnCovered, pct: lnTotal ? (lnCovered / lnTotal) * 100 : 0 },
  };
  return result;
}

function parseCloverMetrics(xml) {
  if (!xml) return null;
  // Collect all <metrics .../> occurrences and pick the one with the largest totals
  const re = /<metrics\b([^>]*)\/>/gi;
  let match;
  let best = null;

  function parseAttrs(attrs) {
    const getAttr = (name) => {
      const m = attrs.match(new RegExp(`${name}="([^"]+)"`));
      return m ? Number(m[1]) : 0;
    };
    const statements = getAttr('statements');
    const coveredStatements = getAttr('coveredstatements');
    const conditionals = getAttr('conditionals');
    const coveredConditionals = getAttr('coveredconditionals');
    const methods = getAttr('methods');
    const coveredMethods = getAttr('coveredmethods');
    const elements = getAttr('elements');
    const coveredElements = getAttr('coveredelements');
    return {
      statements: { total: statements, covered: coveredStatements, pct: statements ? (coveredStatements / statements) * 100 : 0 },
      branches: { total: conditionals, covered: coveredConditionals, pct: conditionals ? (coveredConditionals / conditionals) * 100 : 0 },
      functions: { total: methods, covered: coveredMethods, pct: methods ? (coveredMethods / methods) * 100 : 0 },
      elements: { total: elements, covered: coveredElements, pct: elements ? (coveredElements / elements) * 100 : 0 },
    };
  }

  while ((match = re.exec(xml)) !== null) {
    const candidate = parseAttrs(match[1]);
    // Choose the metrics with the largest statements total; fall back to elements if equal/zero
    const currentScore = (best ? best.statements.total : 0);
    const candidateScore = candidate.statements.total;
    if (!best || candidateScore > currentScore || (
      candidateScore === currentScore && candidate.elements.total > (best ? best.elements.total : 0)
    )) {
      best = candidate;
    }
  }
  return best;
}

function combineTotals(front, back) {
  if (!front && !back) return null;
  const combineMetric = (f, b, key) => {
    const ft = f ? f[key].total : 0;
    const fc = f ? f[key].covered : 0;
    const bt = b ? b[key].total : 0;
    const bc = b ? b[key].covered : 0;
    const total = ft + bt;
    const covered = fc + bc;
    return { total, covered, pct: total ? (covered / total) * 100 : 0 };
  };
  return {
    statements: combineMetric(front, back, 'statements'),
    branches: combineMetric(front, back, 'branches'),
    functions: combineMetric(front, back, 'functions'),
  };
}

function printSection(title) {
  console.log(`\n=== ${title} ===`);
}

function printMetricRow(label, metric) {
  if (!metric) {
    console.log(`${label}: N/A`);
    return;
  }
  console.log(`${label}: ${formatPct(metric.pct)} (${metric.covered}/${metric.total})`);
}

function main() {
  const frontPath = path.resolve('coverage/front/coverage-final.json');
  const backCloverPath = path.resolve('coverage/back/clover.xml');

  const frontJson = readJson(frontPath);
  const backXml = readText(backCloverPath);

  const front = computeFrontend(frontJson);
  const back = parseCloverMetrics(backXml);

  console.log('Coverage Report');
  console.log('Source:');
  console.log(` - Front: ${frontJson ? frontPath : 'not found'}`);
  console.log(` - Back (clover): ${backXml ? backCloverPath : 'not found'}`);

  if (front) {
    printSection('Frontend');
    printMetricRow('Statements', front.statements);
    printMetricRow('Branches', front.branches);
    printMetricRow('Functions', front.functions);
    printMetricRow('Lines', front.lines);
  } else {
    printSection('Frontend');
    console.log('No frontend coverage JSON found.');
  }

  if (back) {
    printSection('Backend');
    printMetricRow('Statements', back.statements);
    printMetricRow('Branches', back.branches);
    printMetricRow('Functions', back.functions);
    printMetricRow('Elements', back.elements);
  } else {
    printSection('Backend');
    console.log('No backend Clover XML found.');
  }

  const overall = combineTotals(front, back);
  if (overall) {
    printSection('Overall (Front + Back)');
    printMetricRow('Statements', overall.statements);
    printMetricRow('Branches', overall.branches);
    printMetricRow('Functions', overall.functions);
  }

  console.log('\nDone.');
}

main();
