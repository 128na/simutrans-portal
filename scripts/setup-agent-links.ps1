param([switch]$Check)

$ErrorActionPreference = 'Stop'
$projectRoot = [IO.Path]::GetFullPath((Join-Path $PSScriptRoot '..'))
$source = Join-Path $projectRoot '.agents\skills'
$destination = Join-Path $projectRoot '.claude\skills'

if (-not (Test-Path -LiteralPath (Join-Path $source 'laravel-best-practices\SKILL.md'))) {
    throw "Shared skills are missing: $source"
}

$existing = Get-Item -LiteralPath $destination -Force -ErrorAction SilentlyContinue
if ($existing -and ($existing.Attributes -band [IO.FileAttributes]::ReparsePoint)) {
    $target = [IO.Path]::GetFullPath($existing.Target)
    if ($target -ne $source) { throw "Existing link points elsewhere: $target" }
    Write-Output "OK: $destination -> $source"
    exit 0
}
if ($Check) { throw "Shared skill junction is missing: $destination" }

$backup = $null
if ($existing) {
    # Verify exact project-local paths before moving a directory.
    $resolvedDestination = (Resolve-Path -LiteralPath $destination).Path
    if ($resolvedDestination -ne $destination -or -not $existing.PSIsContainer) {
        throw "Refusing to move unexpected target: $resolvedDestination"
    }
    $backup = Join-Path $projectRoot ('.claude\skill-backups\' + (Get-Date -Format 'yyyyMMdd-HHmmss') + '-' + [guid]::NewGuid().ToString('N'))
    New-Item -ItemType Directory -Path (Split-Path -Parent $backup) -Force | Out-Null
    Move-Item -LiteralPath $destination -Destination $backup
}
try {
    New-Item -ItemType Directory -Path (Split-Path -Parent $destination) -Force | Out-Null
    New-Item -ItemType Junction -Path $destination -Target $source | Out-Null
} catch {
    if ($backup -and -not (Test-Path -LiteralPath $destination)) {
        Move-Item -LiteralPath $backup -Destination $destination
    }
    throw
}
if ($backup) { Write-Output "Backup: $backup" }
Write-Output "Linked: $destination -> $source"
