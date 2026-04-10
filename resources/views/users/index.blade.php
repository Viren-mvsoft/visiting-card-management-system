@extends('layouts.app')
@section('title', 'Users')
@section('subtitle', 'Manage staff accounts')

@section('header-actions')
<a href="{{ route('users.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-primary-500 to-primary-600 text-white text-sm font-semibold hover:from-primary-400 hover:to-primary-500 transition-all shadow-lg shadow-primary-500/20">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
    Add User
</a>
@endsection

@section('content')
<div class="space-y-6 animate-fade-in">
    <div class="glass rounded-2xl overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="border-b border-surface-700/50">
                    <th class="text-left px-6 py-4 text-xs font-semibold uppercase tracking-wider text-surface-400">Name</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold uppercase tracking-wider text-surface-400">Email</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold uppercase tracking-wider text-surface-400">Role</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold uppercase tracking-wider text-surface-400 hidden sm:table-cell">Joined</th>
                    <th class="text-right px-6 py-4 text-xs font-semibold uppercase tracking-wider text-surface-400">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-800/50">
                @foreach($users as $user)
                    <tr class="hover:bg-surface-800/30 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-primary-500/20 to-accent-500/20 flex items-center justify-center text-primary-400 font-semibold text-xs border border-primary-500/20">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <span class="text-sm font-medium text-surface-200">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-surface-400">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-medium {{ $user->role === 'admin' ? 'bg-primary-500/15 text-primary-400' : 'bg-surface-800 text-surface-400' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-surface-500 hidden sm:table-cell">{{ $user->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-right">
                            @if($user->id !== auth()->id())
                                <form id="delete-user-{{ $user->id }}" method="POST" action="{{ route('users.destroy', $user) }}">@csrf @method('DELETE')</form>
                                <button onclick="confirmDelete('delete-user-{{ $user->id }}', 'user')" class="p-2 rounded-lg text-surface-400 hover:text-danger-400 hover:bg-danger-500/10 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            @else
                                <span class="text-xs text-surface-500">You</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-6 py-4 border-t border-surface-800/50">{{ $users->links() }}</div>
    </div>
</div>
@endsection
