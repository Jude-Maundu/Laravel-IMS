@extends('layouts.app')

@section('title', 'User Details')
@section('page-title', 'User Details')

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
    <a href="{{ route('users.index') }}" class="itd-bc-link">Users</a>
    <span class="itd-bc-sep">/</span>
    <span class="itd-bc-cur">{{ $user->name }}</span>
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

  {{-- User Details --}}
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Profile Card --}}
    <div class="lg:col-span-1">
      <div class="db-card">
        <div class="db-card-content">
          <div class="text-center">
            <div class="mx-auto w-20 h-20 bg-gray-300 rounded-full flex items-center justify-center mb-4">
              <span class="text-2xl font-medium text-gray-700">{{ substr($user->name, 0, 1) }}</span>
            </div>
            <h3 class="text-lg font-medium text-gray-900">{{ $user->name }}</h3>
            <p class="text-sm text-gray-500">{{ $user->email }}</p>

            @if($user->roles->count() > 0)
            <div class="mt-4">
              <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                @if($user->hasRole('Admin')) bg-red-100 text-red-800
                @elseif($user->hasRole('Manager')) bg-blue-100 text-blue-800
                @else bg-gray-100 text-gray-800 @endif">
                {{ $user->roles->first()->name }}
              </span>
            </div>
            @endif
          </div>

          <div class="mt-6 space-y-3">
            <a href="{{ route('users.edit', $user) }}" class="w-full db-btn-primary flex justify-center">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
              </svg>
              Edit User
            </a>

            @if($user->id !== auth()->id())
            <form action="{{ route('users.destroy', $user) }}" method="POST"
                  onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
              @csrf
              @method('DELETE')
              <button type="submit" class="w-full db-btn-danger flex justify-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Delete User
              </button>
            </form>
            @endif
          </div>
        </div>
      </div>
    </div>

    {{-- Details --}}
    <div class="lg:col-span-2">
      <div class="db-card">
        <div class="db-card-header">
          <h3 class="db-card-title">Account Information</h3>
        </div>
        <div class="db-card-content">
          <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <dt class="text-sm font-medium text-gray-500">Full Name</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ $user->name }}</dd>
            </div>

            <div>
              <dt class="text-sm font-medium text-gray-500">Email Address</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ $user->email }}</dd>
            </div>

            <div>
              <dt class="text-sm font-medium text-gray-500">Role</dt>
              <dd class="mt-1 text-sm text-gray-900">
                @if($user->roles->count() > 0)
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                  @if($user->hasRole('Admin')) bg-red-100 text-red-800
                  @elseif($user->hasRole('Manager')) bg-blue-100 text-blue-800
                  @else bg-gray-100 text-gray-800 @endif">
                  {{ $user->roles->first()->name }}
                </span>
                @else
                <span class="text-gray-500">No role assigned</span>
                @endif
              </dd>
            </div>

            <div>
              <dt class="text-sm font-medium text-gray-500">Account Created</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('M d, Y \a\t g:i A') }}</dd>
            </div>

            <div>
              <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ $user->updated_at->format('M d, Y \a\t g:i A') }}</dd>
            </div>

            <div>
              <dt class="text-sm font-medium text-gray-500">Email Verified</dt>
              <dd class="mt-1 text-sm text-gray-900">
                @if($user->email_verified_at)
                <span class="text-green-600">✓ Verified</span>
                @else
                <span class="text-red-600">✗ Not verified</span>
                @endif
              </dd>
            </div>
          </dl>
        </div>
      </div>

      {{-- Permissions --}}
      @if($user->roles->count() > 0)
      <div class="db-card mt-6">
        <div class="db-card-header">
          <h3 class="db-card-title">Permissions</h3>
        </div>
        <div class="db-card-content">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($user->getAllPermissions() as $permission)
            <div class="flex items-center space-x-2">
              <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
              <span class="text-sm text-gray-700">{{ ucwords(str_replace('_', ' ', $permission->name)) }}</span>
            </div>
            @endforeach
          </div>
        </div>
      </div>
      @endif
    </div>
  </div>
</div>
@endsection