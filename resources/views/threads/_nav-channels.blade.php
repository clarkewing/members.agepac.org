@if(Route::is('threads.search'))
    <ais-hierarchical-menu
        :attributes="[
            'thread.channel.lvl0',
            'thread.channel.lvl1',
        ]"
        class="d-none d-sm-block"
    >
        <template #default="{ items, refine, createURL }">
            <x-linklist title="Sections" spacing="0">
                <li v-for="(parent, index) in items">
                    <button class="btn btn-link p-0 font-weight-bold text-muted link-muted"
                            type="button"
                            @click.prevent="refine(parent.value)">
                        @{{ parent.label ? parent.label.toUpperCase() : 'GÉNÉRAL' }}

                        <span class="badge badge-pill badge-secondary align-middle ml-1"
                              v-text="parent.count"></span>
                    </button>

                    <ul :id="'subChannel_' + index"
                        class="list-unstyled pl-2"
                        v-if="parent.data">
                        <li v-for="channel in parent.data">
                            <a class="link-muted"
                               :href="createURL(channel.value)"
                               @click.prevent="refine(channel.value)">
                                @{{ channel.label }}

                                <span class="badge badge-pill badge-secondary align-middle ml-1"
                                      v-text="channel.count"></span>
                            </a>
                        </li>
                    </ul>
                </li>
            </x-linklist>
        </template>
    </ais-hierarchical-menu>

@else
    <x-linklist title="Sections" spacing="0" class="d-none d-sm-block">
        @foreach($channels->withPermission('view')->sortBy('parent.name')->groupBy('parent.name') as $parent => $channels)
            <li>
                <button class="btn btn-link p-0 font-weight-bold text-muted link-muted"
                        type="button"
                        data-toggle="collapse"
                        data-target="#subChannel_{{ $parent }}"
                        aria-expanded="{{ Route::is('threads.index') && Route::input('channel') && optional(Route::input('channel'))->parent == $parent ? 'true' : 'false' }}"
                        aria-controls="collapseExample">
                    {{ $parent ? strtoupper($parent) : 'GÉNÉRAL' }}
                </button>

                <ul id="subChannel_{{ $parent }}"
                    class="list-unstyled pl-2 collapse{{ Route::is('threads.index') && Route::input('channel') && optional(Route::input('channel'))->parent == $parent ? ' show' : '' }}">
                    @foreach($channels as $channel)
                        <li>
                            <a class="link-muted{{ Route::is('threads.index') && Route::input('channel') == $channel ? ' active' : '' }}"
                               href="{{ route('threads.index', $channel) }}">
                                {{ ucwords($channel->name) }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
        @endforeach
    </x-linklist>
@endif
