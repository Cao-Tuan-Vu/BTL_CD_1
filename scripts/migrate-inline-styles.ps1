Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

$root = (Get-Location).Path
$viewBase = Join-Path $root 'resources\views'

$targets = Get-ChildItem (Join-Path $viewBase 'customer'), (Join-Path $viewBase 'admin') -Recurse -File -Filter '*.blade.php' |
    Where-Object { $_.FullName -notmatch '\\layouts\\' }

$migratedFiles = New-Object System.Collections.Generic.List[string]

foreach ($file in $targets) {
    $content = Get-Content -Path $file.FullName -Raw

    if ($content -notmatch 'style="') {
        continue
    }

    $area = if ($file.FullName -match '\\resources\\views\\customer\\') { 'customer' } else { 'admin' }

    $relative = $file.FullName -replace "^.*\\resources\\views\\$area\\", ''
    $relative = $relative -replace '\\', '/'
    $relative = $relative -replace '\.blade\.php$', ''

    $cssRelative = "$area/pages/$relative.css"
    $cssDiskPath = Join-Path $root ("public\\css\\" + $area + "\\pages\\" + (($relative -replace '/', '\\') + '.css'))
    $cssDir = Split-Path -Parent $cssDiskPath

    if (-not (Test-Path $cssDir)) {
        New-Item -ItemType Directory -Path $cssDir -Force | Out-Null
    }

    $styleToClass = @{}
    $rules = New-Object System.Collections.Generic.List[string]
    $styleIndexRef = [ref]0

    $resolveClass = {
        param([string]$rawStyle)

        $style = ($rawStyle.Trim() -replace '\s+', ' ')
        if (-not $style.EndsWith(';')) {
            $style += ';'
        }

        if (-not $styleToClass.ContainsKey($style)) {
            $styleIndexRef.Value += 1
            $className = 'pv-' + $styleIndexRef.Value
            $styleToClass[$style] = $className
            $rules.Add('.' + $className + ' { ' + $style + ' }')
        }

        return $styleToClass[$style]
    }

    $content = [regex]::Replace(
        $content,
        'class="([^"]*)"\s+style="([^"]*)"',
        {
            param($m)
            $className = & $resolveClass $m.Groups[2].Value
            return 'class="' + $m.Groups[1].Value + ' ' + $className + '"'
        }
    )

    $content = [regex]::Replace(
        $content,
        'style="([^"]*)"\s+class="([^"]*)"',
        {
            param($m)
            $className = & $resolveClass $m.Groups[1].Value
            return 'class="' + $m.Groups[2].Value + ' ' + $className + '"'
        }
    )

    $content = [regex]::Replace(
        $content,
        'style="([^"]*)"',
        {
            param($m)
            $className = & $resolveClass $m.Groups[1].Value
            return 'class="' + $className + '"'
        }
    )

    if ($rules.Count -eq 0) {
        continue
    }

    $assetMarkerSingle = "asset('css/$cssRelative')"

    if ($content -notmatch [regex]::Escape($assetMarkerSingle)) {
        $linkLine = '    <link rel="stylesheet" href="{{ asset(''css/' + $cssRelative + ''') }}">'

        if ($content -match "@push\('styles'\)") {
            $content = [regex]::Replace(
                $content,
                "@push\('styles'\)\r?\n",
                "@push('styles')`r`n$linkLine`r`n",
                1
            )
        } elseif ($content -match "@section\('title',[^\n]*\)\r?\n") {
            $content = [regex]::Replace(
                $content,
                "(@section\('title',[^\n]*\)\r?\n)",
                "`$1`r`n@push('styles')`r`n$linkLine`r`n@endpush`r`n`r`n",
                1
            )
        } elseif ($content -match "@extends\([^\n]+\)\r?\n") {
            $content = [regex]::Replace(
                $content,
                "(@extends\([^\n]+\)\r?\n)",
                "`$1`r`n@push('styles')`r`n$linkLine`r`n@endpush`r`n`r`n",
                1
            )
        } else {
            $prefix = "@push('styles')`r`n$linkLine`r`n@endpush`r`n`r`n"
            $content = $prefix + $content
        }
    }

    $cssContent = "/* Auto-generated from $relative.blade.php */`r`n" + ($rules -join "`r`n") + "`r`n"
    Set-Content -Path $cssDiskPath -Value $cssContent -Encoding UTF8
    Set-Content -Path $file.FullName -Value $content -Encoding UTF8

    $migratedFiles.Add($file.FullName)
}

Write-Output ('migrated_count=' + $migratedFiles.Count)
$migratedFiles | ForEach-Object { Write-Output ('migrated=' + $_) }
