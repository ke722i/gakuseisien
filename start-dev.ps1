$ErrorActionPreference = 'Stop'

$workspace = Resolve-Path $PSScriptRoot
$appUrl = 'http://127.0.0.1:8000'
$viteUrl = 'http://127.0.0.1:5173'

function Test-UrlReady {
    param([string]$Url)

    try {
        Invoke-WebRequest -Uri $Url -UseBasicParsing -TimeoutSec 1 | Out-Null
        return $true
    }
    catch {
        return $false
    }
}

if (-not (Test-UrlReady $appUrl) -or -not (Test-UrlReady $viteUrl)) {
    Start-Process powershell -WindowStyle Hidden -WorkingDirectory $workspace -ArgumentList @(
        '-NoProfile',
        '-ExecutionPolicy',
        'Bypass',
        '-Command',
        'composer run dev'
    )
}

while ($true) {
    if (Test-UrlReady $appUrl -and Test-UrlReady $viteUrl) {
        Start-Process cmd.exe -ArgumentList '/c', 'start', '""', $appUrl
        break
    } else {
        Start-Sleep -Milliseconds 200
    }
}
