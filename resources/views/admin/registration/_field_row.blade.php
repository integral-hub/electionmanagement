<div class="field-card" data-index="{{ $i }}" style="border:1px solid var(--border);border-radius:10px;padding:16px;margin-bottom:14px;background:var(--surface);">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
        <span style="font-size:13px;font-weight:600;color:var(--ink-3);">
            Field #{{ $i + 1 }}
        </span>

        <button type="button"
                onclick="removeField(this)"
                style="background:none;border:none;cursor:pointer;color:var(--danger);padding:4px;">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <div class="form-group">
            <label class="form-label">Label</label>
            <input type="text"
                   name="fields[{{ $i }}][label]"
                   class="form-input"
                   value="{{ $field['label'] ?? '' }}"
                   required>
        </div>

        <div class="form-group">
            <label class="form-label">Field Name</label>
            <input type="text"
                   name="fields[{{ $i }}][field_name]"
                   class="form-input"
                   value="{{ $field['field_name'] ?? '' }}"
                   required>
        </div>

        <div class="form-group">
            <label class="form-label">Type</label>
            <select name="fields[{{ $i }}][field_type]"
                    class="form-select"
                    onchange="toggleOptions(this)">
                @foreach(['text','textarea','select','checkbox','radio','date','file'] as $type)
                    <option value="{{ $type }}"
                        {{ ($field['field_type'] ?? '') === $type ? 'selected' : '' }}>
                        {{ ucfirst($type) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Description</label>
            <input type="text"
                   name="fields[{{ $i }}][description]"
                   class="form-input"
                   value="{{ $field['description'] ?? '' }}">
        </div>
    </div>
    <div class="form-group max-input-group"
     style="{{ in_array($field['field_type'] ?? '', ['text', 'textarea']) ? 'display:block' : 'display:none' }}">
        <label class="form-label">Maximum Length</label>
        <input type="number"
               min="1"
               name="fields[{{ $i }}][max_input]"
               class="form-input"
               value="{{ $field['max_input'] ?? '' }}"
               placeholder="e.g. 15">
    </div>

    <div class="options-row"
         style="{{ in_array($field['field_type'] ?? '', ['select','radio','checkbox']) ? 'display:block' : 'display:none' }};margin-top:10px;">

        <div class="form-group">
            <label class="form-label">
                Options
                <span class="form-hint">(comma-separated)</span>
            </label>

            <input type="text"
                   name="fields[{{ $i }}][options]"
                   class="form-input"
                   placeholder="Option 1, Option 2, Option 3"
                   value="{{ is_array($field['options'] ?? null) ? implode(', ', $field['options']) : '' }}">
        </div>
    </div>

    <div style="display:flex;gap:20px;margin-top:12px;flex-wrap:wrap;">
        <label style="display:flex;align-items:center;gap:6px;">
            <input type="hidden" name="fields[{{ $i }}][required]" value="0">
            <input type="checkbox"
                   name="fields[{{ $i }}][required]"
                   value="1"
                   {{ ($field['required'] ?? false) ? 'checked' : '' }}>
            Required
        </label>
        <div class="unique-row">
        <label style="display:flex;align-items:center;gap:6px;">
            <input type="hidden" name="fields[{{ $i }}][unique_field]" value="0">
            <input type="checkbox"
                   name="fields[{{ $i }}][unique_field]"
                   value="1"
                   {{ ($field['unique_field'] ?? false) ? 'checked' : '' }}>
            Unique
        </label>
        </div>
    </div>
</div>