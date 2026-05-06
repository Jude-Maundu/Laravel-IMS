@extends('layouts.app')

@section('title', 'Create User')
@section('page-title', 'Create New User')

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
    <span class="itd-bc-cur">Create</span>
  </div>

  {{-- Flash Messages --}}
  @if($errors->any())
  <div class="ev-flash ev-flash-error">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
    </svg>
    <ul class="mt-2 list-disc list-inside">
      @foreach($errors->all() as $error)
      <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
  @endif

  {{-- Form --}}
  <div class="db-card">
    <div class="db-card-header">
      <h3 class="db-card-title">User Information</h3>
    </div>
    <div class="db-card-content">
      <form action="{{ route('users.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          {{-- Name --}}
          <div>
            <label for="name" class="db-form-label">Full Name</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}"
                   class="db-form-input @error('name') border-red-300 @enderror" required>
            @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
          </div>

          {{-- Email --}}
          <div>
            <label for="email" class="db-form-label">Email Address</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}"
                   class="db-form-input @error('email') border-red-300 @enderror" required>
            @error('email')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
          </div>

          {{-- Password --}}
          <div>
            <label for="password" class="db-form-label">Password</label>
            <input type="password" id="password" name="password"
                   class="db-form-input @error('password') border-red-300 @enderror" required>
            @error('password')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
          </div>

          {{-- Password Confirmation --}}
          <div>
            <label for="password_confirmation" class="db-form-label">Confirm Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation"
                   class="db-form-input @error('password_confirmation') border-red-300 @enderror" required>
            @error('password_confirmation')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
          </div>

          {{-- Role --}}
          <div class="md:col-span-2">
            <label for="role" class="db-form-label">Role</label>
            <select id="role" name="role" class="db-form-input @error('role') border-red-300 @enderror" required>
              <option value="">Select a role</option>
              @foreach($roles as $role)
              <option value="{{ $role->name }}" {{ old('role') === $role->name ? 'selected' : '' }}>
                {{ $role->name }}
              </option>
              @endforeach
            </select>
            @error('role')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
          </div>
        </div>

        {{-- Actions --}}
        <div class="mt-6 flex items-center justify-end space-x-3">
          <a href="{{ route('users.index') }}" class="db-btn-outline">Cancel</a>
          <button type="submit" class="db-btn-primary">Create User</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection