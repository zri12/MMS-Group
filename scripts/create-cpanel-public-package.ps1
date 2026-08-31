[CmdletBinding()]
param(
    [string] $ApplicationPath = "/home/fazriluk/PROJECT MMS WEB ADMIN",
    [string] $PublicDirectoryName = "demoprojectweb.net",
    [string] $OutputDirectory
)

$ErrorActionPreference = "Stop"

$repositoryRoot = (Resolve-Path (Join-Path $PSScriptRoot "..")).Path
$applicationRoot = Join-Path $repositoryRoot "apps\web-admin-api"
$sourcePublic = Join-Path $applicationRoot "public"

if ([string]::IsNullOrWhiteSpace($OutputDirectory)) {
    $OutputDirectory = Join-Path $repositoryRoot "deployment-package"
}

$outputDirectory = [System.IO.Path]::GetFullPath($OutputDirectory)
$outputPath = Join-Path $outputDirectory "$PublicDirectoryName-public.zip"
$stageRoot = Join-Path ([System.IO.Path]::GetTempPath()) ("mms-cpanel-public-" + [guid]::NewGuid())
$stagePublic = Join-Path $stageRoot $PublicDirectoryName

function Ensure-Directory([string] $Path) {
    New-Item -ItemType Directory -Force -Path $Path | Out-Null
}

function Copy-PublicTree() {
    if (-not (Test-Path -LiteralPath $sourcePublic)) {
        throw "Public directory is missing: $sourcePublic"
    }

    Ensure-Directory $stagePublic
    Get-ChildItem -LiteralPath $sourcePublic -Force | Where-Object {
        $_.Name -ne "storage"
    } | ForEach-Object {
        Copy-Item -LiteralPath $_.FullName -Destination (Join-Path $stagePublic $_.Name) -Recurse -Force
    }
}

function Assert-Package([string] $ZipPath) {
    Add-Type -AssemblyName System.IO.Compression
    Add-Type -AssemblyName System.IO.Compression.FileSystem

    $archive = [System.IO.Compression.ZipFile]::OpenRead($ZipPath)
    try {
        $entries = @($archive.Entries | Where-Object { -not $_.FullName.EndsWith("/") } | ForEach-Object { $_.FullName })
        $required = @(
            "$PublicDirectoryName/index.php",
            "$PublicDirectoryName/.htaccess",
            "$PublicDirectoryName/build/manifest.json",
            "$PublicDirectoryName/cpanel_keymigrate.php",
            "$PublicDirectoryName/cpanel_storage.php",
            "$PublicDirectoryName/cpanel_provision_admin.php",
            "$PublicDirectoryName/cpanel_optimize.php",
            "$PublicDirectoryName/cpanel_clear.php"
        )

        foreach ($requiredPath in $required) {
            if ($entries -notcontains $requiredPath) {
                throw "Public ZIP is missing: $requiredPath"
            }
        }

        $forbidden = @($entries | Where-Object {
            ($_ -match "^$([regex]::Escape($PublicDirectoryName))/(\.env|vendor/|storage/|bootstrap/|app/|config/|database/|routes/|resources/)") -or
            ($_ -match "\.log$")
        })

        if ($forbidden.Count -gt 0) {
            throw "Public ZIP contains private files: $($forbidden -join ', ')"
        }

        return $entries.Count
    } finally {
        $archive.Dispose()
    }
}

Ensure-Directory $outputDirectory
if (Test-Path -LiteralPath $outputPath) {
    Remove-Item -LiteralPath $outputPath -Force
}

try {
    Copy-PublicTree

    $phpApplicationPath = $ApplicationPath.Replace("\", "/").Replace("'", "\'")
    $indexContent = @'
<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$applicationPath = '__APPLICATION_PATH__';

if (file_exists($maintenance = $applicationPath.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

require $applicationPath.'/vendor/autoload.php';

/** @var Application $app */
$app = require_once $applicationPath.'/bootstrap/app.php';
$app->usePublicPath(__DIR__);

$app->handleRequest(Request::capture());
'@
    [System.IO.File]::WriteAllText(
        (Join-Path $stagePublic "index.php"),
        $indexContent.Replace("__APPLICATION_PATH__", $phpApplicationPath)
    )

    $setupRequire = "putenv('MMS_CPANEL_PUBLIC_PATH='.__DIR__);`r`n`r`nrequire '$phpApplicationPath/deploy/cpanel/web-setup/bootstrap.php';"
    foreach ($filename in @(
        "cpanel_keymigrate.php",
        "cpanel_storage.php",
        "cpanel_provision_admin.php",
        "cpanel_optimize.php",
        "cpanel_clear.php"
    )) {
        $path = Join-Path $stagePublic $filename
        $content = [System.IO.File]::ReadAllText($path)
        $content = $content.Replace("require __DIR__.'/../deploy/cpanel/web-setup/bootstrap.php';", $setupRequire)
        [System.IO.File]::WriteAllText($path, $content)
    }

    Add-Type -AssemblyName System.IO.Compression
    Add-Type -AssemblyName System.IO.Compression.FileSystem
    $archive = [System.IO.Compression.ZipFile]::Open(
        $outputPath,
        [System.IO.Compression.ZipArchiveMode]::Create
    )
    try {
        $rootUri = [System.Uri]::new($stageRoot.TrimEnd("\") + "\")
        Get-ChildItem -LiteralPath $stageRoot -Recurse -File -Force | ForEach-Object {
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

    Write-Host "Created: $outputPath"
    Write-Host "Application path: $ApplicationPath"
    Write-Host "Public target: /home/fazriluk/public_html/$PublicDirectoryName"
    Write-Host "ZIP entries: $entryCount"
    Write-Host "ZIP size MB: $sizeMb"
} catch {
    if (Test-Path -LiteralPath $outputPath) {
        Remove-Item -LiteralPath $outputPath -Force
    }

    throw
} finally {
    if (Test-Path -LiteralPath $stageRoot) {
        Remove-Item -LiteralPath $stageRoot -Force -Recurse
    }
}
