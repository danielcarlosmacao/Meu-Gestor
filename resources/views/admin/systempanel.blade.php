@extends('layouts.header')
@section('title', 'Painel do sistema')
@section('content')

<style>
  .info-card {
    border-radius: 16px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    transition: transform 0.2s ease;
    border: none;
  }

  .info-card:hover {
    transform: translateY(-5px);
  }

  .info-icon {
    font-size: 2.2rem;
    margin-bottom: 10px;
  }

  .progress {
    height: 8px;
    border-radius: 5px;
  }

  .card-title {
    font-size: 1.1rem;
    font-weight: 600;
  }

  .card-text {
    font-size: 0.95rem;
  }

  .bg-sistema { background-color: #f3f4f6; }
  .bg-memoria { background-color: #e0f7fa; }
  .bg-disco { background-color: #fff3e0; }
  .bg-software { background-color: #ede7f6; }
  .bg-php { background-color: #e8f5e9; }
  .bg-cliente { background-color: #fce4ec; }
</style>

<div class="container py-4">
  <div class="row g-4">

    {{-- SISTEMA --}}
    <div class="col-md-6 col-lg-3">
      <div class="card info-card bg-sistema p-3">
        <div class="card-body text-center">
          <div class="info-icon">🖥️</div>
          <h5 class="card-title">Sistema</h5>
          <p class="card-text">{{ php_uname('s') }} {{ php_uname('r') }}</p>
          <h5 class="card-title">Versao</h5>
          <p class="card-text">{{ trim(file_get_contents(base_path('VERSION'))) }}</p>
        </div>
      </div>
    </div>

    {{-- MEMÓRIA do sistema--}}
{{-- MEMÓRIA RAM REAL DO SISTEMA --}}
@php
    function formatMemory($value) {
        return $value >= 1024
            ? number_format($value / 1024, 2) . ' GB'
            : number_format($value, 2) . ' MB';
    }

    $memUsed = 0;
    $memTotal = 0;
    $memPercent = 0;

    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        // SISTEMA WINDOWS
        @exec("wmic OS get TotalVisibleMemorySize,FreePhysicalMemory /Value", $output);

        $memInfo = [];
        foreach ($output as $line) {
            if (strpos($line, "=") !== false) {
                list($key, $value) = explode("=", $line);
                $memInfo[trim($key)] = (int) trim($value);
            }
        }

        if (isset($memInfo['TotalVisibleMemorySize'], $memInfo['FreePhysicalMemory'])) {
            $memTotal = round($memInfo['TotalVisibleMemorySize'] / 1024, 2); // em MB
            $memAvailable = round($memInfo['FreePhysicalMemory'] / 1024, 2);
            $memUsed = $memTotal - $memAvailable;
            $memPercent = round(($memUsed / $memTotal) * 100);
        }

    } else {
        // SISTEMA LINUX
        if (is_readable("/proc/meminfo")) {
            $meminfo = file_get_contents("/proc/meminfo");

            preg_match("/MemTotal:\s+(\d+)/", $meminfo, $total);
            preg_match("/MemAvailable:\s+(\d+)/", $meminfo, $available);

            if (isset($total[1], $available[1])) {
                $memTotal = round($total[1] / 1024, 2); // em MB
                $memAvailable = round($available[1] / 1024, 2);
                $memUsed = $memTotal - $memAvailable;
                $memPercent = round(($memUsed / $memTotal) * 100);
            }
        }
    }
@endphp

@if ($memTotal > 0)
  <div class="col-md-6 col-lg-3">
    <div class="card info-card bg-memoria p-3">
      <div class="card-body">
        <div class="info-icon">🧠</div>
        <h5 class="card-title">Memória RAM do Sistema</h5>
        <p class="card-text">Uso: {{ formatMemory($memUsed) }}</p>
        <p class="card-text">Total: {{ formatMemory($memTotal) }}</p>

        <div class="progress">
          <div class="progress-bar bg-info" role="progressbar"
               style="width: {{ $memPercent }}%"
               aria-valuenow="{{ $memPercent }}" aria-valuemin="0" aria-valuemax="100">
          </div>
        </div>
      </div>
    </div>
  </div>
@endif


    {{-- MEMÓRIA do PHP--}}
    @php
      $memUsed = memory_get_usage(true);
      $memLimit = ini_get('memory_limit') === '-1' ? 0 : (int) filter_var(ini_get('memory_limit'), FILTER_SANITIZE_NUMBER_INT) * 1024 * 1024;
      $memPercent = $memLimit ? round(($memUsed / $memLimit) * 100) : 0;
    @endphp
    <div class="col-md-6 col-lg-3">
      <div class="card info-card bg-memoria p-3">
        <div class="card-body">
          <div class="info-icon">🧠</div>
          <h5 class="card-title">Memória RAM do PHP</h5>
          <p class="card-text">Uso: {{ round($memUsed / 1024 / 1024, 2) }} MB</p>
          <p class="card-text">Limite: {{ ini_get('memory_limit') }}</p>
          @if ($memLimit)
            <div class="progress">
              <div class="progress-bar bg-info" role="progressbar" style="width: {{ $memPercent }}%" aria-valuenow="{{ $memPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
          @endif
        </div>
      </div>
    </div>

    {{-- DISCO --}}
    @php
      $diskFree = disk_free_space('/');
      $diskTotal = disk_total_space('/');
      $diskPercent = round((($diskTotal - $diskFree) / $diskTotal) * 100);
    @endphp
    <div class="col-md-6 col-lg-3">
      <div class="card info-card bg-disco p-3">
        <div class="card-body">
          <div class="info-icon">💾</div>
          <h5 class="card-title">Disco</h5>
          <p class="card-text">Livre: {{ round($diskFree / 1024 / 1024 / 1024, 2) }} GB</p>
          <p class="card-text">Total: {{ round($diskTotal / 1024 / 1024 / 1024, 2) }} GB</p>
          <div class="progress">
            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $diskPercent }}%" aria-valuenow="{{ $diskPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
        </div>
      </div>
    </div>

    {{-- PHP --}}
    <div class="col-md-6 col-lg-3">
      <div class="card info-card bg-php p-3">
        <div class="card-body text-center">
          <div class="info-icon">⚙️</div>
          <h5 class="card-title">PHP</h5>
          <p class="card-text">Versão: {{ phpversion() }}</p>
          <p class="card-text">Execução: {{ round(microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"], 4) }}s</p>
        </div>
      </div>
    </div>

    {{-- SOFTWARE --}}
    <div class="col-md-6 col-lg-3">
      <div class="card info-card bg-software p-3">
        <div class="card-body text-center">
          <div class="info-icon">📦</div>
          <h5 class="card-title">Servidor</h5>
          <p class="card-text">{{ $_SERVER['SERVER_SOFTWARE'] ?? 'Desconhecido' }}</p>
          <p class="card-text">Porta: {{ $_SERVER['SERVER_PORT'] ?? 'N/A' }}</p>
        </div>
      </div>
    </div>

    {{-- CLIENTE --}}
    <div class="col-md-6 col-lg-3">
      <div class="card info-card bg-cliente p-3">
        <div class="card-body text-center">
          <div class="info-icon">🧑</div>
          <h5 class="card-title">Cliente</h5>
          <p class="card-text">IP: {{ $_SERVER['REMOTE_ADDR'] ?? 'N/A' }}</p>
          <p class="card-text">Navegador: {{ Str::limit($_SERVER['HTTP_USER_AGENT'] ?? 'N/A', 50) }}</p>
        </div>
      </div>
    </div>
    {{-- CPU --}}
@php
    $cpuModel = 'Desconhecido';
    $cpuCores = 0;
    $cpuLoad = [];

    if (PHP_OS_FAMILY === 'Linux') {
        $cpuInfo = @file_get_contents('/proc/cpuinfo');
        if ($cpuInfo) {
            preg_match('/model name\s+:\s+(.+)/', $cpuInfo, $match);
            if (!empty($match[1])) {
                $cpuModel = trim($match[1]);
            }
            preg_match_all('/^processor/m', $cpuInfo, $cores);
            $cpuCores = count($cores[0]);
        }
        $cpuLoad = sys_getloadavg();
    }
@endphp
<div class="col-md-6 col-lg-3">
    <div class="card info-card p-3" style="background-color: #e8eaf6;">
        <div class="card-body">
            <div class="info-icon">🖲️</div>
            <h5 class="card-title">CPU</h5>
            <p class="card-text">{{ $cpuModel }}</p>
            <p class="card-text">Cores: {{ $cpuCores ?: 'N/A' }}</p>
            @if(!empty($cpuLoad))
                <p class="card-text">Load: {{ implode(', ', array_map(fn($l) => round($l, 2), $cpuLoad)) }}</p>
            @endif
        </div>
    </div>
</div>

{{-- Banco de Dados --}}
@php
    try {
        $dbVersion = DB::selectOne('select version() as v')->v ?? 'Desconhecida';
        $dbStatus = 'Conectado';
    } catch (Exception $e) {
        $dbVersion = 'Erro';
        $dbStatus = 'Falha na conexão';
    }
@endphp
<div class="col-md-6 col-lg-3">
    <div class="card info-card p-3" style="background-color: #fff8e1;">
        <div class="card-body">
            <div class="info-icon">🗄️</div>
            <h5 class="card-title">Banco de Dados</h5>
            <p class="card-text">Status: {{ $dbStatus }}</p>
            <p class="card-text">Versão: {{ $dbVersion }}</p>
        </div>
    </div>
</div>

{{-- Tempo do Servidor --}}
<div class="col-md-6 col-lg-3">
    <div class="card info-card p-3" style="background-color: #f1f8e9;">
        <div class="card-body">
            <div class="info-icon">⏰</div>
            <h5 class="card-title">Servidor</h5>
            <p class="card-text">Data/Hora: {{ now()->format('d/m/Y H:i:s') }}</p>
            @if(PHP_OS_FAMILY === 'Linux' && is_readable('/proc/uptime'))
                @php
                    $uptimeData = explode(' ', file_get_contents('/proc/uptime'));
                    $uptimeSeconds = (int) $uptimeData[0];
                    $uptimeDays = floor($uptimeSeconds / 86400);
                    $uptimeHours = floor(($uptimeSeconds % 86400) / 3600);
                    $uptimeMinutes = floor(($uptimeSeconds % 3600) / 60);
                @endphp
                <p class="card-text">Uptime: {{ $uptimeDays }}d {{ $uptimeHours }}h {{ $uptimeMinutes }}m</p>
            @endif
        </div>
    </div>
</div>


  </div>
</div>


@endsection