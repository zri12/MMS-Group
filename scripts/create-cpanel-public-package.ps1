[CmdletBinding()]
param(
    [string] $OutputDirectory,
    [string] $ProjectPath = "/home/fazriluk/PROJECT MESS MONITORING",
    [string] $DocumentRoot = "/home/fazriluk/public_html/demoprojectweb.net"
)

$ErrorActionPreference = "Stop"

$repositoryRoot = (Resolve-Path (Join-Path $PSScriptRoot "..")).Path
$applicationRoot = Join-Path $repositoryRoot "apps\web-admin-api"
$sourcePublic = Join-Path $applicationRoot "public"

if ([string]::IsNullOrWhiteSpace($OutputDirectory)) {
    $OutputDirectory = Join-Path $repositoryRoot "deployment-package"
}

$outputDirectory = [System.IO.Path]::GetFullPath($OutputDirectory)
$outputPath = Join-Path $outputDirectory "demoprojectweb.net-public.zip"
$stageRoot = Join-Path ([System.IO.Path]::GetTempPath()) ("mms-public-cpanel-" + [guid]::NewGuid())
$stagePublic = Join-Path $stageRoot "demoprojectweb.net"

function Ensure-Directory([string] $Path) {
    New-Item -ItemType Directory -Force -Path $Path | Out-Null
}

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

function Copy-PublicContents() {
    Ensure-Directory $stagePublic

    Get-ChildItem -LiteralPath $sourcePublic -Force | ForEach-Object {
        if ($_.Name -eq "storage") {
            return
        }

        Copy-Item -LiteralPath $_.FullName -Destination (Join-Path $stagePublic $_.Name) -Recurse -Force
    }
}

function Assert-Package([string] $ZipPath, [string] $ExpectedProjectPath) {
    Add-Type -AssemblyName System.IO.Compression
    Add-Type -AssemblyName System.IO.Compression.FileSystem

    $archive = [System.IO.Compression.ZipFile]::OpenRead($ZipPath)
    try {
        $entries = @($archive.Entries | Where-Object { -not $_.FullName.EndsWith("/") } | ForEach-Object { $_.FullName })
        $required = @(
            "demoprojectweb.net/index.php",
            "demoprojectweb.net/.htaccess",
            "demoprojectweb.net/build/manifest.json",
            "demoprojectweb.net/cpanel_keymigrate.php",
            "demoprojectweb.net/cpanel_storage.php",
            "demoprojectweb.net/cpanel_provision_admin.php",
            "demoprojectweb.net/cpanel_optimize.php",
            "demoprojectweb.net/cpanel_clear.php"
        )

        foreach ($requiredPath in $required) {
            if ($entries -notcontains $requiredPath) {
                throw "Public ZIP is missing: $requiredPath"
            }
        }

        if ($entries | Where-Object { $_ -match "^demoprojectweb\.net/storage/" -or $_ -match "^demoprojectweb\.net/\.env$" }) {
            throw "Public ZIP contains forbidden runtime files."
        }

        $indexEntry = $archive.GetEntry("demoprojectweb.net/index.php")
        $reader = [System.IO.StreamReader]::new($indexEntry.Open())
        try {
            $indexContent = $reader.ReadToEnd()
        } finally {
            $reader.Dispose()
        }

        if (-not $indexContent.Contains($ExpectedProjectPath)) {
            throw "Public index.php does not target the requested project path."
        }

        return $entries.Count
    } finally {
        $archive.Dispose()
    }
}

if (-not (Test-Path -LiteralPath $sourcePublic)) {
    throw "Laravel public directory is missing: $sourcePublic"
}
if (-not (Get-Command npm -ErrorAction SilentlyContinue)) {
    throw "npm must be available to build the public deployment package."
}

Ensure-Directory $outputDirectory
if (Test-Path -LiteralPath $outputPath) {
    Remove-Item -LiteralPath $outputPath -Force
}

try {
    Invoke-Checked "npm" @("ci") $applicationRoot
    Invoke-Checked "npm" @("run", "build") $applicationRoot
    Copy-PublicContents

    $phpProjectPath = $ProjectPath.Replace("\", "/").Replace("'", "\\'")
    $indexTemplate = @'
<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$projectRoot = '__PROJECT_ROOT__';

if (! is_file($projectRoot.'/vendor/autoload.php')) {
    http_response_code(500);
    exit('Laravel project files are not available.');
}

if (file_exists($maintenance = $projectRoot.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

require $projectRoot.'/vendor/autoload.php';

/** @var Application $app */
$app = require_once $projectRoot.'/bootstrap/app.php';

$app->handleRequest(Request::capture());
'@
    [System.IO.File]::WriteAllText(
        (Join-Path $stagePublic "index.php"),
        $indexTemplate.Replace("__PROJECT_ROOT__", $phpProjectPath)
    )

    $bootstrapRequire = "require '$phpProjectPath/deploy/cpanel/web-setup/bootstrap.php';"
    foreach ($fileName in @(
        "cpanel_keymigrate.php",
        "cpanel_storage.php",
        "cpanel_provision_admin.php",
        "cpanel_optimize.php",
        "cpanel_clear.php"
    )) {
        $path = Join-Path $stagePublic $fileName
        $content = [System.IO.File]::ReadAllText($path)
        $content = $content.Replace(
            "require __DIR__.'/../deploy/cpanel/web-setup/bootstrap.php';",
            $bootstrapRequire
        )
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

    $entryCount = Assert-Package $outputPath $phpProjectPath
    $sizeMb = [math]::Round((Get-Item -LiteralPath $outputPath).Length / 1MB, 2)

    Write-Host "Created: $outputPath"
    Write-Host "Project path: $ProjectPath"
    Write-Host "Document root: $DocumentRoot"
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
