# =============================================================================
# Export DB local → archivo listo para importar en producción
# Uso: desde PowerShell en la raíz del proyecto:
#   powershell -ExecutionPolicy Bypass -File scripts\export_db.ps1
# =============================================================================

$DB_NAME  = "cms_db2"
$DB_USER  = "root"
$DB_PASS  = ""           # Cambiar si Laragon tiene contraseña en root
$DATE     = Get-Date -Format "yyyyMMdd"
$OUT_FILE = "$PSScriptRoot\..\database\dumps\cms_db2_production_$DATE.sql"

# Buscar mysqldump en Laragon o en PATH
$MYSQLDUMP = $null
$candidates = @(
    "C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysqldump.exe",
    "C:\laragon\bin\mysql\mysql-8.0.28-winx64\bin\mysqldump.exe",
    "C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysqldump.exe",
    "C:\laragon\bin\mysql\mysql-5.7.33-winx64\bin\mysqldump.exe"
)

foreach ($c in $candidates) {
    if (Test-Path $c) { $MYSQLDUMP = $c; break }
}

if (-not $MYSQLDUMP) {
    $cmd = Get-Command mysqldump -ErrorAction SilentlyContinue
    if ($cmd) { $MYSQLDUMP = $cmd.Source }
}

if (-not $MYSQLDUMP) {
    # Buscar automáticamente en C:\laragon\bin\mysql\
    $found = Get-ChildItem "C:\laragon\bin\mysql" -Recurse -Filter "mysqldump.exe" -ErrorAction SilentlyContinue | Select-Object -First 1
    if ($found) { $MYSQLDUMP = $found.FullName }
}

if (-not $MYSQLDUMP) {
    Write-Error "mysqldump no encontrado. Verifica que Laragon esté instalado."
    exit 1
}

Write-Host "Usando: $MYSQLDUMP"
Write-Host "Exportando base de datos '$DB_NAME'..."

# Crear directorio si no existe
New-Item -ItemType Directory -Force -Path (Split-Path $OUT_FILE) | Out-Null

# Ejecutar mysqldump
if ($DB_PASS -eq "") {
    & $MYSQLDUMP --user=$DB_USER --no-tablespaces --single-transaction --routines --triggers $DB_NAME | Out-File -Encoding utf8 $OUT_FILE
} else {
    & $MYSQLDUMP --user=$DB_USER --password=$DB_PASS --no-tablespaces --single-transaction --routines --triggers $DB_NAME | Out-File -Encoding utf8 $OUT_FILE
}

if ($LASTEXITCODE -ne 0) {
    Write-Error "Error al exportar la DB. Verifica que Laragon/MySQL esté corriendo."
    exit 1
}

# Fix collation: reemplazar utf8mb4_0900_ai_ci → utf8mb4_unicode_ci
Write-Host "Aplicando fix de collation para compatibilidad con servidor de produccion..."
$content = Get-Content $OUT_FILE -Raw -Encoding utf8
$content = $content -replace 'utf8mb4_0900_ai_ci', 'utf8mb4_unicode_ci'
$content = $content -replace 'utf8mb4_0900_as_cs', 'utf8mb4_unicode_ci'
[System.IO.File]::WriteAllText($OUT_FILE, $content, [System.Text.Encoding]::UTF8)

$size = [math]::Round((Get-Item $OUT_FILE).Length / 1KB, 1)
Write-Host ""
Write-Host "Listo: $OUT_FILE ($size KB)"
Write-Host ""
Write-Host "Pasos para importar en produccion:"
Write-Host "  1. Sube el archivo a Plesk"
Write-Host "  2. Plesk → Databases → Import Dump"
Write-Host "  3. (o) SSH: mysql -u DB_USER -p DB_NAME < $(Split-Path $OUT_FILE -Leaf)"
