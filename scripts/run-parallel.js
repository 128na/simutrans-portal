#!/usr/bin/env node
/* eslint-disable no-console */
/* eslint-disable no-undef */

import { spawn } from 'child_process';

/**
 * 並列実行スクリプト
 *
 * 使用方法:
 *   node scripts/run-parallel.js "task1:command1" "task2:command2" ...
 *
 * 例:
 *   node scripts/run-parallel.js "eslint:npm run lint" "prettier:npm run format"
 *
 * 各タスクは "タスク名:コマンド" の形式で指定します。
 * すべてのタスクを並列実行し、最後にサマリを表示します。
 */

const args = process.argv.slice(2);

if (args.length === 0) {
  console.error('Error: No tasks specified');
  console.error('Usage: node scripts/run-parallel.js "name:command" "name:command" ...');
  console.error('Example: node scripts/run-parallel.js "eslint:npm run lint" "prettier:npm run format"');
  process.exit(1);
}

const tasks = args.map(arg => {
  const colonIndex = arg.indexOf(':');
  if (colonIndex === -1) {
    console.error(`Error: Invalid task format "${arg}". Expected "name:command"`);
    process.exit(1);
  }

  const name = arg.substring(0, colonIndex);
  const commandString = arg.substring(colonIndex + 1);

  // コマンドをスペースで分割してコマンドと引数に分ける
  const parts = commandString.split(' ');
  const cmd = parts[0];
  const cmdArgs = parts.slice(1);

  return { name, cmd, args: cmdArgs, fullCommand: commandString };
});

console.log('=== Starting parallel tasks ===\n');
tasks.forEach(t => {
  console.log(`  ${t.name}: ${t.fullCommand}`);
});
console.log('');

const results = [];
const startTime = Date.now();

await Promise.all(
  tasks.map(t =>
    new Promise(resolve => {
      const taskStartTime = Date.now();
      console.log(`[${t.name}] Starting...`);

      const p = spawn(t.cmd, t.args, {
        stdio: 'inherit',
        shell: true
      });

      p.on('close', code => {
        const duration = ((Date.now() - taskStartTime) / 1000).toFixed(2);
        results.push({
          name: t.name,
          code,
          duration,
          command: t.fullCommand
        });
        console.log(`[${t.name}] Finished in ${duration}s with code ${code}`);
        resolve();
      });

      p.on('error', err => {
        console.error(`[${t.name}] Error:`, err.message);
        results.push({
          name: t.name,
          code: 1,
          duration: '0.00',
          command: t.fullCommand,
          error: err.message
        });
        resolve();
      });
    })
  )
);

const totalDuration = ((Date.now() - startTime) / 1000).toFixed(2);

console.log('\n' + '='.repeat(50));
console.log('=== Summary ===');
console.log('='.repeat(50));

const failed = [];
const passed = [];

for (const r of results) {
  const status = r.code === 0 ? '✓ PASSED' : '✗ FAILED';
  const icon = r.code === 0 ? '✓' : '✗';
  console.log(`${icon} ${r.name.padEnd(20)} ${status} (${r.duration}s)`);

  if (r.code === 0) {
    passed.push(r);
  } else {
    failed.push(r);
  }
}

console.log('='.repeat(50));
console.log(`Total: ${results.length} tasks, ${passed.length} passed, ${failed.length} failed (${totalDuration}s)`);

if (failed.length > 0) {
  console.log('\nFailed tasks:');
  failed.forEach(r => {
    console.log(`  - ${r.name}: ${r.command}`);
  });
}

console.log('='.repeat(50) + '\n');

process.exit(failed.length > 0 ? 1 : 0);
