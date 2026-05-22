# Genera zafirosCalc-deploy.zip para subir a hosting compartido (tar).
# Rutas: usa la ruta real del repo (Resolve-Path). Salida: raíz del proyecto.
# Ejecutar desde cualquier sitio:  .\scripts\build-deploy-zip.ps1
# Si Windows dice que el .zip está en uso, cerrá el Explorador o renombrá el zip viejo.

$ErrorActionPreference = 'Stop'
$ProjectRoot = Resolve-Path (Join-Path $PSScriptRoot '..')
$ZipName = 'zafirosCalc-deploy.zip'
$ZipPath = Join-Path $ProjectRoot.Path $ZipName

Push-Location $ProjectRoot.Path
try {
    if (Test-Path $ZipPath) {
        Remove-Item $ZipPath -Force
    }

    Write-Host 'Creando ZIP (ruta salida):' -ForegroundColor Cyan
    Write-Host "  $ZipPath"
    Write-Host ''

    $tarArgs = @(
        '-acf', $ZipPath,
        '--exclude=node_modules',
        '--exclude=.git',
        '--exclude=tests',
        '--exclude=.cursor',
        '--exclude=.env',
        '--exclude=.phpunit.result.cache',
        '--exclude=.phpunit.cache',
        '--exclude=storage/logs/*.log',
        '--exclude=storage/framework/cache/data',
        '--exclude=storage/framework/sessions',
        '--exclude=storage/framework/views',
        "--exclude=$ZipName",
        '--exclude=zafirosCalc-deploy-FULL.zip',
        '--exclude=zafirosCalc-deploy-new.zip',
        '--exclude=zafirosCalc-deploy-*.zip',
        '.'
    )

    & tar @tarArgs
    if ($LASTEXITCODE -ne 0) {
        throw "tar falló con código $LASTEXITCODE"
    }

    if (-not (Test-Path $ZipPath)) {
        throw "No se generó el archivo: $ZipPath"
    }

    Write-Host ''
    Write-Host 'OK - ZIP listo:' -ForegroundColor Green
    Write-Host "  $ZipPath"
    $size = (Get-Item $ZipPath).Length / 1MB
    Write-Host ("  Tamaño aproximado: {0:N1} MB" -f $size)
    Write-Host ''
    Write-Host 'Incluye NOTAS_SUBIDA_DEMO.txt y deploy-env-template.env (APP_URL demo).'
    Write-Host 'En el servidor: conserva tu .env; si es nuevo, copia deploy-env-template.env a .env.'
}
finally {
    Pop-Location
}
