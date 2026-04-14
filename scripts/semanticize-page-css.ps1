Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

$root = (Get-Location).Path

function Get-SemanticSuffix {
    param(
        [string]$decl
    )

    if ($decl -eq "margin: 0; font-family: Georgia, 'Times New Roman', serif;") { return 'title' }
    if ($decl -eq "margin: .35rem 0 0; color: var(--muted);") { return 'subtitle' }
    if ($decl -eq "margin-bottom: 1rem;") { return 'section' }
    if ($decl -eq "margin: .3rem 0;") { return 'meta-row' }
    if ($decl -eq "margin: 0 0 .8rem; font-family: Georgia, 'Times New Roman', serif;") { return 'card-title' }
    if ($decl -eq "margin-top: .9rem;") { return 'actions-top' }
    if ($decl -eq "margin: 0; color: var(--muted);") { return 'muted-text' }
    if ($decl -match '^max-width: [0-9]+px; margin: 0 auto;$') { return 'auth-card' }
    if ($decl -eq 'justify-content: end;') { return 'actions-end' }
    if ($decl -eq 'margin-top: .8rem;') { return 'pagination-top' }
    if ($decl -eq 'margin-bottom: .8rem;') { return 'title-gap' }
    if ($decl -eq 'font-size: 1rem; margin-bottom: .7rem;') { return 'title-sm' }
    if ($decl -match 'object-fit: cover' -and $decl -match 'height: 180px') { return 'product-image' }
    if ($decl -match 'object-fit: cover' -and $decl -match 'height: 74px') { return 'gallery-image-sm' }
    if ($decl -match 'object-fit: cover' -and $decl -match 'height: 80px') { return 'gallery-image-md' }
    if ($decl -match 'object-fit: cover' -and $decl -match 'height: 90px') { return 'product-image-sm' }
    if ($decl -match 'place-items: center' -and $decl -match 'dashed') { return 'placeholder' }
    if ($decl -match '^width: (70|82|88)px;$') { return 'qty-input' }
    if ($decl -match '^display: grid; grid-template-columns: repeat\(auto-fit') { return 'gallery-grid' }
    if ($decl -eq 'white-space: pre-line;') { return 'multiline' }

    return 'block'
}

function Process-Area {
    param(
        [string]$area,
        [string]$prefix,
        [string]$cssRoot,
        [hashtable]$sharedMap
    )

    $cssRootPath = Join-Path $root $cssRoot
    $migrated = New-Object System.Collections.Generic.List[string]

    Get-ChildItem $cssRootPath -Recurse -File -Filter '*.css' | ForEach-Object {
        $cssPath = $_.FullName
        $cssRaw = Get-Content $cssPath -Raw

        if ($cssRaw -notmatch '\.pv-') {
            return
        }

        $relCss = ($cssPath.Substring($cssRootPath.Length + 1) -replace '[\\/]+', '/')
        $slug = ($relCss -replace '\.css$', '' -replace '/', '-')
        $viewRel = ($relCss -replace '\.css$', '.blade.php')
        $viewPath = Join-Path $root ("resources\\views\\$area\\" + ($viewRel -replace '/', '\\'))

        $cssLines = Get-Content $cssPath
        $classMap = @{}
        $rulesOut = New-Object System.Collections.Generic.List[string]
        $suffixCounter = @{}

        foreach ($line in $cssLines) {
            if ($line -match '^\.(pv-[A-Za-z0-9_-]+)\s*\{\s*(.+?)\s*\}\s*$') {
                $oldClass = $matches[1]
                $decl = $matches[2].Trim()

                if ($sharedMap.ContainsKey($decl)) {
                    $newClass = $sharedMap[$decl]
                    $classMap[$oldClass] = $newClass
                    continue
                }

                $suffix = Get-SemanticSuffix -decl $decl
                if (-not $suffixCounter.ContainsKey($suffix)) {
                    $suffixCounter[$suffix] = 0
                }
                $suffixCounter[$suffix] += 1

                $index = $suffixCounter[$suffix]
                $suffixFinal = if ($index -gt 1) { "$suffix-$index" } else { $suffix }
                $newClass = "$prefix-$slug-$suffixFinal"

                $classMap[$oldClass] = $newClass
                $rulesOut.Add('.' + $newClass + ' { ' + $decl + ' }')
            }
        }

        if ($classMap.Count -eq 0) {
            return
        }

        if (-not (Test-Path $viewPath)) {
            throw "View not found for CSS: $cssPath -> $viewPath"
        }

        $viewRaw = Get-Content $viewPath -Raw
        foreach ($entry in $classMap.GetEnumerator()) {
            $pattern = '(?<![A-Za-z0-9_-])' + [regex]::Escape($entry.Key) + '(?![A-Za-z0-9_-])'
            $viewRaw = [regex]::Replace($viewRaw, $pattern, $entry.Value)
        }
        Set-Content -Path $viewPath -Value $viewRaw -Encoding UTF8

        $out = New-Object System.Collections.Generic.List[string]
        $out.Add('/* Semantic classes for ' + ($viewRel -replace '\\.blade\\.php$', '') + ' */')
        foreach ($rule in $rulesOut) {
            $out.Add($rule)
        }
        Set-Content -Path $cssPath -Value ($out -join "`r`n" + "`r`n") -Encoding UTF8

        $migrated.Add($viewPath)
    }

    return $migrated
}

$customerSharedMap = [ordered]@{
    "margin: 0; font-family: Georgia, 'Times New Roman', serif;" = 'c-shared-title'
    "margin: .35rem 0 0; color: var(--muted);" = 'c-shared-subtitle'
    'margin-bottom: 1rem;' = 'c-shared-section-gap'
    'margin: .3rem 0;' = 'c-shared-meta-row'
    "margin: 0 0 .8rem; font-family: Georgia, 'Times New Roman', serif;" = 'c-shared-card-title'
    'margin-top: .9rem;' = 'c-shared-actions-top'
    'margin: 0; color: var(--muted);' = 'c-shared-muted'
    'max-width: 620px; margin: 0 auto;' = 'c-shared-auth-card'
}

$adminSharedMap = [ordered]@{
    'margin-bottom: 1rem;' = 'a-shared-section-gap'
    'margin-top: .8rem;' = 'a-shared-pagination-top'
    'justify-content: end;' = 'a-shared-actions-end'
    'margin-bottom: .8rem;' = 'a-shared-title-gap'
    'font-size: 1rem; margin-bottom: .7rem;' = 'a-shared-title-sm'
}

$customerSharedCssPath = Join-Path $root 'public\\css\\customer\\shared.css'
$adminSharedCssPath = Join-Path $root 'public\\css\\admin\\shared.css'

$customerSharedOut = New-Object System.Collections.Generic.List[string]
$customerSharedOut.Add('/* Customer shared semantic classes */')
foreach ($entry in $customerSharedMap.GetEnumerator()) {
    $customerSharedOut.Add('.' + $entry.Value + ' { ' + $entry.Key + ' }')
}
Set-Content -Path $customerSharedCssPath -Value ($customerSharedOut -join "`r`n" + "`r`n") -Encoding UTF8

$adminSharedOut = New-Object System.Collections.Generic.List[string]
$adminSharedOut.Add('/* Admin shared semantic classes */')
foreach ($entry in $adminSharedMap.GetEnumerator()) {
    $adminSharedOut.Add('.' + $entry.Value + ' { ' + $entry.Key + ' }')
}
Set-Content -Path $adminSharedCssPath -Value ($adminSharedOut -join "`r`n" + "`r`n") -Encoding UTF8

$customerChanged = Process-Area -area 'customer' -prefix 'c' -cssRoot 'public/css/customer/pages' -sharedMap $customerSharedMap
$adminChanged = Process-Area -area 'admin' -prefix 'a' -cssRoot 'public/css/admin/pages' -sharedMap $adminSharedMap

Write-Output ('customer_changed=' + $customerChanged.Count)
$customerChanged | Sort-Object -Unique | ForEach-Object { Write-Output ('customer_view=' + $_) }
Write-Output ('admin_changed=' + $adminChanged.Count)
$adminChanged | Sort-Object -Unique | ForEach-Object { Write-Output ('admin_view=' + $_) }
