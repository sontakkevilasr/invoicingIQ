@extends('layouts.app')
@section('title','Customers')
@section('content')
<div class="page">
    <div class="page-head flex justify-between items-center">
        <div><div class="page-title">Customers</div><div class="page-subtitle">{{ $customers->total() }} customers</div></div>
        <button class="btn btn-primary" onclick="openM()">+ Add Customer</button>
    </div>
    <form method="GET" class="toolbar"><input type="text" name="q" value="{{ request('q') }}" placeholder="Search name, GSTIN…" class="form-control" style="max-width:320px;"><button class="btn btn-sm">Search</button>@if(request('q'))<a href="{{ route('customers.index') }}" class="btn btn-sm btn-ghost">Clear</a>@endif</form>
    <div class="card"><div class="table-wrap"><table class="tbl"><thead><tr><th>Name</th><th>GSTIN</th><th>City / State</th><th>Phone</th><th>Email</th><th>Terms</th><th class="c">Actions</th></tr></thead><tbody>
    @forelse($customers as $c)
    <tr><td style="font-weight:500;">{{ $c->name }}</td><td style="font-size:11px;font-family:monospace;">{{ $c->gstin ?: '—' }}</td><td style="color:var(--t3);">{{ $c->billing_city }}{{ $c->billing_city&&$c->billing_state?', ':'' }}{{ $c->billing_state }}</td><td style="color:var(--t3);">{{ $c->phone ?: '—' }}</td><td style="color:var(--t3);">{{ $c->email ?: '—' }}</td><td style="color:var(--t3);">{{ $c->payment_terms }}d</td>
    <td class="c"><div style="display:flex;gap:5px;justify-content:center;"><button class="btn btn-xs" onclick='openE(@json($c))'>Edit</button><form method="POST" action="{{ route('customers.destroy',$c) }}" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="btn btn-xs btn-danger">Del</button></form></div></td></tr>
    @empty<tr><td colspan="7" style="text-align:center;padding:50px;color:var(--t4);">No customers yet.</td></tr>
    @endforelse
    </tbody></table></div><div style="padding:12px 16px;">{{ $customers->links('partials.pagination') }}</div></div>
</div>
<div id="cModal" class="modal-overlay" style="display:none;"><div class="modal-box">
    <div class="modal-head"><div class="modal-title" id="mTitle">Add Customer</div><button class="modal-close" onclick="closeM()">×</button></div>
    <form id="cForm" method="POST" action="{{ route('customers.store') }}">@csrf<input type="hidden" id="fM" name="_method" value="">
    <div class="modal-body">
        <div class="form-group"><label class="form-label">Name *</label><input type="text" name="name" id="fn" class="form-control" required></div>
        <div class="form-row form-row-2"><div class="form-group"><label class="form-label">GSTIN</label><input name="gstin" id="fg" class="form-control"></div><div class="form-group"><label class="form-label">Phone</label><input name="phone" id="fp" class="form-control"></div></div>
        <div class="form-group"><label class="form-label">Email</label><input type="email" name="email" id="fe" class="form-control"></div>
        <div class="form-group"><label class="form-label">Billing Address</label><input name="billing_address" id="fa" class="form-control"></div>
        <div class="form-row form-row-3"><div class="form-group"><label class="form-label">City</label><input name="billing_city" id="fc" class="form-control"></div><div class="form-group"><label class="form-label">State</label><input name="billing_state" id="fs" class="form-control"></div><div class="form-group"><label class="form-label">State Code</label><input name="billing_state_code" id="fsc" class="form-control" style="width:70px;" placeholder="27"></div></div>
        <div class="form-row form-row-2"><div class="form-group"><label class="form-label">PIN</label><input name="billing_pincode" id="fpin" class="form-control"></div><div class="form-group"><label class="form-label">Payment Terms (days)</label><input type="number" name="payment_terms" id="ft" class="form-control" value="30"></div></div>
    </div>
    <div class="modal-foot"><button type="button" class="btn" onclick="closeM()">Cancel</button><button class="btn btn-primary">Save</button></div></form>
</div></div>
@push('scripts')<script>
function closeM(){document.getElementById('cModal').style.display='none';}
function openM(){document.getElementById('cModal').style.display='flex';document.getElementById('mTitle').textContent='Add Customer';document.getElementById('cForm').action='{{ route('customers.store') }}';document.getElementById('fM').value='';['fn','fg','fp','fe','fa','fc','fs','fsc','fpin'].forEach(id=>{const e=document.getElementById(id);if(e)e.value='';});document.getElementById('ft').value='30';}
function openE(c){document.getElementById('cModal').style.display='flex';document.getElementById('mTitle').textContent='Edit Customer';document.getElementById('cForm').action='/customers/'+c.id;document.getElementById('fM').value='PUT';const m={name:'fn',gstin:'fg',phone:'fp',email:'fe',billing_address:'fa',billing_city:'fc',billing_state:'fs',billing_state_code:'fsc',billing_pincode:'fpin',payment_terms:'ft'};Object.entries(m).forEach(([k,id])=>{const e=document.getElementById(id);if(e)e.value=c[k]||'';});}
document.getElementById('cModal').addEventListener('click',e=>{if(e.target===e.currentTarget)closeM();});
</script>@endpush
@endsection
