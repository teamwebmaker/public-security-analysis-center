<td class="text-end">
    <div class="dropdown dropstart">
        <button class="btn btn-sm btn-light border-0" data-bs-toggle="dropdown">
            <i class="bi bi-three-dots-vertical"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li>
                <a href="{{ route($resourceName . '.edit', $model) }}"
                    class="dropdown-item text-primary d-flex align-items-center justify-center gap-2">
                    <i class="bi bi-pencil-square"></i><span>რედაქტირება</span>
                </a>
            </li>

            @if ($action_delete == true)
                <li class="m-0">
                    <form class="m-0" method="POST" action="{{ route($resourceName . '.destroy', $model) }}"
                        onsubmit="return confirm('{{ $deleteMessage }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="dropdown-item text-danger d-flex align-items-center justify-center gap-2">
                            <i class="bi bi-trash"></i><span>წაშლა</span>
                        </button>
                    </form>
                </li>
            @endif
        </ul>
    </div>
</td>
