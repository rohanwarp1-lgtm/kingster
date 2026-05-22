<ul class="list-inline font-size-20 contact-links mb-0">
    @php $isCeoAccount = ($item->role === 'CEO'); @endphp
    @if($item->is_deleted == 0)
        @if(!$isCeoAccount)
            @if($item->id != auth()->id())
                <li class="list-inline-item px-2 pos-middle">
                    <a href="javascript:void(0);" onclick="userDelete({{ $item->id }})">
                        <i class="fe fe-trash"></i>
                    </a>
                </li>
            @endif
            <li class="list-inline-item px-2 pos-middle">
                <a href="javascript:void(0);" onclick="editUserCredentials({{ $item->id }})">
                    <i class="fe fe-edit"></i>
                </a>
            </li>
        @elseif(auth()->user()->role === 'CEO')
            <li class="list-inline-item px-2 pos-middle">
                <a href="javascript:void(0);" onclick="editUserCredentials({{ $item->id }})">
                    <i class="fe fe-edit"></i>
                </a>
            </li>
        @endif
    @else
        @if(!$isCeoAccount)
            <li class="list-inline-item px-2 pos-middle">
                <a href="javascript:void(0);" onclick="userRestore({{ $item->id }})">
                    <i class="fe fe-rotate-ccw"></i>
                </a>
            </li>
        @endif
    @endif
</ul>
