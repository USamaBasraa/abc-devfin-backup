<div class="mb-3">
    <label>BH Category ID</label>
    <input type="number" name="bh_cat_id" class="form-control"
           value="{{ old('bh_cat_id', $item->bh_cat_id ?? '') }}" required>
</div>

<div class="mb-3">
    <label>Item Title</label>
    <input type="text" name="item_title" class="form-control"
           value="{{ old('item_title', $item->item_title ?? '') }}" required>
</div>

<div class="mb-3">
    <label>Item Type</label>
    <select name="item_type" class="form-control" required>
        @foreach(['EQUIPMENT','CONSUMABLE','SERVICE'] as $type)
            <option value="{{ $type }}"
                @selected(old('item_type', $item->item_type ?? '') == $type)>
                {{ $type }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label>Status</label>
    <select name="status" class="form-control" required>
        @foreach(['ACTIVE','INACTIVE'] as $st)
            <option value="{{ $st }}"
                @selected(old('status', $item->status ?? '') == $st)>
                {{ $st }}
            </option>
        @endforeach
    </select>
</div>
