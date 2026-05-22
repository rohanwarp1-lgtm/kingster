<ul class="list-inline font-size-20 contact-links mb-0">
    @if($item->is_deleted == 0)
        <li class="list-inline-item px-2 pos-middle">
            <a href="javascript:void(0);" onclick="productDelete({{ $item->id }})">
                <i class="fe fe-trash"></i>
            </a>
        </li>
    @else
        <li class="list-inline-item px-2 pos-middle">
            <a href="javascript:void(0);" onclick="productRestore({{ $item->id }})">
                <i class="fe fe-rotate-ccw"></i>
            </a>
        </li>
    @endif
    <li class="list-inline-item px-2 pos-middle">
        <a href="{{ url('admin/edit-product?id=' . $item->id) }}">
            <i class="fe fe-edit"></i>
        </a>
    </li>
</ul>
