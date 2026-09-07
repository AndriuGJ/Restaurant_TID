<div class="flex flex-col gap-4">
    <a href="{{ route('dashboard') }}"
        class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm font-medium text-slate-300 transition hover:bg-slate-800 hover:text-white {{ request()->routeIs('dashboard') ? 'bg-brand-500/15 text-brand-400' : '' }}">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12 19.5 2.25M3 10.5h18v10.5H3V10.5Z" />
        </svg>
        Dashboard
    </a>

    @php
        $menu = [
            [
                'label' => 'Configuración',
                'permission' => 'configuracion-ver',
                'groups' => [
                    [
                        'label' => 'Restaurante',
                        'children' => [
                            ['label' => 'Salones', 'route' => 'restaurant.halls.index'],
                            ['label' => 'Mesas', 'route' => 'restaurant.tables.index'],
                            ['label' => 'Turnos', 'route' => 'restaurant.shifts.index'],
                            ['label' => 'Cajas', 'route' => 'restaurant.cash-registers.index'],
                            ['label' => 'Delivery', 'route' => 'restaurant.delivery-providers.index'],
                            ['label' => 'Impresoras', 'route' => 'restaurant.printers.index'],
                        ],
                    ],
                    [
                        'label' => 'Sistema',
                        'children' => [
                            ['label' => 'Tipos de documento', 'route' => 'configuration.document-types.index'],
                            ['label' => 'Medios de pago', 'route' => 'configuration.payment-methods.index'],
                            ['label' => 'Config. Empresa', 'route' => 'configuration.companies.index'],
                            ['label' => 'Config. SUNAT', 'route' => 'configuration.sunat-configs.index'],
                        ],
                    ],
                ],
            ],
            [
                'label' => 'Cajas',
                'permission' => 'cajas-ver',
                'groups' => [
                    [
                        'label' => 'Sesiones',
                        'children' => [
                            ['label' => 'Apertura / Cierre', 'route' => 'cash-registers.sessions.index'],
                        ],
                    ],
                ],
            ],
            [
                'label' => 'Clientes',
                'permission' => 'clientes-ver',
                'groups' => [
                    [
                        'label' => 'Catálogo',
                        'children' => [
                            ['label' => 'Clientes', 'route' => 'customers.index'],
                            ['label' => 'Empresas', 'route' => 'customers.companies.index'],
                        ],
                    ],
                ],
            ],
            [
                'label' => 'Inventario',
                'permission' => 'inventario-ver',
                'groups' => [
                    [
                        'label' => 'Catálogo',
                        'children' => [
                            ['label' => 'Proveedores', 'route' => 'inventory.suppliers.index'],
                            ['label' => 'Categorías de compra', 'route' => 'inventory.purchase-categories.index'],
                            ['label' => 'Categorías de producto', 'route' => 'inventory.product-categories.index'],
                        ],
                    ],
                    [
                        'label' => 'Productos',
                        'children' => [
                            ['label' => 'Productos', 'route' => 'inventory.products.index'],
                        ],
                    ],
                    [
                        'label' => 'Compras',
                        'children' => [
                            ['label' => 'Compras', 'route' => 'inventory.purchases.index'],
                            ['label' => 'Kardex', 'route' => 'inventory.kardex.index'],
                        ],
                    ],
                ],
            ],
            [
                'label' => 'Reportes',
                'permission' => 'reportes-ver',
                'groups' => [
                    [
                        'label' => 'Reportes',
                        'children' => [
                            ['label' => 'Ventas', 'route' => 'reportes.ventas'],
                            ['label' => 'Cajas', 'route' => 'reportes.cajas', 'permission' => 'reportes-cajas-ver'],
                        ],
                    ],
                ],
            ],
            [
                'label' => 'Usuarios',
                'permission' => 'usuarios-ver',
                'groups' => [
                    [
                        'label' => 'Usuarios',
                        'children' => [
                            ['label' => 'Usuarios', 'route' => 'users.index'],
                            ['label' => 'Roles y permisos', 'route' => 'users.roles.index', 'permission' => 'roles-gestionar'],
                        ],
                    ],
                ],
            ],
            [
                'label' => 'Punto de Venta',
                'permission' => 'pos-ver',
                'groups' => [
                    [
                        'label' => 'Nueva venta',
                        'children' => [
                            ['label' => 'Delivery', 'route' => 'pos.new.delivery'],
                            ['label' => 'Venta rápida', 'route' => 'pos.new.quick-sale'],
                        ],
                    ],
                    [
                        'label' => 'Salón',
                        'children' => [
                            ['label' => 'Salón', 'route' => 'pos.hall'],
                        ],
                    ],
                    [
                        'label' => 'Cocina',
                        'children' => [
                            ['label' => 'Preparación', 'route' => 'pos.kitchen.index'],
                        ],
                    ],
                ],
            ],
        ];
    @endphp

    @foreach ($menu as $section)
        @can($section['permission'])
            <nav class="flex flex-col gap-1">
                <p class="px-2 text-xs font-semibold uppercase tracking-widest text-slate-500">
                    {{ $section['label'] }}
                </p>

                @foreach ($section['groups'] as $group)
                    <details class="group">
                        <summary
                            class="flex cursor-pointer select-none items-center justify-between rounded-lg px-3 py-2 text-sm font-medium text-slate-300 transition hover:bg-slate-800 hover:text-white">
                            <span>{{ $group['label'] }}</span>
                            <svg class="h-4 w-4 text-slate-400 transition-transform group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7" />
                            </svg>
                        </summary>
                        <div class="mt-1 flex flex-col gap-0.5 border-l border-slate-700 pl-3">
                            @foreach ($group['children'] as $item)
                                @php
                                    $active = request()->routeIs($item['route']);
                                    $allowed = isset($item['permission']) ? auth()->user()->can($item['permission']) : true;
                                @endphp
                                @if ($allowed)
                                    <a href="{{ route($item['route']) }}"
                                        class="rounded-lg px-3 py-1.5 text-sm font-medium transition {{ $active ? 'bg-brand-500/15 font-semibold text-brand-400' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                                        {{ $item['label'] }}
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </details>
                @endforeach
            </nav>
        @endcan
    @endforeach
</div>
