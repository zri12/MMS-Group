[CmdletBinding()]
param(
    [string] $OutputDirectory
)

$ErrorActionPreference = "Stop"

$repositoryRoot = (Resolve-Path (Join-Path $PSScriptRoot "..")).Path
$applicationRoot = Join-Path $repositoryRoot "apps\web-admin-api"

if ([string]::IsNullOrWhiteSpace($OutputDirectory)) {
    $OutputDirectory = Join-Path $repositoryRoot "deployment-package"
}

$outputDirectory = [System.IO.Path]::GetFullPath($OutputDirectory)
$outputPath = Join-Path $outputDirectory "mms-web-admin-cpanel.zip"
$setupInfoPath = Join-Path $outputDirectory "mms-web-admin-cpanel-setup.txt"
$stageRoot = Join-Path ([System.IO.Path]::GetTempPath()) ("mms-web-admin-cpanel-" + [guid]::NewGuid())
$stageApplication = Join-Path $stageRoot "mms-web-admin"

function Invoke-Checked([string] $FilePath, [string[]] $Arguments, [string] $WorkingDirectory) {
    Push-Location $WorkingDirectory
    try {
        & $FilePath @Arguments
        if ($LASTEXITCODE -ne 0) {
            throw "Command failed: $FilePath $($Arguments -join ' ')"
        }
    } finally {
        Pop-Location
    }
}

function Ensure-Directory([string] $Path) {
    New-Item -ItemType Directory -Force -Path $Path | Out-Null
}

function Copy-RequiredItem([string] $RelativePath) {
    $source = Join-Path $applicationRoot $RelativePath
    if (-not (Test-Path -LiteralPath $source)) {
        throw "Required deployment item is missing: $RelativePath"
    }

    $destination = Join-Path $stageApplication $RelativePath
    Ensure-Directory (Split-Path -Parent $destination)
    Copy-Item -LiteralPath $source -Destination $destination -Recurse -Force
}

function Add-DirectoryMarker([string] $RelativePath) {
    $directory = Join-Path $stageApplication $RelativePath
    Ensure-Directory $directory
    [System.IO.File]::WriteAllText((Join-Path $directory ".gitignore"), "")
}

function Assert-Package([string] $ZipPath) {
    Add-Type -AssemblyName System.IO.Compression
    Add-Type -AssemblyName System.IO.Compression.FileSystem

    $archive = [System.IO.Compression.ZipFile]::OpenRead($ZipPath)
    try {
        $entries = @($archive.Entries | Where-Object { -not $_.FullName.EndsWith("/") } | ForEach-Object { $_.FullName })
        $backslashEntries = @($entries | Where-Object { $_.Contains("\") })
        if ($backslashEntries.Count -gt 0) {
            throw "Deployment ZIP contains Windows path separators: $($backslashEntries -join ', ')"
        }
        $required = @(
            "mms-web-admin/artisan",
            "mms-web-admin/composer.json",
            "mms-web-admin/composer.lock",
            "mms-web-admin/.env.example",
            "mms-web-admin/vendor/autoload.php",
            "mms-web-admin/public/index.php",
            "mms-web-admin/public/build/manifest.json",
            "mms-web-admin/deploy/cpanel/web-setup/setup-token.php"
        )

        foreach ($requiredPath in $required) {
            if ($entries -notcontains $requiredPath) {
                throw "Deployment ZIP is missing: $requiredPath"
            }
        }

        $forbidden = @($entries | Where-Object {
            ($_ -eq "mms-web-admin/.env") -or
            ($_ -match "^mms-web-admin/node_modules/") -or
            ($_ -match "^mms-web-admin/tests/") -or
            ($_ -match "^mms-web-admin/\.git/") -or
            ($_ -match "^mms-web-admin/storage/(?!.*\.gitignore$)") -or
            ($_ -match "^mms-web-admin/public/storage/") -or
            ($_ -match "^mms-web-admin/bootstrap/cache/(?!.*\.gitignore$)") -or
            ($_ -match "\.log$")
        })

        if ($forbidden.Count -gt 0) {
            throw "Deployment ZIP contains forbidden files: $($forbidden -join ', ')"
        }

        return $entries.Count
    } finally {
        $archive.Dispose()
    }
}

if (-not (Get-Command composer -ErrorAction SilentlyContinue)) {
    throw "Composer must be available to build the cPanel deployment package."
}

if (-not (Get-Command npm -ErrorAction SilentlyContinue)) {
    throw "npm must be available to build the cPanel deployment package."
}

Ensure-Directory $outputDirectory
if (Test-Path -LiteralPath $outputPath) {
    Remove-Item -LiteralPath $outputPath -Force
}
if (Test-Path -LiteralPath $setupInfoPath) {
    Remove-Item -LiteralPath $setupInfoPath -Force
}

try {
    Invoke-Checked "npm" @("ci") $applicationRoot
    Invoke-Checked "npm" @("run", "build") $applicationRoot

    Ensure-Directory $stageApplication
    foreach ($relativePath in @(
        "app",
        "bootstrap",
        "config",
        "database",
        "deploy",
        "public",
        "resources",
        "routes",
        ".env.example",
        ".gitattributes",
        ".gitignore",
        "artisan",
        "composer.json",
        "composer.lock",
        "package.json",
        "package-lock.json",
        "README.md",
        "vite.config.js"
    )) {
        Copy-RequiredItem $relativePath
    }

    $setupTokenBytes = New-Object byte[] 32
    $randomNumberGenerator = [System.Security.Cryptography.RandomNumberGenerator]::Create()
    try {
        $randomNumberGenerator.GetBytes($setupTokenBytes)
    } finally {
        $randomNumberGenerator.Dispose()
    }
    $setupToken = ($setupTokenBytes | ForEach-Object { $_.ToString("x2") }) -join ""
    $setupTokenFile = Join-Path $stageApplication "deploy\cpanel\web-setup\setup-token.php"
    [System.IO.File]::WriteAllText(
        $setupTokenFile,
        "<?php`r`n`r`ndeclare(strict_types=1);`r`n`r`nreturn '$setupToken';`r`n"
    )

    Remove-Item -LiteralPath (Join-Path $stageApplication "public\storage") -Force -Recurse -ErrorAction SilentlyContinue
    Get-ChildItem -LiteralPath (Join-Path $stageApplication "bootstrap\cache") -Force |
        Where-Object { $_.Name -ne ".gitignore" } |
        Remove-Item -Force -Recurse

    Remove-Item -LiteralPath (Join-Path $stageApplication "storage") -Force -Recurse -ErrorAction SilentlyContinue
    foreach ($relativePath in @(
        "storage\app\private",
        "storage\app\public",
        "storage\framework\cache\data",
        "storage\framework\sessions",
        "storage\framework\views",
        "storage\logs"
    )) {
        Add-DirectoryMarker $relativePath
    }

    Invoke-Checked "composer" @("install", "--no-dev", "--optimize-autoloader", "--no-interaction", "--prefer-dist") $stageApplication
    Get-ChildItem -LiteralPath (Join-Path $stageApplication "bootstrap\cache") -Force |
        Where-Object { $_.Name -ne ".gitignore" } |
        Remove-Item -Force -Recurse

    Add-Type -AssemblyName System.IO.Compression
    Add-Type -AssemblyName System.IO.Compression.FileSystem
    $archive = [System.IO.Compression.ZipFile]::Open(
        $outputPath,
        [System.IO.Compression.ZipArchiveMode]::Create
    )
    try {
        $rootUri = [System.Uri]::new($stageRoot.TrimEnd("\") + "\")
        Get-ChildItem -LiteralPath $stageRoot -Recurse -File | ForEach-Object {
            $relativePath = [System.Uri]::UnescapeDataString(
                $rootUri.MakeRelativeUri([System.Uri]::new($_.FullName)).ToString()
            ).Replace("\", "/")

            [System.IO.Compression.ZipFileExtensions]::CreateEntryFromFile(
                $archive,
                $_.FullName,
                $relativePath,
                [System.IO.Compression.CompressionLevel]::Optimal
            ) | Out-Null
        }
    } finally {
        $archive.Dispose()
    }
    $entryCount = Assert-Package $outputPath
    $sizeMb = [math]::Round((Get-Item -LiteralPath $outputPath).Length / 1MB, 2)
    [System.IO.File]::WriteAllText(
        $setupInfoPath,
        "MMS cPanel Web Setup Token`r`n`r`nToken: $setupToken`r`n`r`nAfter extracting the ZIP and pointing the domain to mms-web-admin/public, open:`r`nhttps://YOUR-DOMAIN/cpanel_keymigrate.php?token=$setupToken`r`n`r`nKeep this file private. After setup, use cpanel_clear.php with the same token to disable all setup pages, then delete cpanel_*.php via cPanel File Manager.`r`n"
    )

    Write-Host "Created: $outputPath"
    Write-Host "Setup token: $setupInfoPath"
    Write-Host "ZIP entries: $entryCount"
    Write-Host "ZIP size MB: $sizeMb"
} catch {
    if (Test-Path -LiteralPath $outputPath) {
        Remove-Item -LiteralPath $outputPath -Force
    }
    if (Test-Path -LiteralPath $setupInfoPath) {
        Remove-Item -LiteralPath $setupInfoPath -Force
    }

    throw
} finally {
    if (Test-Path -LiteralPath $stageRoot) {
        Remove-Item -LiteralPath $stageRoot -Force -Recurse
    }
}
