<ul class="list-inline font-size-20 contact-links mb-0">
    @if($item->is_deleted == 0)
        <li class="list-inline-item px-1">
            <a href="javascript:void(0);" title="Delete" onclick="warrantyDelete({{ $item->id }})">
                <i class="fe fe-trash text-danger"></i>
            </a>
        </li>
    @else
        <li class="list-inline-item px-1">
            <a href="javascript:void(0);" title="Restore" onclick="warrantyRestore({{ $item->id }})">
                <i class="fe fe-rotate-ccw text-success"></i>
            </a>
        </li>
    @endif
    <li class="list-inline-item px-1">
        <a href="javascript:void(0);" title="Change Status"
           onclick="changeWarrantyStatus({{ $item->id }}, '{{ e($item->warranty_status) }}')">
            <i class="fe fe-sliders text-primary"></i>
        </a>
    </li>
</ul>
