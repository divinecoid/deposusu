@extends('layouts.admin')

@section('header', 'Access Control Matrix (ACM)')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Breadcrumb & Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Access Control Matrix (ACM)</h1>
        <p class="text-sm text-slate-500 mt-1">Configure feature-level CRUD permissions for each user role in the system.</p>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <form action="{{ route('admin.acm.update') }}" method="POST">
        @csrf
        
        <!-- Action Card -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-6">
            <div class="p-6 border-b border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Permissions Matrix</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Toggle the checkboxes below to assign/retract permissions. Don't forget to save changes.</p>
                </div>
                <div class="flex gap-3 w-full sm:w-auto">
                    <button type="button" onclick="toggleAllCheckboxes(true)" class="flex-1 sm:flex-none text-xs font-semibold px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition">
                        Select All
                    </button>
                    <button type="button" onclick="toggleAllCheckboxes(false)" class="flex-1 sm:flex-none text-xs font-semibold px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition">
                        Deselect All
                    </button>
                    <button type="submit" class="flex-1 sm:flex-none text-xs font-semibold px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-sm transition">
                        Save Changes
                    </button>
                </div>
            </div>

            <!-- Responsive Table Container -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-800 text-white text-xs font-bold uppercase tracking-wider">
                            <th class="py-4 px-6 border-b border-slate-700">Feature</th>
                            <th class="py-4 px-4 border-b border-slate-700">Action / CRUD</th>
                            @foreach($roles as $role)
                                <th class="py-4 px-4 border-b border-slate-700 text-center min-w-[120px]">
                                    <span class="block font-bold">{{ $role->name }}</span>
                                    <button type="button" onclick="toggleColumnCheckboxes({{ $role->id }}, this)" data-state="true" class="text-[10px] text-slate-300 hover:text-white font-normal underline mt-1 block mx-auto">
                                        Toggle All
                                    </button>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @foreach($features as $featureKey => $featureName)
                            @foreach($actions as $actionKey => $actionName)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <!-- Row spanning feature name for the first action -->
                                    @if($loop->first)
                                        <td rowspan="{{ count($actions) }}" class="py-4 px-6 font-semibold text-slate-700 bg-slate-50/30 align-middle border-r border-slate-100 min-w-[200px]">
                                            <div class="flex items-center gap-2">
                                                <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                                {{ $featureName }}
                                            </div>
                                            <span class="text-[10px] text-slate-400 block mt-1">Slug: {{ $featureKey }}</span>
                                            
                                            <!-- Row Level Select All -->
                                            <button type="button" onclick="toggleRowCheckboxes('{{ $featureKey }}', this)" data-state="true" class="text-[10px] text-slate-500 hover:text-blue-600 font-semibold underline mt-2 block">
                                                Toggle Row
                                            </button>
                                        </td>
                                    @endif

                                    <td class="py-3 px-4 font-medium text-slate-600 border-r border-slate-100">
                                        @php
                                            $badgeColor = match($actionKey) {
                                                'create' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                'read' => 'bg-blue-50 text-blue-700 border-blue-200',
                                                'update' => 'bg-amber-50 text-amber-700 border-amber-200',
                                                'delete' => 'bg-rose-50 text-rose-700 border-rose-200',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold border {{ $badgeColor }}">
                                            {{ $actionName }}
                                        </span>
                                    </td>

                                    @foreach($roles as $role)
                                        <td class="py-3 px-4 text-center align-middle border-r border-slate-100 last:border-r-0">
                                            @if($role->name === 'Super Admin')
                                                <!-- Super Admin is read-only by default to prevent lockout -->
                                                <input type="hidden" name="permissions[{{ $role->id }}][{{ $featureKey }}][{{ $actionKey }}]" value="1">
                                                <input type="checkbox" checked disabled 
                                                       class="w-4 h-4 text-blue-600 bg-slate-100 border-slate-300 rounded focus:ring-blue-500 cursor-not-allowed">
                                            @else
                                                <input type="checkbox" 
                                                       name="permissions[{{ $role->id }}][{{ $featureKey }}][{{ $actionKey }}]" 
                                                       value="1"
                                                       data-role-id="{{ $role->id }}"
                                                       data-feature="{{ $featureKey }}"
                                                       data-action="{{ $actionKey }}"
                                                       {{ isset($matrix[$featureKey][$actionKey][$role->id]) && $matrix[$featureKey][$actionKey][$role->id] ? 'checked' : '' }}
                                                       class="permission-checkbox w-4 h-4 text-blue-600 bg-white border-slate-300 rounded focus:ring-blue-500 cursor-pointer hover:border-blue-400">
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Footer Save Bar -->
            <div class="p-6 border-t border-slate-200 bg-slate-50/50 flex justify-end">
                <button type="submit" class="w-full sm:w-auto text-sm font-semibold px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-sm transition">
                    Save Permission Matrix
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    // Toggle all checkboxes in the entire matrix (except Super Admin)
    function toggleAllCheckboxes(state) {
        document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
            checkbox.checked = state;
        });
    }

    // Toggle all checkboxes in a specific role column
    function toggleColumnCheckboxes(roleId, button) {
        const state = button.getAttribute('data-state') === 'true';
        document.querySelectorAll(`.permission-checkbox[data-role-id="${roleId}"]`).forEach(checkbox => {
            checkbox.checked = state;
        });
        button.setAttribute('data-state', !state);
    }

    // Toggle all checkboxes in a specific feature row
    function toggleRowCheckboxes(featureKey, button) {
        const state = button.getAttribute('data-state') === 'true';
        document.querySelectorAll(`.permission-checkbox[data-feature="${featureKey}"]`).forEach(checkbox => {
            checkbox.checked = state;
        });
        button.setAttribute('data-state', !state);
    }
</script>
@endsection
