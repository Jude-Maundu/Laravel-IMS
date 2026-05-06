@extends('layouts.app')

@section('title', 'Users')
@section('page-title', 'User Management')

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
    <span class="itd-bc-cur">Users</span>
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
      <h1 class="db-title">User Management</h1>
      <p class="db-subtitle">Manage system users and their roles</p>
    </div>
    <div class="db-header-actions">
      <a href="{{ route('users.create') }}" class="db-btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
        </svg>
        Add User
      </a>
    </div>
  </div>

  {{-- Users Table --}}
  <div class="db-card">
    <div class="db-card-content">
      <div class="overflow-x-auto">
        <table class="db-table">
          <thead>
            <tr>
              <th class="db-th">Name</th>
              <th class="db-th">Email</th>
              <th class="db-th">Role</th>
              <th class="db-th">Created</th>
              <th class="db-th">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($users as $user)
            <tr class="db-tr">
              <td class="db-td">
                <div class="flex items-center space-x-3">
                  <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                      <span class="text-sm font-medium text-gray-700">{{ substr($user->name, 0, 1) }}</span>
                    </div>
                  </div>
                  <div>
                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                  </div>
                </div>
              </td>
              <td class="db-td">{{ $user->email }}</td>
              <td class="db-td">
                @if($user->roles->count() > 0)
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    @if($user->hasRole('Admin')) bg-red-100 text-red-800
                    @elseif($user->hasRole('Manager')) bg-blue-100 text-blue-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ $user->roles->first()->name }}
                  </span>
                @else
                  <span class="text-gray-500">No role</span>
                @endif
              </td>
              <td class="db-td">{{ $user->created_at->format('M d, Y') }}</td>
              <td class="db-td">
                <div class="flex items-center space-x-2">
                  <a href="{{ route('users.show', $user) }}" class="text-blue-600 hover:text-blue-900">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                  </a>
                  <a href="{{ route('users.edit', $user) }}" class="text-yellow-600 hover:text-yellow-900">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                  </a>
                  @if($user->id !== auth()->id())
                  <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline"
                        onsubmit="return confirm('Are you sure you want to delete this user?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-900">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                      </svg>
                    </button>
                  </form>
                  @endif
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="5" class="db-td text-center text-gray-500">
                No users found.
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- Pagination --}}
      @if($users->hasPages())
      <div class="db-pagination">
        {{ $users->links() }}
      </div>
      @endif
    </div>
  </div>
</div>
@endsection