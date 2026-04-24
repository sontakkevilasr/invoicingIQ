@extends('layouts.app')
@section('title','Customers')
@section('content')
<div class="page">

    {{-- Header --}}
    <div class="page-head flex justify-between items-center">
        <div>
            <div class="page-title">Customers</div>
            <div class="page-subtitle">{{ $stats['total'] }} customers &middot; {{ $stats['states'] }} {{ $stats['states'] == 1 ? 'state' : 'states' }} covered</div>
        </div>
        <button class="btn btn-primary" onclick="openM()">+ Add Customer</button>
    </div>

    {{-- Stat cards --}}
    <div class="stat-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:22px;">
        <div class="stat-card">
            <div class="stat-label">Total Customers</div>
            <div class="stat-value">{{ $stats['total'] }}</div>
        </div>
        <div class="stat-card acc">
            <div class="stat-label">GST Registered</div>
            <div class="stat-value">{{ $stats['with_gstin'] }}</div>
        </div>
        <div class="stat-card ok">
            <div class="stat-label">With Email</div>
            <div class="stat-value">{{ $stats['with_email'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">States Covered</div>
            <div class="stat-value">{{ $stats['states'] }}</div>
        </div>
    </div>

    {{-- Search toolbar --}}
    <form method="GET" class="toolbar">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search name, GSTIN…" class="form-control" style="max-width:340px;">
        <button class="btn btn-sm">Search</button>
        @if(request('q'))
            <a href="{{ route('customers.index') }}" class="btn btn-sm btn-ghost">Clear</a>
            <span style="font-size:12px;color:var(--t4);">{{ $customers->total() }} result{{ $customers->total() != 1 ? 's' : '' }} for "{{ request('q') }}"</span>
        @endif
    </form>

    {{-- Table card --}}
    <div class="card">
        <div class="table-wrap">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>GSTIN</th>
                        <th>City / State</th>
                        <th>PIN</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Billing Address</th>
                        <th style="text-align:center;">Terms</th>
                        <th class="c">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($customers as $c)
                    <tr>
                        <td style="font-weight:600;color:var(--t1);">{{ $c->name }}</td>
                        <td>
                            @if($c->gstin)
                                <span class="badge badge-blue" style="font-family:monospace;letter-spacing:.3px;">{{ $c->gstin }}</span>
                            @else
                                <span style="color:var(--t4);">—</span>
                            @endif
                        </td>
                        <td style="color:var(--t2);">
                            @if($c->billing_city || $c->billing_state)
                                {{ $c->billing_city }}{{ $c->billing_city && $c->billing_state ? ', ' : '' }}{{ $c->billing_state }}
                            @else
                                <span style="color:var(--t4);">—</span>
                            @endif
                        </td>
                        <td style="color:var(--t4);font-size:11px;font-family:monospace;">{{ $c->billing_pincode ?: '—' }}</td>
                        <td style="color:var(--t3);">{{ $c->phone ?: '—' }}</td>
                        <td style="color:var(--t3);">{{ $c->email ?: '—' }}</td>
                        <td style="color:var(--t4);font-size:11px;max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="{{ $c->billing_address }}">{{ $c->billing_address ?: '—' }}</td>
                        <td style="text-align:center;">
                            <span class="badge badge-gray">{{ $c->payment_terms }}d</span>
                        </td>
                        <td class="c">
                            <div style="display:flex;gap:6px;justify-content:center;">
                                <button class="btn btn-xs" onclick='openE(@json($c))'>Edit</button>
                                <form method="POST" action="{{ route('customers.destroy',$c) }}" onsubmit="return confirm('Delete customer?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-xs btn-danger">Del</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align:center;padding:60px;color:var(--t4);">
                            <div style="font-size:32px;margin-bottom:10px;">👤</div>
                            <div style="font-size:14px;font-weight:500;color:var(--t3);margin-bottom:6px;">No customers yet</div>
                            <div style="font-size:12px;">Click <strong>+ Add Customer</strong> to get started.</div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($customers->hasPages())
        <div style="padding:12px 16px;border-top:1px solid var(--bdr);">
            {{ $customers->links('partials.pagination') }}
        </div>
        @endif
    </div>

</div>

{{-- Add / Edit Modal --}}
<div id="cModal" class="modal-overlay" style="display:none;">
    <div class="modal-box">
        <div class="modal-head">
            <div class="modal-title" id="mTitle">Add Customer</div>
            <button class="modal-close" onclick="closeM()">×</button>
        </div>
        <form id="cForm" method="POST" action="{{ route('customers.store') }}">
            @csrf
            <input type="hidden" id="fM" name="_method" value="">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Name <span class="req">*</span></label>
                    <input type="text" name="name" id="fn" class="form-control" required placeholder="Customer / company name">
                </div>
                <div class="form-row form-row-2">
                    <div class="form-group">
                        <label class="form-label">GSTIN</label>
                        <input name="gstin" id="fg" class="form-control" placeholder="27AABCT1234R1ZX" style="font-family:monospace;">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Phone</label>
                        <input name="phone" id="fp" class="form-control" placeholder="98XXXXXXXX">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" id="fe" class="form-control" placeholder="billing@company.com">
                </div>
                <div class="form-group">
                    <label class="form-label">Billing Address</label>
                    <input name="billing_address" id="fa" class="form-control" placeholder="Street / area">
                </div>
                <div class="form-row form-row-3">
                    <div class="form-group">
                        <label class="form-label">City</label>
                        <input name="billing_city" id="fc" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">State</label>
                        <input name="billing_state" id="fs" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">State Code</label>
                        <input name="billing_state_code" id="fsc" class="form-control" placeholder="27">
                    </div>
                </div>
                <div class="form-row form-row-2">
                    <div class="form-group">
                        <label class="form-label">PIN Code</label>
                        <input name="billing_pincode" id="fpin" class="form-control" placeholder="400001">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Payment Terms (days)</label>
                        <input type="number" name="payment_terms" id="ft" class="form-control" value="30" min="0">
                    </div>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn" onclick="closeM()">Cancel</button>
                <button class="btn btn-primary">Save Customer</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function closeM() { document.getElementById('cModal').style.display = 'none'; }
function openM() {
    document.getElementById('cModal').style.display = 'flex';
    document.getElementById('mTitle').textContent = 'Add Customer';
    document.getElementById('cForm').action = '{{ route('customers.store') }}';
    document.getElementById('fM').value = '';
    ['fn','fg','fp','fe','fa','fc','fs','fsc','fpin'].forEach(id => {
        const e = document.getElementById(id);
        if (e) e.value = '';
    });
    document.getElementById('ft').value = '30';
}
function openE(c) {
    document.getElementById('cModal').style.display = 'flex';
    document.getElementById('mTitle').textContent = 'Edit Customer';
    document.getElementById('cForm').action = '/customers/' + c.id;
    document.getElementById('fM').value = 'PUT';
    const m = { name:'fn', gstin:'fg', phone:'fp', email:'fe', billing_address:'fa', billing_city:'fc', billing_state:'fs', billing_state_code:'fsc', billing_pincode:'fpin', payment_terms:'ft' };
    Object.entries(m).forEach(([k, id]) => {
        const e = document.getElementById(id);
        if (e) e.value = c[k] || '';
    });
}
document.getElementById('cModal').addEventListener('click', e => {
    if (e.target === e.currentTarget) closeM();
});
</script>
@endpush
@endsection
