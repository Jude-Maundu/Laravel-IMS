@extends('layouts.app')

@section('title', 'Settings')
@section('page-title', 'System Settings')

@section('content')
<div class="db-container">

  {{-- Breadcrumb --}}
  <div class="itd-breadcrumb">
    <a href="{{ route('dashboard.index') }}" class="itd-bc-link">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v0M9 9h6"/>
      </svg>
      Dashboard
    </a>
    <span class="itd-bc-sep">/</span>
    <span class="itd-bc-cur">Settings</span>
  </div>

  {{-- Flash Messages --}}
  @if(session('success'))
  <div class="ev-flash ev-flash-success">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
    </svg>
    {{ session('success') }}
  </div>
  @endif

  @if(session('error'))
  <div class="ev-flash ev-flash-error">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
    </svg>
    {{ session('error') }}
  </div>
  @endif

  {{-- Page Header --}}
  <div class="db-header">
    <div class="db-header-content">
      <h1 class="db-title">System Settings</h1>
      <p class="db-subtitle">Configure application settings and system preferences</p>
    </div>
    <div class="db-header-actions">
      <form action="{{ route('settings.clear-cache') }}" method="POST" class="inline">
        @csrf
        <button type="submit" class="db-btn-outline">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
          </svg>
          Clear Cache
        </button>
      </form>
    </div>
  </div>

  {{-- Settings Form --}}
  <form action="{{ route('settings.update') }}" method="POST">
    @csrf

    {{-- Application Settings --}}
    <div class="db-card">
      <div class="db-card-header">
        <h3 class="db-card-title">Application Settings</h3>
      </div>
      <div class="db-card-content">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          {{-- App Name --}}
          <div>
            <label for="app_name" class="db-form-label">Application Name</label>
            <input type="text" id="app_name" name="app_name" value="{{ old('app_name', config('app.name')) }}"
                   class="db-form-input @error('app_name') border-red-300 @enderror" required>
            @error('app_name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
          </div>

          {{-- Debug Mode --}}
          <div>
            <label class="db-form-label">Debug Mode</label>
            <div class="mt-2">
              <label class="inline-flex items-center">
                <input type="checkbox" name="app_debug" value="1"
                       {{ old('app_debug', config('app.debug')) ? 'checked' : '' }}
                       class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                <span class="ml-2 text-sm text-gray-700">Enable debug mode (shows detailed error messages)</span>
              </label>
            </div>
            @error('app_debug')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
          </div>
        </div>
      </div>
    </div>

    {{-- System Settings --}}
    <div class="db-card mt-6">
      <div class="db-card-header">
        <h3 class="db-card-title">System Configuration</h3>
      </div>
      <div class="db-card-content">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          {{-- Log Level --}}
          <div>
            <label for="log_level" class="db-form-label">Log Level</label>
            <select id="log_level" name="log_level" class="db-form-input @error('log_level') border-red-300 @enderror" required>
              @php
              $logLevels = ['emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug'];
              @endphp
              @foreach($logLevels as $level)
              <option value="{{ $level }}" {{ old('log_level', config('logging.channels.stack.level', 'error')) === $level ? 'selected' : '' }}>
                {{ ucfirst($level) }}
              </option>
              @endforeach
            </select>
            @error('log_level')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
          </div>

          {{-- Cache Driver --}}
          <div>
            <label for="cache_driver" class="db-form-label">Cache Driver</label>
            <select id="cache_driver" name="cache_driver" class="db-form-input @error('cache_driver') border-red-300 @enderror" required>
              @php
              $cacheDrivers = ['file', 'database', 'redis'];
              @endphp
              @foreach($cacheDrivers as $driver)
              <option value="{{ $driver }}" {{ old('cache_driver', config('cache.default')) === $driver ? 'selected' : '' }}>
                {{ ucfirst($driver) }}
              </option>
              @endforeach
            </select>
            @error('cache_driver')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
          </div>

          {{-- Session Driver --}}
          <div>
            <label for="session_driver" class="db-form-label">Session Driver</label>
            <select id="session_driver" name="session_driver" class="db-form-input @error('session_driver') border-red-300 @enderror" required>
              @php
              $sessionDrivers = ['file', 'cookie', 'database', 'redis'];
              @endphp
              @foreach($sessionDrivers as $driver)
              <option value="{{ $driver }}" {{ old('session_driver', config('session.driver')) === $driver ? 'selected' : '' }}>
                {{ ucfirst($driver) }}
              </option>
              @endforeach
            </select>
            @error('session_driver')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
          </div>

          {{-- Queue Connection --}}
          <div>
            <label for="queue_connection" class="db-form-label">Queue Connection</label>
            <select id="queue_connection" name="queue_connection" class="db-form-input @error('queue_connection') border-red-300 @enderror" required>
              @php
              $queueConnections = ['sync', 'database', 'redis'];
              @endphp
              @foreach($queueConnections as $connection)
              <option value="{{ $connection }}" {{ old('queue_connection', config('queue.default')) === $connection ? 'selected' : '' }}>
                {{ ucfirst($connection) }}
              </option>
              @endforeach
            </select>
            @error('queue_connection')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
          </div>
        </div>
      </div>
    </div>

    {{-- Actions --}}
    <div class="mt-6 flex items-center justify-end space-x-3">
      <button type="submit" class="db-btn-primary">Save Settings</button>
    </div>
  </form>

  {{-- Danger Zone --}}
  <div class="db-card mt-6 border border-red-200 bg-red-50">
    <div class="db-card-header">
      <h3 class="db-card-title">Danger Zone</h3>
    </div>
    <div class="db-card-content">
      <p class="text-sm text-gray-700">This action will delete all application records, including inventory, events, repairs, and logs. User accounts will remain.</p>
      <form action="{{ route('settings.clear-data') }}" method="POST" onsubmit="return confirm('Are you sure? This will wipe application data but keep user accounts.');">
        @csrf
        <label class="inline-flex items-center mt-4">
          <input type="checkbox" name="confirm_wipe" value="1" class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50" required>
          <span class="ml-2 text-sm text-gray-700">I understand this will erase app data.</span>
        </label>
        <div class="mt-4">
          <button type="submit" class="db-btn-danger">
            <svg class="w-4 h-4 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 2C8.134 2 5 5.134 5 9c0 4.5 7 13 7 13s7-8.5 7-13c0-3.866-3.134-7-7-7z"/>
              <path d="M9 10h6M9 14h6M10.5 18h3"/>
            </svg>
            Wipe Application Data
          </button>
        </div>
      </form>
    </div>
  </div>

  {{-- System Information --}}
  <div class="db-card mt-6">
    <div class="db-card-header">
      <h3 class="db-card-title">System Information</h3>
    </div>
    <div class="db-card-content">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div>
          <dt class="text-sm font-medium text-gray-500">Laravel Version</dt>
          <dd class="mt-1 text-sm text-gray-900">{{ app()->version() }}</dd>
        </div>

        <div>
          <dt class="text-sm font-medium text-gray-500">PHP Version</dt>
          <dd class="mt-1 text-sm text-gray-900">{{ PHP_VERSION }}</dd>
        </div>

        <div>
          <dt class="text-sm font-medium text-gray-500">Environment</dt>
          <dd class="mt-1 text-sm text-gray-900">{{ config('app.env') }}</dd>
        </div>

        <div>
          <dt class="text-sm font-medium text-gray-500">Database</dt>
          <dd class="mt-1 text-sm text-gray-900">{{ config('database.default') }}</dd>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection