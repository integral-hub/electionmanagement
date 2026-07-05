@extends('layouts.admin')
@section('page-title', 'Registration Form Builder')

@section('topbar-actions')
    <a href="{{ route('admin.elections.show', $election) }}" class="btn btn-secondary btn-sm">← Back</a>
@endsection

@section('content')
<div style="max-width:800px;">
    <div class="card">
        <div style="margin-bottom:24px;">
            <h2 style="font-size:18px;font-weight:700;">Voter Registration Form</h2>
            <p style="font-size:14px;color:var(--ink-3);margin-top:4px;">Define the fields voters must fill when registering for <strong>{{ $election->name }}</strong>.</p>
        
            {{-- Info Callout --}}
        <div style="margin-top:12px;padding:12px 14px;border:1px solid var(--border);border-radius:8px;background:var(--surface);font-size:13px;color:var(--ink-3);line-height:1.4;">
            <strong style="color:var(--danger);">IMPORTANT:</strong>
            <strong>Email</strong>, <strong>Phone number</strong>, and <strong>Password</strong> fields already exist by default in this system, you do NOT need to add them.

            You should only add additional fields such as <strong>full name</strong>, <strong>gender</strong>, <strong>address</strong>, <strong>ID number</strong>, etc.

            You can also add <strong>file upload fields</strong> (e.g. passport photo, documents, or ID uploads) using the <strong>File Upload</strong> type.
        </div>
        </div>

        <form action="{{ $form ? route('admin.elections.registration.update', $election) : route('admin.elections.registration.store', $election) }}" method="POST" id="rfForm">
            @csrf
            @if($form) @method('PUT') @endif

            <div id="fields-container">
                @if($form && !empty($form->fields))
                    @foreach($form->fields as $i => $field)
                    <div class="field-card" data-index="{{ $i }}" style="border:1px solid var(--border);border-radius:10px;padding:16px;margin-bottom:14px;background:var(--surface);">
                        @include('admin.registration._field_row', ['i' => $i, 'field' => $field])
                    </div>
                    @endforeach
                @endif
            </div>

            <button type="button" onclick="addField()" class="btn btn-secondary" style="width:100%;justify-content:center;border-style:dashed;margin-bottom:20px;">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16"><path d="M12 5v14M5 12h14"/></svg>
                Add Field
            </button>

            <div style="display:flex;gap:10px;border-top:1px solid var(--border);padding-top:16px;">
                <button type="submit" class="btn btn-primary">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16"><path d="M5 13l4 4L19 7"/></svg>
                    {{ $form ? 'Update Form' : 'Save Form' }}
                </button>
        </form>
                @if($form)
                <form action="{{ route('admin.elections.registration.destroy', $election) }}" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Delete registration form?')">Delete Form</button>
                </form>
                @endif
            </div>
        </div>
    </div>

{{-- Field template --}}
<template id="field-template">
    <div class="field-card" style="border:1px solid var(--border);border-radius:10px;padding:16px;margin-bottom:14px;background:var(--surface);">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
            <span style="font-size:13px;font-weight:600;color:var(--ink-3);">Field #<span class="field-num"></span></span>
            <button type="button" onclick="removeField(this)" style="background:none;border:none;cursor:pointer;color:var(--danger);padding:4px;">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div class="form-group">
                <label class="form-label">Label</label>
                <input type="text" name="fields[IDX][label]" class="form-input" placeholder="e.g. Student ID" required>
            </div>
            <div class="form-group">
                <label class="form-label">Field Name <span class="form-hint">(no spaces)</span></label>
                <input type="text" name="fields[IDX][field_name]" class="form-input" placeholder="e.g. student_id" required>
            </div>
            <div class="form-group">
                <label class="form-label">Type</label>
                <select name="fields[IDX][field_type]" class="form-select" onchange="toggleOptions(this)">
                    <option value="text">Text</option>
                    <option value="textarea">Textarea</option>
                    <option value="select">Select</option>
                    <option value="checkbox">Checkbox</option>
                    <option value="radio">Radio</option>
                    <option value="date">Date</option>
                    <option value="file">File Upload</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <input type="text" name="fields[IDX][description]" class="form-input" placeholder="Optional helper text">
            </div>
            <div class="form-group max-input-group">
                <label class="form-label">Maximum Length</label>
                <input type="number" name="fields[IDX][max_input]" class="form-input" min="1" placeholder="e.g. 10">
            </div>
        </div>
        <div class="options-row" style="display:none;margin-top:10px;">
            <div class="form-group">
                <label class="form-label">Options <span class="form-hint">(comma-separated)</span></label>
                <input type="text" name="fields[IDX][options]" class="form-input" placeholder="Option 1, Option 2, Option 3">
            </div>
        </div>
        <div style="display:flex;gap:20px;margin-top:12px;flex-wrap:wrap;">
            <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer;">
                <input type="hidden" name="fields[IDX][required]" value="0">
                <input type="checkbox" name="fields[IDX][required]" value="1" style="accent-color:var(--accent);"> Required
            </label>
            <div class="unique-row">
            <label style="display:flex;align-items:center;gap:6px;">
                <input type="hidden" name="fields[IDX][unique_field]" value="0">
                <input type="checkbox" name="fields[IDX][unique_field]" value="1">
            Unique
            </label>
            </div>
        </div>
    </div>
</template>

<script>

document.getElementById('rfForm').addEventListener('submit', function (e) {

    const fields = document.querySelectorAll('#fields-container .field-card');

    if (fields.length === 0) {
        e.preventDefault();
        alert('Please add at least one field before saving.');
        return;
    }

});

let fieldCount = {{ $form ? count($form->fields ?? []) : 0 }};

function addField() {
    const tpl = document.getElementById('field-template').innerHTML;
    const html = tpl.replace(/IDX/g, fieldCount).replace('field-num"></span>', `field-num"></span>${fieldCount + 1}`);
    const wrap = document.createElement('div');
    wrap.innerHTML = html;
    document.getElementById('fields-container').appendChild(wrap.firstElementChild);
    fieldCount++;
}

function removeField(btn) {
    btn.closest('.field-card').remove();
}

function toggleOptions(select) {
    const card = select.closest('.field-card');
    //options field
    const row = card.querySelector('.options-row');
    if (row) {
        row.style.display = ['select','radio','checkbox'].includes(select.value)
            ? 'block'
            : 'none';
    }
    // max-input field + Unique field
    const maxInput = card.querySelector('.max-input-group');
    const uniqueRow = card.querySelector('.unique-row');

    if (maxInput) {
        maxInput.style.display =
            ['text', 'textarea'].includes(select.value)
                    ? 'block'
                    : 'none';
    }

    if (uniqueRow) {
        uniqueRow.style.display =
            ['text', 'textarea'].includes(select.value)
                ? 'flex'
                : 'none';
    }

}

// Restore options display on page load for existing fields
document.querySelectorAll('select[name*="field_type"]').forEach(toggleOptions);
</script>
@endsection
