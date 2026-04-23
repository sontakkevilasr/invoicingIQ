@extends('layouts.app')
@section('title','Settings')
@section('content')
<div class="page" style="max-width:760px;">
    <div class="page-head"><div class="page-title">Settings</div><div class="page-subtitle">Company profile &amp; billing preferences</div></div>
    <form method="POST" action="{{ route('settings.update') }}">@csrf
        <div class="card" style="margin-bottom:16px;"><div class="card-header"><div class="card-title">Company Details</div></div><div class="card-body"><div style="display:grid;grid-template-columns:1fr 1fr;gap:0 16px;">
            @foreach([['company_name','Company Name','text'],['company_gstin','GSTIN','text'],['company_pan','PAN','text'],['company_address','Address','text'],['company_city','City','text'],['company_state','State','text'],['company_state_code','State Code','text'],['company_phone','Phone','tel'],['company_email','Email','email']] as [$k,$l,$t])
            <div class="form-group"><label class="form-label">{{ $l }}</label><input type="{{ $t }}" name="{{ $k }}" class="form-control" value="{{ old($k,$settings[$k]??'') }}"></div>
            @endforeach
        </div></div></div>
        <div class="card" style="margin-bottom:16px;"><div class="card-header"><div class="card-title">Bank Details</div></div><div class="card-body"><div style="display:grid;grid-template-columns:1fr 1fr;gap:0 16px;">
            @foreach([['bank_name','Bank Name'],['bank_acc_no','Account Number'],['bank_ifsc','IFSC Code'],['bank_branch','Branch']] as [$k,$l])
            <div class="form-group"><label class="form-label">{{ $l }}</label><input type="text" name="{{ $k }}" class="form-control" value="{{ old($k,$settings[$k]??'') }}"></div>
            @endforeach
        </div></div></div>
        <div class="card" style="margin-bottom:16px;"><div class="card-header"><div class="card-title">Invoice Settings</div></div><div class="card-body"><div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:0 16px;">
            @foreach([['invoice_prefix','Invoice Prefix','text'],['invoice_seq','Next Sequence No.','number'],['default_terms','Payment Terms (days)','number']] as [$k,$l,$t])
            <div class="form-group"><label class="form-label">{{ $l }}</label><input type="{{ $t }}" name="{{ $k }}" class="form-control" value="{{ old($k,$settings[$k]??'') }}"></div>
            @endforeach
        </div></div></div>
        <div class="card" style="margin-bottom:24px;"><div class="card-header"><div class="card-title">Default Invoice Notes</div></div><div class="card-body">
            <textarea name="default_notes" class="form-control" rows="3">{{ old('default_notes',$settings['default_notes']??'') }}</textarea>
        </div></div>
        <button type="submit" class="btn btn-primary" style="padding:10px 28px;">Save Settings</button>
    </form>
</div>
@endsection
