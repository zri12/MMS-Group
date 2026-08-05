param(
    [switch] $DryRun
)

$ErrorActionPreference = "Stop"

$root = (Resolve-Path (Join-Path $PSScriptRoot "..")).Path
$timestamp = Get-Date -Format "yyyyMMdd-HHmm"
$outputDirectory = Join-Path $root "dist-package"
$outputPath = Join-Path $outputDirectory "mms-monitoring-source-$timestamp.zip"

$excludedDirectoryNames = @(
    ".git",
    ".codex-artifacts",
    ".vercel",
    ".vite",
    ".phpunit.cache",
    "vendor",
    "node_modules",
    "dist",
    "build",
    "coverage"
)

$excludedRelativePatterns = @(
    ".env",
    ".phpunit.result.cache",
    "apps/web-admin-api/public/build",
    "apps/web-admin-api/database/database.sqlite",
    "apps/web-admin-api/database/testing.sqlite",
    "apps/web-admin-api/storage/testing.sqlite",
    "apps/web-admin-api/storage/logs",
    "apps/web-admin-api/storage/framework/views",
    "apps/web-admin-api/storage/framework/cache",
    "apps/web-admin-api/storage/framework/sessions",
    "apps/web-admin-api/bootstrap/cache",
    "dist-package"
)

$excludedExtensions = @(".zip", ".rar", ".7z", ".log", ".tmp")
$sensitivePatterns = @(
    ".env",
    ".env.local",
    ".env.production",
    ".env.testing",
    "database.sqlite",
    "testing.sqlite"
)

function Convert-ToRelativePath([string] $Path) {
    $rootUri = [System.Uri]::new(($root.TrimEnd("\") + "\"))
    $pathUri = [System.Uri]::new($Path)

    return [System.Uri]::UnescapeDataString($rootUri.MakeRelativeUri($pathUri).ToString()).Replace("\", "/")
}

function Test-IsEnvExample([string] $RelativePath) {
    return $RelativePath -eq ".env.example" -or $RelativePath.EndsWith("/.env.example")
}

function Test-MatchesExcludedPattern([string] $RelativePath) {
    if (Test-IsEnvExample $RelativePath) {
        return $false
    }

    if ($RelativePath -like ".env.*" -or $RelativePath -like "*/.env.*") {
        return $true
    }

    if ($RelativePath -eq ".env" -or $RelativePath -like "*/.env") {
        return $true
    }

    if ($RelativePath -eq ".phpunit.result.cache" -or $RelativePath -like "*/.phpunit.result.cache") {
        return $true
    }

    foreach ($pattern in $excludedRelativePatterns) {
        if ($RelativePath -eq $pattern -or $RelativePath.StartsWith("$pattern/")) {
            return $true
        }
    }

    return $false
}

function Test-IsExcluded([System.IO.FileSystemInfo] $Item) {
    $relative = Convert-ToRelativePath $Item.FullName

    if ($Item.PSIsContainer -and $excludedDirectoryNames -contains $Item.Name) {
        return $true
    }

    if (Test-MatchesExcludedPattern $relative) {
        return $true
    }

    if (-not $Item.PSIsContainer -and $excludedExtensions -contains $Item.Extension.ToLowerInvariant()) {
        return $true
    }

    return $false
}

function Test-EntryExists([string[]] $Entries, [string] $RequiredPath) {
    return ($Entries | Where-Object { $_ -eq $RequiredPath -or $_.StartsWith("$RequiredPath/") }).Count -gt 0
}

function Assert-CleanFileSet([System.IO.FileInfo[]] $IncludedFiles) {
    $includedRelative = $IncludedFiles | ForEach-Object { Convert-ToRelativePath $_.FullName }
    $badFiles = $includedRelative | Where-Object {
        $path = $_
        ($path -eq ".env") -or
        ($path -like "*/.env") -or
        (($path -like ".env.*" -or $path -like "*/.env.*") -and -not (Test-IsEnvExample $path)) -or
        ($path -match "(^|/)vendor/") -or
        ($path -match "(^|/)node_modules/") -or
        ($path -match "(^|/)\.git/") -or
        ($path -match "(^|/)public/build/") -or
        ($path -match "(^|/)\.phpunit\.cache/") -or
        ($path -like "*.zip") -or
        ($path -like "*.rar") -or
        ($path -like "*.7z") -or
        ($path -like "*.log") -or
        ($path -like "*database.sqlite") -or
        ($path -like "*testing.sqlite")
    }

    if ($badFiles.Count -gt 0) {
        throw "Unsafe files would be included: $($badFiles -join ', ')"
    }
}

function Assert-ZipContents([string] $ZipPath) {
    Add-Type -AssemblyName System.IO.Compression
    Add-Type -AssemblyName System.IO.Compression.FileSystem

    $zip = [System.IO.Compression.ZipFile]::OpenRead($ZipPath)

    try {
        $rawEntries = @($zip.Entries | Where-Object { -not $_.FullName.EndsWith("/") } | ForEach-Object { $_.FullName })
        $backslashEntries = @($rawEntries | Where-Object { $_ -like "*\*" })

        if ($backslashEntries.Count -gt 0) {
            throw "ZIP contains backslash entries: $($backslashEntries -join ', ')"
        }

        $invalidEntries = @($rawEntries | Where-Object {
            ($_ -match "^[A-Za-z]:") -or
            ($_ -match "^/") -or
            ($_ -match "(^|/)\.\.(/|$)")
        })

        if ($invalidEntries.Count -gt 0) {
            throw "ZIP contains invalid paths: $($invalidEntries -join ', ')"
        }

        $entries = $rawEntries

        $forbidden = $entries | Where-Object {
            ($_ -eq ".env") -or
            ($_ -like "*/.env") -or
            (($_ -like ".env.*" -or $_ -like "*/.env.*") -and $_ -notlike "*.env.example") -or
            ($_ -match "(^|/)vendor/") -or
            ($_ -match "(^|/)node_modules/") -or
            ($_ -match "(^|/)\.git/") -or
            ($_ -match "(^|/)public/build/") -or
            ($_ -match "(^|/)\.phpunit\.cache/") -or
            ($_ -eq ".phpunit.result.cache" -or $_ -like "*/.phpunit.result.cache") -or
            ($_ -like "*database.sqlite") -or
            ($_ -like "*testing.sqlite") -or
            ($_ -like "*.zip") -or
            ($_ -like "*.rar") -or
            ($_ -like "*.7z") -or
            ($_ -like "*.log")
        }

        if ($forbidden.Count -gt 0) {
            throw "ZIP contains forbidden entries: $($forbidden -join ', ')"
        }

        $required = @(
            "apps/web-admin-api/.env.example",
            "apps/web-admin-api/composer.json",
            "apps/web-admin-api/composer.lock",
            "apps/web-admin-api/package.json",
            "apps/web-admin-api/package-lock.json",
            "apps/web-admin-api/app",
            "apps/web-admin-api/database/migrations",
            "apps/web-admin-api/database/factories",
            "apps/web-admin-api/database/seeders",
            "apps/web-admin-api/database/sql/README.md",
            "apps/web-admin-api/database/sql/mms_monitoring_schema.sql",
            "apps/web-admin-api/database/sql/mms_monitoring_development_data.sql",
            "apps/web-admin-api/database/sql/mms_monitoring_development_full.sql",
            "docs",
            "scripts",
            "REFERENSI UI WEB ADMIN MONITORING MARKETING",
            "REFERENSI UI APLIKASI MARKETING"
        )

        $missing = $required | Where-Object { -not (Test-EntryExists $entries $_) }

        if ($missing.Count -gt 0) {
            throw "ZIP is missing required entries: $($missing -join ', ')"
        }

        return @{
            EntryCount = $entries.Count
            BackslashEntries = $backslashEntries.Count
            ForwardSlashEntries = @($entries | Where-Object { $_ -like "*/*" }).Count
        }
    } finally {
        $zip.Dispose()
    }
}

$includedFileList = [System.Collections.Generic.List[System.IO.FileInfo]]::new()
$excludedFileList = [System.Collections.Generic.List[System.IO.FileInfo]]::new()
$excludedDirectoryList = [System.Collections.Generic.List[System.IO.DirectoryInfo]]::new()

function Add-SourceFiles([string] $Directory) {
    foreach ($item in Get-ChildItem -LiteralPath $Directory -Force) {
        if (Test-IsExcluded $item) {
            if ($item.PSIsContainer) {
                $excludedDirectoryList.Add([System.IO.DirectoryInfo] $item)
            } else {
                $excludedFileList.Add([System.IO.FileInfo] $item)
            }

            continue
        }

        if ($item.PSIsContainer) {
            Add-SourceFiles $item.FullName
        } else {
            $includedFileList.Add([System.IO.FileInfo] $item)
        }
    }
}

Add-SourceFiles $root

$files = @($includedFileList)
$excludedFiles = @($excludedFileList)
$sensitiveFound = @(
    $excludedFiles |
        Where-Object {
            $relative = Convert-ToRelativePath $_.FullName
            ($sensitivePatterns | Where-Object { $relative -eq $_ -or $relative.EndsWith("/$_") }).Count -gt 0
        } |
        ForEach-Object { Convert-ToRelativePath $_.FullName }
)

Assert-CleanFileSet $files

$totalBytes = ($files | Measure-Object -Property Length -Sum).Sum
$excludedBytes = ($excludedFiles | Measure-Object -Property Length -Sum).Sum

if ($DryRun) {
    Write-Host "Dry run: no ZIP created."
    Write-Host "Included files: $($files.Count)"
    Write-Host "Excluded files: $($excludedFiles.Count)"
    Write-Host "Excluded directories: $($excludedDirectoryList.Count)"
    Write-Host "Estimated included size MB: $([math]::Round($totalBytes / 1MB, 2))"
    Write-Host "Estimated excluded size MB: $([math]::Round($excludedBytes / 1MB, 2))"
    Write-Host "Output name: $(Split-Path -Leaf $outputPath)"

    if ($sensitiveFound.Count -gt 0) {
        Write-Host "Sensitive files found but excluded:"
        $sensitiveFound | ForEach-Object { Write-Host " - $_" }
    } else {
        Write-Host "Sensitive files found: 0"
    }

    return
}

New-Item -ItemType Directory -Force -Path $outputDirectory | Out-Null

if (Test-Path -LiteralPath $outputPath) {
    Remove-Item -LiteralPath $outputPath -Force
}

try {
    Add-Type -AssemblyName System.IO.Compression
    Add-Type -AssemblyName System.IO.Compression.FileSystem

    $archive = [System.IO.Compression.ZipFile]::Open(
        $outputPath,
        [System.IO.Compression.ZipArchiveMode]::Create
    )

    foreach ($file in $files) {
        $entryName = (Convert-ToRelativePath $file.FullName).Replace("\", "/")

        [System.IO.Compression.ZipFileExtensions]::CreateEntryFromFile(
            $archive,
            $file.FullName,
            $entryName,
            [System.IO.Compression.CompressionLevel]::Optimal
        ) | Out-Null
    }

    $archive.Dispose()
    $zipSummary = Assert-ZipContents $outputPath
    $zipSizeMb = [math]::Round((Get-Item -LiteralPath $outputPath).Length / 1MB, 2)

    Write-Host "Created: $outputPath"
    Write-Host "Included files: $($files.Count)"
    Write-Host "ZIP entries: $($zipSummary.EntryCount)"
    Write-Host "ZIP backslash entries: $($zipSummary.BackslashEntries)"
    Write-Host "ZIP forward slash entries: $($zipSummary.ForwardSlashEntries)"
    Write-Host "ZIP size MB: $zipSizeMb"
} catch {
    if ($archive) {
        $archive.Dispose()
    }

    if (Test-Path -LiteralPath $outputPath) {
        Remove-Item -LiteralPath $outputPath -Force
    }

    throw
}
